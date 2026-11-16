<?php
/**
 * PaddockID upload-limit doctor (read-only).
 *
 * Jalankan dengan PHP yang dipakai web server:
 *   php scripts/check-upload-limits.php
 *
 * Untuk PHP-FPM, jalankan juga:
 *   php-fpm -i | grep -E 'upload_max_filesize|post_max_size|max_file_uploads'
 * atau buat phpinfo sementara di server. CLI dan FPM punya ini terpisah.
 */

function ini_bytes($value)
{
    $value = trim((string) $value);
    if ($value === '') return 0;
    $unit = strtolower(substr($value, -1));
    $num = (float) $value;
    switch ($unit) {
        case 'g': $num *= 1024;
        case 'm': $num *= 1024;
        case 'k': $num *= 1024;
    }
    return (int) $num;
}

function fmt_bytes($bytes)
{
    if ($bytes >= 1024 * 1024) return round($bytes / 1024 / 1024, 1) . ' MB';
    if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
    return $bytes . ' B';
}

$checks = [
    'upload_max_filesize' => 12 * 1024 * 1024,
    'post_max_size'       => 52 * 1024 * 1024,
    'memory_limit'        => 256 * 1024 * 1024,
];

echo "PaddockID upload-limit doctor\n";
echo "SAPI: " . PHP_SAPI . "\n";
echo "php.ini: " . (php_ini_loaded_file() ?: '(none)') . "\n";
echo "file_uploads: " . ini_get('file_uploads') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . " (app butuh minimal 4)\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "s\n\n";

$failed = false;
foreach ($checks as $key => $recommended) {
    $raw = ini_get($key);
    $actual = ini_bytes($raw);
    $ok = ($key === 'memory_limit' && $actual === -1) || $actual >= $recommended;
    printf("%-22s %-10s minimal %-10s %s\n", $key, fmt_bytes($actual), fmt_bytes($recommended), $ok ? 'OK' : 'WARN');
    if (!$ok) $failed = true;
}

$maxFiles = (int) ini_get('max_file_uploads');
if ($maxFiles > 0 && $maxFiles < 4) {
    echo "max_file_uploads terlalu kecil untuk 4 gambar per post.\n";
    $failed = true;
}

echo "\nCatatan: nilai di atas adalah SAPI " . PHP_SAPI . ". Jika CLI berbeda dari PHP-FPM,\n";
echo "periksa konfigurasi FPM secara terpisah sebelum mengubah klaim UI.\n";

exit($failed ? 1 : 0);
