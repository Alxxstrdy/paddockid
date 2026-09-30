<?php

if (!defined('WIB_TIMEZONE_SET')) {
    date_default_timezone_set('Asia/Jakarta');
    define('WIB_TIMEZONE_SET', true);
}

if (!function_exists('session_duration_minutes')) {
    function session_duration_minutes($label) {
        $map = [
            'FP1' => 90, 'FP2' => 90, 'FP3' => 90,
            'Sprint Qualifying' => 90,
            'Qualifying' => 90,
            'Practice 1' => 90, 'Practice 2' => 90, 'Practice 3' => 90,
            'Sprint' => 60,
            'Race' => 120,
        ];
        return $map[$label] ?? 90;
    }
}

if (!function_exists('formatWaktuSosmed')) {
    function formatWaktuSosmed($datetime_str) {
        $waktu_post = new DateTime($datetime_str, new DateTimeZone('Asia/Jakarta'));
        $waktu_sekarang = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $selisih = $waktu_sekarang->diff($waktu_post);
        $total_jam = ($selisih->days * 24) + $selisih->h;

        if ($total_jam < 24) {
            if ($total_jam < 1) {
                return $selisih->i == 0 ? 'Baru saja' : $selisih->i . ' menit yang lalu';
            }
            return $total_jam . ' jam yang lalu';
        } else {
            return $waktu_post->format('d M Y');
        }
    }
}