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

        if (is_array($trusted) && !empty($trusted) && _ip_in_trusted_proxy($direct, $trusted)) {
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

    if (!function_exists('_ip_in_trusted_proxy')) {
        function _ip_in_trusted_proxy($ip, array $trusted)
        {
            if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
                return false;
            }

            $packed_ip = inet_pton($ip);

            foreach ($trusted as $entry) {
                $entry = trim($entry);
                if ($entry === '') {
                    continue;
                }

                // IP eksak (tanpa CIDR)
                if (strpos($entry, '/') === false) {
                    if (inet_pton($entry) === $packed_ip) {
                        return true;
                    }
                    continue;
                }

                list($net, $bits_str) = explode('/', $entry, 2);
                if (!is_numeric($bits_str) || inet_pton($net) === false) {
                    continue;
                }

                $bits = (int) $bits_str;
                $packed_net = inet_pton($net);

                $full_len = strlen($packed_ip) * 8;
                $bits = max(0, min($bits, $full_len));

                $mask = str_repeat("\xff", intdiv($bits, 8));
                if ($bits % 8 !== 0) {
                    $mask .= chr(0xff << (8 - ($bits % 8)));
                }
                $mask = str_pad($mask, $full_len / 8, "\0");

                if ((($packed_ip & $mask) === ($packed_net & $mask))) {
                    return true;
                }
            }

            return false;
        }
    }
}
