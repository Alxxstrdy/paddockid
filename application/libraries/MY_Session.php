<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * MY_Session
 *
 * Mengatur sess_expiration berdasarkan preferensi login yang disimpan
 * di cookie "session_exp" (di-set saat login):
 *   - 2592000 detik (30 hari)  -> Remember Me
 *   - 604800 detik  (7 hari)   -> tanpa Remember Me
 *
 * CI3 membaca sess_expiration saat library session di-load, sehingga
 * override harus dilakukan sebelum parent::__construct().
 */
class MY_Session extends CI_Session
{
    public function __construct(array $params = array())
    {
        $CI =& get_instance();

        $cookie_name = config_item('cookie_prefix') . 'session_exp';
        if (isset($_COOKIE[$cookie_name])) {
            $seconds = ((int) $_COOKIE[$cookie_name] === 2592000) ? 2592000 : 604800;
            $CI->config->set_item('sess_expiration', $seconds);
        }

        parent::__construct($params);
    }
}