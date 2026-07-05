<?php

if (!defined('WIB_TIMEZONE_SET')) {
    date_default_timezone_set('Asia/Jakarta');
    define('WIB_TIMEZONE_SET', true);
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