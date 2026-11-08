<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Moderation
 * ==========
 * Klien PHP untuk microservice moderasi konten PaddockID
 * (XLM-RoBERTa untuk teks + YOLOv8-cls untuk gambar).
 *
 * Prinsip penting:
 *   - FAIL-OPEN. Kalau service mati/lambat/error, konten tetap lolos.
 *     Metode check() TIDAK PERNAH melempar exception ke controller.
 *   - MONITOR-ONLY (lihat config/moderation.php). Keputusan tetap dicatat,
 *     tetapi pemanggil tidak memblokir konten selama fase uji kelayakan.
 *
 * Pemakaian (dari controller):
 *   $this->moderation->check($teks, [$pathGambarAbsolut], [
 *       'type' => 'post' | 'comment',
 *       'id'   => $id,
 *       'user_id' => $user_id,
 *   ]);
 */
class Moderation {

    /** @var CI_Controller */
    private $ci;

    /** @var array */
    private $cfg;

    public function __construct() {
        $this->ci =& get_instance();
        // Muat tanpa section (pola yang sama dengan config/ads.php).
        $this->ci->config->load('moderation');
        $cfg = $this->ci->config->item('moderation');
        // Jaga-jaga bila suatu saat dimuat dengan section (TRUE) -> ada nesting.
        if (isset($cfg['moderation']) && is_array($cfg['moderation'])) {
            $cfg = $cfg['moderation'];
        }
        $this->cfg = is_array($cfg) ? $cfg : [];
    }

    public function enabled() {
        return !empty($this->cfg['enabled']);
    }

    public function monitor_only() {
        return !empty($this->cfg['monitor_only']);
    }

    /**
     * Hitung keputusan moderasi untuk sebuah konten.
     *
     * @param  string $text             teks konten (post/komentar)
     * @param  array  $image_abs_paths  path ABSOLUT gambar (opsional)
     * @param  array  $context          info log: type, id, user_id
     * @return array  ['ok'=>bool,'decision'=>string,'reason'=>string,'total_ms'=>int,'raw'=>mixed,'error'=>?string]
     *                decision bernilai APPROVE|REVIEW|BLOCK, atau SKIPPED bila nonaktif/gagal.
     */
    public function check($text, array $image_abs_paths = [], array $context = []) {
        $result = [
            'ok'       => FALSE,
            'decision' => 'SKIPPED',
            'reason'   => '',
            'total_ms' => 0,
            'raw'      => NULL,
            'error'    => NULL,
        ];

        if (!$this->enabled()) {
            $result['reason'] = 'moderation disabled';
            $this->write_log($text, $image_abs_paths, $context, $result);
            return $result;
        }

        $base    = rtrim($this->cfg['base_url'] ?? 'http://127.0.0.1:8000', '/');
        $timeout = (int) ($this->cfg['timeout'] ?? 3);

        try {
            $start = microtime(TRUE);

            // Saring gambar yang benar-benar ada di disk.
            $images = [];
            foreach ($image_abs_paths as $p) {
                if (is_string($p) && is_file($p)) {
                    $images[] = $p;
                }
            }

            $ch = curl_init();
            if (empty($images)) {
                // Teks saja -> JSON ke /moderate/text (lebih ringan).
                curl_setopt_array($ch, [
                    CURLOPT_URL            => $base . '/moderate/text',
                    CURLOPT_POST           => TRUE,
                    CURLOPT_POSTFIELDS     => json_encode(['text' => (string) $text]),
                    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT        => $timeout,
                    CURLOPT_CONNECTTIMEOUT => min(2, $timeout),
                ]);
            } else {
                // Teks + gambar -> multipart ke /moderate (fusi).
                //
                // Catatan: PHP curl tidak bisa mengirim BEBERAPA field dengan
                // nama sama (list), sedangkan FastAPI mengharapkan field "files"
                // yang berulang. Jadi body multipart dibangun manual.
                $mp = $this->build_multipart(['text' => (string) $text], $images);
                curl_setopt_array($ch, [
                    CURLOPT_URL            => $base . '/moderate',
                    CURLOPT_POST           => TRUE,
                    CURLOPT_POSTFIELDS     => $mp['body'],
                    CURLOPT_HTTPHEADER     => [
                        'Content-Type: multipart/form-data; boundary=' . $mp['boundary'],
                        'Content-Length: ' . strlen($mp['body']),
                    ],
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT        => $timeout,
                    CURLOPT_CONNECTTIMEOUT => min(2, $timeout),
                ]);
            }

            $body  = curl_exec($ch);
            $errno = curl_errno($ch);
            $http  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $elapsed = (int) round((microtime(TRUE) - $start) * 1000);
            $result['total_ms'] = $elapsed;

            if ($errno !== 0 || $body === FALSE) {
                $result['error'] = 'curl_errno=' . $errno . ' (' . curl_strerror($errno) . ')';
            } elseif ($http < 200 || $http >= 300) {
                $result['error'] = 'http=' . $http . ' body=' . substr((string) $body, 0, 200);
            } else {
                $decoded = json_decode((string) $body, TRUE);
                if (!is_array($decoded)) {
                    $result['error'] = 'respons bukan JSON valid';
                } else {
                    $result['ok']       = TRUE;
                    $result['decision'] = $decoded['decision'] ?? 'UNKNOWN';
                    $result['reason']   = $decoded['reason'] ?? '';
                    $result['raw']      = $decoded;
                }
            }
        } catch (Throwable $e) {
            // Apa pun yang terjadi: jangan sampai menggagalkan posting.
            $result['error'] = 'exception: ' . $e->getMessage();
        }

        $this->write_log($text, $image_abs_paths, $context, $result);

        return $result;
    }

    /**
     * Bangun body multipart/form-data secara manual.
     * Dipakai agar bisa mengirim beberapa file dengan nama field yang sama
     * ("files"), yang tidak didukung CURLOPT_POSTFIELDS bentuk array.
     *
     * @param  array $fields  [nama => nilai] field teks
     * @param  array $files   daftar path file, semuanya dikirim sebagai "files"
     * @return array ['body' => string, 'boundary' => string]
     */
    private function build_multipart(array $fields, array $files) {
        $eol      = "\r\n";
        $boundary = '----PaddockID' . bin2hex(random_bytes(16));
        $body     = '';

        foreach ($fields as $name => $value) {
            $body .= '--' . $boundary . $eol;
            $body .= 'Content-Disposition: form-data; name="' . $name . '"' . $eol . $eol;
            $body .= $value . $eol;
        }

        foreach ($files as $path) {
            $filename = basename($path);
            $ctype    = function_exists('mime_content_type')
                ? (mime_content_type($path) ?: 'application/octet-stream')
                : 'application/octet-stream';
            $body .= '--' . $boundary . $eol;
            $body .= 'Content-Disposition: form-data; name="files"; filename="' . $filename . '"' . $eol;
            $body .= 'Content-Type: ' . $ctype . $eol . $eol;
            $realBase = realpath(APPPATH . '../uploads');
            $realPath = realpath($path);
            if ($realBase && $realPath && strpos($realPath, $realBase) === 0 && is_file($realPath)) {
                $body .= @file_get_contents($realPath) . $eol;
            }
            // skip invalid/untrusted file
        }

        $body .= '--' . $boundary . '--' . $eol;

        return ['body' => $body, 'boundary' => $boundary];
    }

    /**
     * Catat satu baris JSON ke log_file. Tidak pernah melempar error.
     */
    private function write_log($text, array $image_abs_paths, array $context, array $result) {
        try {
            $line = [
                'ts'        => date('c'),
                'context'   => $context,
                'text_len'  => function_exists('mb_strlen') ? mb_strlen((string) $text) : strlen((string) $text),
                'text_head' => function_exists('mb_substr')
                    ? mb_substr(trim((string) $text), 0, 120)
                    : substr(trim((string) $text), 0, 120),
                'n_images'  => count($image_abs_paths),
                'ok'        => $result['ok'],
                'decision'  => $result['decision'],
                'reason'    => $result['reason'],
                'total_ms'  => $result['total_ms'],
                'error'     => $result['error'],
            ];

            if (is_array($result['raw'])) {
                $raw = $result['raw'];
                if (!empty($raw['text']['label'])) {
                    $line['text_label'] = $raw['text']['label'];
                    $line['text_conf']  = $raw['text']['confidence'] ?? NULL;
                }
                if (!empty($raw['images']) && is_array($raw['images'])) {
                    $line['images'] = array_map(static function ($im) {
                        // YOLOv8-cls: hasil klasifikasi (label + probabilitas).
                        return [
                            'label'      => $im['label'] ?? NULL,
                            'confidence' => $im['confidence'] ?? NULL,
                            'flagged'    => $im['flagged'] ?? FALSE,
                            'trained'    => $im['classes_are_trained'] ?? FALSE,
                        ];
                    }, $raw['images']);
                }
            }

            $file = $this->cfg['log_file'] ?? (APPPATH . 'logs/moderation.log');
            @file_put_contents(
                $file,
                json_encode($line, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
                FILE_APPEND | LOCK_EX
            );
        } catch (Throwable $e) {
            // Logging tidak boleh mengganggu aplikasi.
            @log_message('error', 'Moderation::write_log gagal: ' . $e->getMessage());
        }
    }
}
