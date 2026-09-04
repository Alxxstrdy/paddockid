<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Cookie Consent & Preferensi
 *
 * Menangani:
 *   - Endpoint untuk menyimpan keputusan consent cookie.
 *   - Endpoint untuk menambah/mengubah/menghapus preferensi pengguna
 *     (theme, bahasa, bunyi notifikasi) berbasis cookie.
 */
class Consent extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('cookie_pref_helper');
    }

    /**
     * Simpan keputusan izin cookie (GDPR-lite).
     * POST: action = 'accept_all' | 'essential_only'
     */
    public function save()
    {
        $action = $this->input->post('action');

        if ($action !== 'accept_all' && $action !== 'essential_only') {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Aksi tidak valid.',
                ]));
            return;
        }

        // Simpan keputusan + apakah mengizinkan iklan/pelacakan pihak ketiga.
        set_pref_cookie('consent', '1', 31536000);
        set_pref_cookie('ads_consent', $action === 'accept_all' ? '1' : '0', 31536000);

        $this->output->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'ads_allowed' => $action === 'accept_all',
            ]));
    }

    /**
     * Ubah preferensi pengguna via cookie.
     * POST: key (theme|lang|notif_sound), value
     */
    public function set_preference()
    {
        $key = $this->input->post('key');
        $value = $this->input->post('value');

        $whitelist = array(
            'theme'      => array('dark', 'light'),
            'lang'       => array('id', 'en'),
            'notif_sound' => array('on', 'off'),
            'feed_tab'   => array('for_you', 'following'),
        );

        if (!isset($whitelist[$key])) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Kunci preferensi tidak valid.',
                ]));
            return;
        }

        if (!in_array($value, $whitelist[$key], true)) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Nilai tidak valid.',
                ]));
            return;
        }

        set_pref_cookie($key, $value, 31536000);

        $this->output->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'key'    => $key,
                'value'  => $value,
            ]));
    }
}
