<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('upload_storage_path')) {
    /**
     * Resolve physical storage path for uploaded files.
     * In production this can point to an external CDN folder via UPLOAD_BASE_PATH.
     */
    function upload_storage_path($path = '')
    {
        $base = defined('UPLOAD_BASE_PATH') ? UPLOAD_BASE_PATH : FCPATH;
        $base = rtrim($base, '/\\') . DIRECTORY_SEPARATOR;
        $path = ltrim((string) $path, '/\\');
        return $base . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }
}

if (!function_exists('ensure_upload_directory')) {
    function ensure_upload_directory($dir)
    {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return is_dir($dir) && is_writable($dir);
    }
}

if (!function_exists('image_upload_limits')) {
    /**
     * Batas aman decoding gambar. Dipakai SEBELUM getimagesize/decode agar
     * gambar raksasa tidak menghabiskan memori (decompression bomb).
     */
    function image_upload_limits()
    {
        return [
            'max_side'   => 6000,          // piksel per sisi
            'max_pixels' => 40000000,      // 40 megapiksel total
            'max_frames' => 300,           // khusus GIF animasi
            'mimes'      => [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
                'image/webp' => 'webp',
            ],
        ];
    }
}

if (!function_exists('_image_validate_info')) {
    function _image_validate_info($info, $detected_mime = null, $allowed_mimes = null)
    {
        $limits = image_upload_limits();
        $allowed = $allowed_mimes ? array_values($allowed_mimes) : array_keys($limits['mimes']);

        if (!is_array($info) || empty($info[0]) || empty($info[1]) || empty($info['mime'])) {
            return ['ok' => false, 'error' => 'File bukan gambar yang valid.'];
        }

        $width  = (int) $info[0];
        $height = (int) $info[1];
        $mime   = strtolower((string) $info['mime']);

        if (!in_array($mime, $allowed, true) || !isset($limits['mimes'][$mime])) {
            return ['ok' => false, 'error' => 'Tipe gambar tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.'];
        }

        if ($detected_mime !== null && strtolower((string) $detected_mime) !== $mime) {
            return ['ok' => false, 'error' => 'Isi file tidak cocok dengan tipe gambarnya.'];
        }

        if ($width <= 0 || $height <= 0) {
            return ['ok' => false, 'error' => 'Dimensi gambar tidak valid.'];
        }

        if ($width > $limits['max_side'] || $height > $limits['max_side']) {
            return ['ok' => false, 'error' => 'Gambar terlalu besar. Maksimal ' . $limits['max_side'] . ' piksel per sisi.'];
        }

        if (($width * $height) > $limits['max_pixels']) {
            return ['ok' => false, 'error' => 'Resolusi gambar terlalu tinggi. Maksimal 40 megapiksel.'];
        }

        return [
            'ok'     => true,
            'error'  => '',
            'mime'   => $mime,
            'ext'    => $limits['mimes'][$mime],
            'width'  => $width,
            'height' => $height,
        ];
    }
}

if (!function_exists('validate_image_file')) {
    /**
     * Validasi gambar di disk: MIME asli, dimensi, dan total piksel.
     * Aman dipanggil pada file tmp sebelum dipindahkan / didecode.
     */
    function validate_image_file($sourcePath, $allowed_mimes = null)
    {
        if (!is_file($sourcePath) || !is_readable($sourcePath)) {
            return ['ok' => false, 'error' => 'File gambar tidak dapat dibaca.'];
        }

        $info = @getimagesize($sourcePath);
        $detected = null;
        if (class_exists('finfo')) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $detected = @$finfo->file($sourcePath);
        }

        $result = _image_validate_info($info, $detected, $allowed_mimes);
        if (isset($result['ok'])) {
            $result['info'] = $info;
        }
        return $result;
    }
}

if (!function_exists('validate_image_bytes')) {
    /**
     * Validasi gambar dari string biner (mis. avatar hasil download OAuth).
     */
    function validate_image_bytes($bytes, $allowed_mimes = null)
    {
        if (!is_string($bytes) || $bytes === '') {
            return ['ok' => false, 'error' => 'Data gambar kosong.'];
        }

        $info = @getimagesizefromstring($bytes);
        $detected = null;
        if (class_exists('finfo')) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $detected = @$finfo->buffer($bytes);
        }

        return _image_validate_info($info, $detected, $allowed_mimes);
    }
}

if (!function_exists('convert_image_file_to_webp')) {
    /**
     * Convert an image file on disk to WebP and remove the original on success.
     *
     * @param string $sourcePath Absolute path to the source image file.
     * @param int    $quality    WebP quality (0-100).
     * @return string|null       Absolute path to the new .webp file, or null on failure.
     */
    function convert_image_file_to_webp($sourcePath, $quality = 82)
    {
        if (!is_file($sourcePath) || !is_readable($sourcePath)) {
            return null;
        }

        // Defense-in-depth: tolak gambar kebesaran sebelum decode.
        $validation = validate_image_file($sourcePath);
        if (empty($validation['ok'])) {
            log_message('error', 'Upload gambar ditolak: ' . ($validation['error'] ?? 'tidak valid'));
            return null;
        }
        $mime = $validation['mime'];

        $dir = dirname($sourcePath);
        $base = pathinfo($sourcePath, PATHINFO_FILENAME);
        $destPath = $dir . DIRECTORY_SEPARATOR . $base . '.webp';

        // Already WebP: keep as-is (normalize name only if needed).
        if ($mime === 'image/webp') {
            if ($sourcePath !== $destPath) {
                if (@rename($sourcePath, $destPath)) {
                    return $destPath;
                }
                return $sourcePath;
            }
            return $sourcePath;
        }

        // Animated GIF: prefer Imagick to preserve animation.
        if ($mime === 'image/gif' && extension_loaded('imagick')) {
            $imagickResult = _convert_gif_to_webp_imagick($sourcePath, $destPath, $quality);
            if ($imagickResult !== null) {
                @unlink($sourcePath);
                return $imagickResult;
            }
            // fall through to GD (first frame) if Imagick path fails
        }

        $image = _image_create_from_mime($sourcePath, $mime);
        if (!$image) {
            return null;
        }

        // Preserve alpha for formats that may have it.
        if (in_array($mime, ['image/png', 'image/gif', 'image/webp', 'image/bmp'], true)) {
            if (function_exists('imagepalettetotruecolor')) {
                @imagepalettetotruecolor($image);
            }
            @imagealphablending($image, true);
            @imagesavealpha($image, true);
        }

        $ok = @imagewebp($image, $destPath, $quality);
        imagedestroy($image);

        if (!$ok || !is_file($destPath)) {
            @unlink($destPath);
            return null;
        }

        if ($destPath !== $sourcePath && is_file($sourcePath)) {
            @unlink($sourcePath);
        }

        return $destPath;
    }
}

if (!function_exists('_image_create_from_mime')) {
    function _image_create_from_mime($path, $mime)
    {
        switch ($mime) {
            case 'image/jpeg':
                $img = @imagecreatefromjpeg($path);
                if ($img && function_exists('exif_read_data')) {
                    $exif = @exif_read_data($path);
                    $orientation = isset($exif['Orientation']) ? (int) $exif['Orientation'] : 1;
                    if ($orientation > 1) {
                        $img = _apply_jpeg_orientation($img, $orientation);
                    }
                }
                return $img;
            case 'image/png':
                return @imagecreatefrompng($path);
            case 'image/gif':
                return @imagecreatefromgif($path);
            case 'image/webp':
                return function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null;
            case 'image/bmp':
                return function_exists('imagecreatefrombmp') ? @imagecreatefrombmp($path) : null;
            default:
                return null;
        }
    }
}

if (!function_exists('_apply_jpeg_orientation')) {
    function _apply_jpeg_orientation($img, $orientation)
    {
        switch ($orientation) {
            case 2: // mirror horizontal
                if (function_exists('imageflip')) {
                    @imageflip($img, IMG_FLIP_HORIZONTAL);
                }
                return $img;
            case 3:
                $rot = @imagerotate($img, 180, 0);
                if ($rot) { imagedestroy($img); return $rot; }
                return $img;
            case 4: // mirror vertical
                if (function_exists('imageflip')) {
                    @imageflip($img, IMG_FLIP_VERTICAL);
                }
                return $img;
            case 5: // mirror horizontal + rotate 270 CW
                if (function_exists('imageflip')) {
                    @imageflip($img, IMG_FLIP_HORIZONTAL);
                }
                $rot = @imagerotate($img, 270, 0);
                if ($rot) { imagedestroy($img); return $rot; }
                return $img;
            case 6:
                $rot = @imagerotate($img, -90, 0);
                if ($rot) { imagedestroy($img); return $rot; }
                return $img;
            case 7: // mirror horizontal + rotate 90 CW
                if (function_exists('imageflip')) {
                    @imageflip($img, IMG_FLIP_HORIZONTAL);
                }
                $rot = @imagerotate($img, -270, 0);
                if ($rot) { imagedestroy($img); return $rot; }
                return $img;
            case 8:
                $rot = @imagerotate($img, 90, 0);
                if ($rot) { imagedestroy($img); return $rot; }
                return $img;
            default:
                return $img;
        }
    }
}

if (!function_exists('_convert_gif_to_webp_imagick')) {
    function _convert_gif_to_webp_imagick($sourcePath, $destPath, $quality)
    {
        try {
            $im = new Imagick();
            $im->readImage($sourcePath);

            // Cegah GIF animasi dengan jumlah frame ekstrem (resource exhaustion).
            if ($im->getNumberImages() > image_upload_limits()['max_frames']) {
                $im->clear();
                $im->destroy();
                log_message('error', 'GIF ditolak: jumlah frame melebihi batas.');
                return null;
            }

            $isAnimated = $im->getNumberImages() > 1;
            if ($isAnimated) {
                $im = $im->coalesceImages();
                foreach ($im as $frame) {
                    $frame->setImageFormat('webp');
                    $frame->setImageCompressionQuality($quality);
                }
                $im = $im->optimizeImageLayers();
                $ok = $im->writeImages($destPath, true);
            } else {
                $im->setImageFormat('webp');
                $im->setImageCompressionQuality($quality);
                $ok = $im->writeImage($destPath);
            }

            $im->clear();
            $im->destroy();

            if (!empty($ok) && is_file($destPath)) {
                return $destPath;
            }
        } catch (Throwable $e) {
            log_message('error', 'Imagick GIF->WebP failed: ' . $e->getMessage());
        }

        @unlink($destPath);
        return null;
    }
}
