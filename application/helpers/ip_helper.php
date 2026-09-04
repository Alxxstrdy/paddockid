<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mendapatkan IP asli klien dengan aman.
 *
 * Header yang bisa dipalsukan klien (X-Forwarded-For, CF-Connecting-IP, X-Real-IP)
 * HANYA dipercaya jika koneksi langsung (REMOTE_ADDR) berasal dari proxy yang
 * terdaftar di $config['trusted_proxies']. Tanpa proxy tepercaya, fungsi ini
 * mengembalikan IP koneksi langsung sehingga rate-limit tidak bisa dibypass.
 */
if (!function_exists('get_real_ip')) {
    function get_real_ip()
    {
        $ci =& get_instance();
        $direct = $ci->input->ip_address();
        $trusted = $ci->config->item('trusted_proxies');

        if (is_array($trusted) && !empty($trusted) && in_array($direct, $trusted, true)) {
            $candidates = [];

            if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
                $candidates[] = $_SERVER['HTTP_CF_CONNECTING_IP'];
            }
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                foreach (explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']) as $ip) {
                    $candidates[] = trim($ip);
                }
            }
            if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
                $candidates[] = $_SERVER['HTTP_X_REAL_IP'];
            }

            foreach ($candidates as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $direct;
    }
}
