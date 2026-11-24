<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Renderer tunggal untuk media postingan.
 *
 * Dipakai oleh semua view yang menampilkan gambar post (home, detail, profil,
 * user, search). Post satu gambar memakai tampilan gambar biasa; post dengan
 * beberapa gambar memakai carousel satu slide per gambar.
 *
 * Urutan gambar mengikuti urutan string file_url (dari post_media ORDER BY id ASC).
 *
 * @param string $file_url   Daftar URL gambar yang dipisahkan koma.
 * @param array  $dimensions Opsi metadata: [['width'=>1200,'height'=>1600], ...].
 *                           Boleh kosong; carousel akan mengukur via frontend.
 * @return string
 */
if (!function_exists('post_media_html')) {
    function post_media_html($file_url, $dimensions = array())
    {
        $urls = array();
        if (!empty($file_url)) {
            foreach (explode(',', $file_url) as $url) {
                $url = trim($url);
                if ($url !== '') {
                    $urls[] = $url;
                }
            }
        }

        if (empty($urls)) {
            return '';
        }

        $count = count($urls);
        $is_carousel = $count > 1;
        $initial_ratio = '4 / 3';

        if (!empty($dimensions[0]['width']) && !empty($dimensions[0]['height'])) {
            $w = (int) $dimensions[0]['width'];
            $h = (int) $dimensions[0]['height'];
            if ($w > 0 && $h > 0) {
                $initial_ratio = $w . ' / ' . $h;
            }
        }

        $html  = '<div class="h-post__media">';
        $html .= '<div class="post-media ' . ($is_carousel ? 'post-media--carousel' : 'post-media--single') . '" data-pmc data-pmc-count="' . $count . '">';
        $html .= '<div class="post-media__frame" data-pmc-frame style="--pmc-ratio: ' . $initial_ratio . ';"';
        if ($is_carousel) {
            $html .= ' role="group" aria-roledescription="carousel" aria-label="Galeri postingan" tabindex="0"';
        }
        $html .= '>';

        foreach ($urls as $index => $url) {
            $active = ($index === 0);
            $width  = isset($dimensions[$index]['width']) ? (int) $dimensions[$index]['width'] : 0;
            $height = isset($dimensions[$index]['height']) ? (int) $dimensions[$index]['height'] : 0;
            $dim_attr = ($width > 0 && $height > 0)
                ? ' data-width="' . $width . '" data-height="' . $height . '"'
                : '';

            $html .= '<div class="post-media__slide' . ($active ? ' is-active' : '') . '" data-pmc-slide data-pmc-index="' . $index . '" data-pmc-open';
            if (!$active) {
                $html .= ' aria-hidden="true"';
            }
            $html .= '>';

            $src_attr = $active
                ? ' src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"'
                : ' data-src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"';

            $html .= '<img' . $src_attr . ' alt="Media postingan ' . ($index + 1) . '" loading="lazy" decoding="async" data-pmc-img' . $dim_attr . '>';
            $html .= '</div>';
        }

        if ($is_carousel) {
            $html .= '<button type="button" class="pmc__nav pmc__nav--prev" data-pmc-prev aria-label="Gambar sebelumnya"><i data-lucide="chevron-left"></i></button>';
            $html .= '<button type="button" class="pmc__nav pmc__nav--next" data-pmc-next aria-label="Gambar berikutnya"><i data-lucide="chevron-right"></i></button>';
            $html .= '<span class="pmc__counter" data-pmc-counter aria-live="polite">1 / ' . $count . '</span>';
        }

        $html .= '</div></div></div>';

        return $html;
    }
}
