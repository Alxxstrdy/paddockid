<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Cookie Preferensi PaddockID
 *
 * Helper untuk mengelola cookie preferensi first-party (non-sesi) ala
 * sosial media: theme, bahasa, bunyi notifikasi, tab feed, dan izin cookie.
 *
 * Tujuan:
 *   - Memisahkan cookie preferensi dari session & CSRF.
 *   - Menyediakan satu titik sentral untuk set/read/delete cookie dengan
 *     atribut keamanan yang konsisten (HttpOnly, SameSite, Secure).
 *   - Memudahkan halaman pengaturan untuk mengubah/menghapus preferensi.
 *
 * Catatan prefix:
 *   CI3 otomatis menambahkan `cookie_prefix` saat set_cookie() dipanggil
 *   dengan nama polos, tetapi `input->cookie()` membaca $_COOKIE polos.
 *   Karena itu: TULIS pakai nama polos, BACA pakai nama ber-prefix.
 */

if (!function_exists('pref_cookie_name')) {
    /**
     * Nama cookie yang sudah diberi prefix aplikasi (untuk dibaca).
     */
    function pref_cookie_name($name)
    {
        return config_item('cookie_prefix') . $name;
    }
}

if (!function_exists('set_pref_cookie')) {
    /**
     * Set cookie preferensi (first-party, persisten).
     *
     * @param string $name   Nama cookie (tanpa prefix).
     * @param string $value  Nilai.
     * @param int    $expire Durasi hidup dalam detik (default 1 tahun).
     * @return bool
     */
    function set_pref_cookie($name, $value, $expire = 31536000)
    {
        $CI =& get_instance();

        $cookie = array(
            'name'   => $name,
            'value'  => $value,
            'expire' => $expire,
            'domain' => config_item('cookie_domain'),
            'secure' => config_item('cookie_secure'),
            'httponly' => true,
            'samesite' => config_item('cookie_samesite'),
        );

        return $CI->input->set_cookie($cookie);
    }
}

if (!function_exists('get_pref_cookie')) {
    /**
     * Ambil nilai cookie preferensi. Mengembalikan default jika tidak ada.
     *
     * @param string $name    Nama cookie (tanpa prefix).
     * @param mixed  $default Nilai default bila cookie tidak ada.
     * @return mixed
     */
    function get_pref_cookie($name, $default = null)
    {
        $CI =& get_instance();
        $value = $CI->input->cookie(pref_cookie_name($name));

        if ($value === null || $value === '') {
            return $default;
        }

        return $value;
    }
}

if (!function_exists('delete_pref_cookie')) {
    /**
     * Hapus cookie preferensi.
     *
     * @param string $name Nama cookie (tanpa prefix).
     */
    function delete_pref_cookie($name)
    {
        $CI =& get_instance();

        $cookie = array(
            'name'   => $name,
            'value'  => '',
            'expire' => -86400,
            'domain' => config_item('cookie_domain'),
            'secure' => config_item('cookie_secure'),
            'httponly' => true,
            'samesite' => config_item('cookie_samesite'),
        );

        $CI->input->set_cookie($cookie);
    }
}

if (!function_exists('has_pref_cookie')) {
    /**
     * Cek apakah cookie preferensi ada.
     *
     * @param string $name Nama cookie (tanpa prefix).
     * @return bool
     */
    function has_pref_cookie($name)
    {
        $CI =& get_instance();
        return $CI->input->cookie(pref_cookie_name($name)) !== null;
    }
}
