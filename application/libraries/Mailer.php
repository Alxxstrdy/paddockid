<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mailer — pembungkus CI3 Email library.
 * Konfigurasi dibaca dari config/email.php (env-driven dari .env).
 */
class Mailer
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * Kirim email HTML.
     *
     * @param string $to           Penerima.
     * @param string $subject      Subjek (tanpa akhiran "- PaddockID").
     * @param string $message_html Isi email (boleh HTML).
     * @return bool TRUE jika berhasil dikirim/diserahkan.
     */
    public function send($to, $subject, $message_html)
    {
        $this->CI->load->library('email');
        $this->CI->load->config('email', true);

        $from = getenv('SMTP_FROM');
        if (empty($from)) {
            $from = $this->CI->config->item('smtp_user', 'email');
        }
        if (empty($from)) {
            $from = 'no-reply@paddockid.web.id';
        }

        $this->CI->email->from($from, 'PaddockID');
        $this->CI->email->to($to);
        $this->CI->email->subject($subject . ' - PaddockID');
        $this->CI->email->message($message_html);

        return (bool) $this->CI->email->send();
    }

    /**
     * Render HTML email dari view application/views/emails/layout.php
     * (satu-satunya sumber template — lihat file itu untuk variabel & gaya).
     *
     * @param string          $heading   Judul email.
     * @param string          $body      Isi HTML (boleh <br>, <b>, dsb).
     * @param array|null      $button    Opsional: ['url' => ..., 'text' => ...].
     * @param string          $preheader Opsional: teks preview di inbox.
     * @return string
     */
    public function template($heading, $body, $button = null, $preheader = '')
    {
        if (!function_exists('base_url')) {
            $this->CI->load->helper('url');
        }

        return $this->CI->load->view('emails/layout', array(
            'heading'   => $heading,
            'body'      => $body,
            'button'    => $button,
            'preheader' => $preheader,
        ), true);
    }
}