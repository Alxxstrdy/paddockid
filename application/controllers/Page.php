<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Halaman statis: Kebijakan Privasi dan Kontak.
 *
 * Sengaja tidak butuh login, supaya pengunjung baru bisa membacanya
 * sebelum mendaftar.
 */
class Page extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
    }

    private function _current_user_id() {
        $session_data = $this->session->userdata('user_logged_in');
        return $session_data ? (int) $session_data['user_id'] : 0;
    }

    public function privacy() {
        $data = [
            'title' => 'Kebijakan Privasi | PaddockID',
            'meta_description' => 'Penjelasan data apa yang dikumpulkan PaddockID, bagaimana '
                . 'digunakan, dan hak Anda sebagai pengguna.',
            'current_user_id' => $this->_current_user_id(),
            'is_logged_in' => $this->_current_user_id() > 0,
            'page_css' => ['pages'],
        ];
        $this->_render($data, 'privacy');
    }

    public function about() {
        $data = [
            'title' => 'Tentang | PaddockID',
            'meta_description' => 'PaddockID adalah komunitas Formula 1 Indonesia — wadah para '
                . 'penggemar berbagi momen, berdiskusi, dan mengikuti balapan bersama.',
            'page_url' => base_url('about'),
        ];
        $this->load->view('about', $data);
    }

    public function donate() {
        $data = [
            'title' => 'Dukung PaddockID | Donasi',
            'meta_description' => 'Dukung PaddockID lewat donasi sukarela. Situs bebas iklan, '
                . 'dan dana dipakai untuk biaya server, domain, serta bandwidth API.',
            'current_user_id' => $this->_current_user_id(),
            'is_logged_in' => $this->_current_user_id() > 0,
            'page_css' => ['pages'],
        ];
        $this->_render($data, 'donate');
    }

    public function contact() {
        $data = [
            'title' => 'Kontak | PaddockID',
            'meta_description' => 'Cara menghubungi pengelola PaddockID untuk pertanyaan, '
                . 'laporan konten, atau permintaan penghapusan data.',
            'current_user_id' => $this->_current_user_id(),
            'is_logged_in' => $this->_current_user_id() > 0,
            'contact_email' => $this->_contact_email(),
            'page_css' => ['pages'],
        ];
        $this->_render($data, 'contact');
    }

    /**
     * Endpoint formulir kontak (POST, dilindungi CSRF bawaan CI).
     */
    public function send_message() {
        $json = function ($status, $message, $code = 200) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header($code)
                ->set_output(json_encode(['status' => $status, 'message' => $message]));
        };

        if ($this->input->method() !== 'post') {
            return $json('error', 'Metode tidak diizinkan.', 405);
        }

        // Honeypot: bot mengisi field tersembunyi, manusia tidak.
        // Perhatikan: input->post() mengembalikan NULL kalau field tidak
        // dikirim sama sekali, jadi harus dilonggarkan ke string dulu --
        // kalau tidak, NULL !== '' bernilai true dan semua user asli
        // akan mendapat balasan "terkirim" padahal pesan tidak terkirim.
        if (trim((string) $this->input->post('website')) !== '') {
            return $json('success', 'Pesan kamu sudah terkirim.');
        }

        if (!throttle('contact_form', 5, 60)) {
            return $json('error', 'Terlalu banyak percobaan. Coba lagi dalam satu jam.', 429);
        }

        $name    = trim($this->input->post('name', true));
        $email   = trim($this->input->post('email', true));
        $category = trim($this->input->post('category', true));
        $message = trim($this->input->post('message', true));

        if ($name === '' || mb_strlen($name) > 80) {
            return $json('error', 'Nama wajib diisi (maksimal 80 karakter).', 422);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 120) {
            return $json('error', 'Alamat email tidak valid.', 422);
        }
        if (mb_strlen($message) < 10 || mb_strlen($message) > 2000) {
            return $json('error', 'Pesan harus antara 10 dan 2000 karakter.', 422);
        }

        $categories = [
            'umum'     => 'Pertanyaan umum',
            'laporan'  => 'Laporan konten',
            'hapus'    => 'Permintaan penghapusan data',
            'kerja sama' => 'Kerja sama / sponsor',
        ];
        $category_label = $categories[$category] ?? 'Lainnya';

        $this->load->library('mailer');
        $row = function ($label, $value) {
            return '<tr>'
                . '<td style="padding:8px 12px;border-bottom:1px solid #eee;color:#666;width:120px;">'
                . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td style="padding:8px 12px;border-bottom:1px solid #eee;color:#111;">'
                . nl2br(htmlspecialchars($value, ENT_QUOTES, 'UTF-8')) . '</td>'
                . '</tr>';
        };

        $body = '<table cellpadding="0" cellspacing="0" style="font-family:Arial,sans-serif;font-size:14px;">'
            . $row('Nama', $name)
            . $row('Email', $email)
            . $row('Kategori', $category_label)
            . $row('Pesan', $message)
            . '</table>';

        $html = $this->mailer->template(
            'Pesan baru dari halaman Kontak',
            '<p>Ada pesan baru dari <strong>' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8')
            . '</strong> melalui paddockid.web.id/contact.</p>' . $body
        );

        if (!$this->mailer->send($this->_contact_email(), 'Kontak: ' . $category_label, $html)) {
            log_message('error', 'Kontak: gagal mengirim pesan dari ' . $email);
            return $json('error', 'Gagal terkirim. Coba lagi nanti atau kirim langsung ke email kami.', 500);
        }

        return $json('success', 'Pesan kamu sudah terkirim. Kami akan membalas lewat email.');
    }

    /**
     * Kotak masuk situs. Pakai alamat yang sama dengan pengirim sistem
     * supaya tidak perlu menambah kredensial baru di .env.
     */
    private function _contact_email() {
        $to = getenv('SMTP_FROM');
        return $to ? $to : 'paddockidd@gmail.com';
    }

    private function _render($data, $view) {
        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view($view, $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }
}
