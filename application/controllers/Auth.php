<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    private $_session_loaded = false;

    private function _ensure_session()
    {
        if (!$this->_session_loaded) {
            $this->load->library('session');
            $this->_session_loaded = true;
        }
    }

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('Auth_model');
        $this->load->model('Activity_model');
    }

    /**
     * Helper Privat untuk Mendapatkan Real IP Address dari Client
     * Mengamankan deteksi IP dari Cloudflare, Ngrok, atau Load Balancer
     */
    private function _get_real_ip()
    {
        return get_real_ip();
    }

    /**
     * Tampilan Halaman Login (Debug IP sudah dibersihkan)
     */
    public function index()
    {
        $this->_ensure_session();
        // Jika user sudah login, langsung lempar ke halaman utama
        if ($this->session->userdata('user_logged_in')) {
            redirect(base_url());
        }
        $this->load->view('login');
    }

    public function login()
    {
        $this->index();
    }

    /**
     * Tampilan Halaman Register
     */
    public function register()
    {
        $this->_ensure_session();
        if ($this->session->userdata('user_logged_in')) {
            redirect(base_url());
        }
        $this->load->view('register');
    }

    /**
     * PROSES LOGIN MANUAL / REGULER
     */
    public function login_process()
    {
        $identity   = $this->input->post('identity', true); 
        $password   = $this->input->post('password', true);
        $remember   = $this->input->post('remember'); 

        // Session duration HARUS diatur sebelum library session dimuat
        // (CI3 hanya membaca sess_expiration saat inisialisasi):
        // 7 hari biasa, 30 hari jika Remember Me
        $remember_duration = $remember ? 2592000 : 604800;
        $this->config->set_item('sess_expiration', $remember_duration);
        $this->input->set_cookie('session_exp', (string) $remember_duration, $remember_duration);

        $ip_address = $this->_get_real_ip();

        $this->_ensure_session();

        // 1. Cek limit percobaan gagal via Model (IP + account-based)
        $attempts_ip = $this->Auth_model->count_failed_attempts($ip_address);
        $attempts_user = $this->Auth_model->count_failed_attempts(null, $identity);
        $attempts = max($attempts_ip, $attempts_user);

        if ($attempts >= 3) {
            $this->session->set_flashdata('error', 'Terlalu banyak percobaan login. Silakan tunggu 10 menit lagi.');
            redirect('auth');
            return;
        }

        // 2. Ambil data user
        $user = $this->Auth_model->get_user_by_identity($identity);

        if ($user && $user['status'] !== 'active') {
            // Anti-enumeration: pesan sama dengan gagal login biasa, tetap catat percobaan
            $this->Auth_model->insert_failed_attempt($ip_address, $identity);
            $sisa_percobaan = 3 - ($attempts + 1);
            $this->session->set_flashdata('error', $sisa_percobaan > 0 ? 'Username/Email atau password salah.' : 'Silakan tunggu 10 menit.');
            redirect('auth');
            return;
        }

        if ($user && $user['status'] === 'active') {
            if ($user['login_type'] === 'regular' && password_verify($password, $user['password'])) {

                $this->Auth_model->clear_failed_attempts($ip_address, $identity);
                $this->Auth_model->insert_successful_login($ip_address, $identity);

                $this->setup_session($user, $remember ? 2592000 : 604800);
                $this->Activity_model->log($user['id_user'], $user['username'], 'login', null, null, 'Login regular');

                redirect(base_url());
                return;
            }
        }

        // 3. Jika gagal login...
        $this->Auth_model->insert_failed_attempt($ip_address, $identity);

        $sisa_percobaan = 3 - ($attempts + 1);
        $msg = ($sisa_percobaan > 0) ? "Username/Email atau password salah." : "Silakan tunggu 10 menit.";

        $this->session->set_flashdata('error', $msg);
        redirect('auth');
    }

/**
     * PROSES REGISTRASI MANUAL
     * Alur: Username -> Email -> Password -> Verifikasi Password
     */
    public function register_process()
    {
        $this->_ensure_session();

        $ip_address = $this->_get_real_ip();

        // Rate limit: max 3 registrasi per jam per IP
        if (!$this->Auth_model->check_rate_limit($ip_address, 'register', 3, 60)) {
            $this->session->set_flashdata('error', 'Terlalu banyak pendaftaran dari perangkat ini. Coba lagi nanti.');
            redirect('auth/register');
            return;
        }

        $username         = trim($this->input->post('username', true));
        $email            = trim($this->input->post('email', true));
        $password         = $this->input->post('password', true);
        $confirm_password = $this->input->post('confirm_password', true);

        // 1. Validasi: Kecocokan Password & Verifikasi Password
        if ($password !== $confirm_password) {
            $this->session->set_flashdata('error', 'Konfirmasi verifikasi password tidak cocok.');
            redirect('auth/register');
            return;
        }

        // 2. Validasi Keamanan Password: Besar, Kecil, Angka, Simbol, Minimal 8 Karakter
        // Aturan: Besar (?=.*[A-Z]), Kecil (?=.*[a-z]), Angka (?=.*\d), Simbol (?=.*[@$!%*?&])
        $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
        if (!preg_match($pattern, $password)) {
            $this->session->set_flashdata('error', 'Keamanan lemah! Password wajib minimal 8 karakter berisi kombinasi huruf besar, kecil, angka, dan simbol (@$!%*?&).');
            redirect('auth/register');
            return;
        }

        // 3. Validasi Duplikasi Data: Cek Username
        if ($this->Auth_model->is_username_exists($username)) {
            $this->session->set_flashdata('error', 'Username sudah digunakan, silakan pilih username lain.');
            redirect('auth/register');
            return;
        }

        // 4. Validasi Duplikasi Data: Cek Email
        if ($this->Auth_model->get_user_by_identity($email)) {
            $this->session->set_flashdata('error', 'Email sudah terdaftar, silakan masuk atau gunakan email lain.');
            redirect('auth/register');
            return;
        }

        // Siapkan data payload ke database
        $data = [
            'username'       => $username,
            'display_name'   => $username,
            'email'          => $email,
            'password'       => password_hash($password, PASSWORD_BCRYPT),
            'login_type'     => 'regular',
            'status'         => 'active',
            'email_verified' => 0,
            'verified'       => 0,
            'avatar'         => 'default.jpg',
            'created_at'     => date('Y-m-d H:i:s')
        ];

        $insert_id = $this->Auth_model->register_google_user($data);
        if ($insert_id) {
            $this->Auth_model->log_rate_limit_action($ip_address, 'register');
            $this->Activity_model->log(null, $username, 'register', null, null, 'Akun baru dibuat: ' . $email);

            // Kirim email verifikasi (maks 3x/jam tetap dilindungi rate limit)
            $token = bin2hex(random_bytes(32));
            $this->Auth_model->set_email_verification_token($insert_id, $token);
            $this->_send_verification_mail($email, $insert_id, $token);

            // Simpan "email sedang menunggu verifikasi" agar halaman check_email bisa mengikuti
            $this->session->set_userdata('pending_email_verification', $email);
            redirect('auth/check_email');
        } else {
            $this->session->set_flashdata('error', 'Terjadi gangguan internal sistem, coba kembali nanti.');
            redirect('auth/register');
        }
    }

    /**
     * TRIGGER GOOGLE AUTH URL
     */
    public function google_login()
    {
        require_once APPPATH . '../vendor/autoload.php';

        $client = new Google_Client();
        $client->setClientId(getenv('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(base_url('auth/google_callback'));
        $client->addScope('email');
        $client->addScope('profile');

        if (!$client->getClientId() || !$client->getClientSecret()) {
            $this->session->set_flashdata('error', 'Login Google sedang tidak tersedia.');
            redirect('auth');
            return;
        }

        $this->_ensure_session();

        // Anti-CSRF login OAuth: state acak disimpan di session lalu diverifikasi di callback
        $state = bin2hex(random_bytes(16));
        $this->session->set_userdata('oauth_state', $state);
        $client->setState($state);

        redirect($client->createAuthUrl());
    }

    /**
     * CALLBACK GOOGLE OAUTH
     */
    public function google_callback()
    {
        require_once APPPATH . '../vendor/autoload.php';

        // Google login always uses long session; set config BEFORE session loads
        $this->config->set_item('sess_expiration', 2592000);
        $this->input->set_cookie('session_exp', '2592000', 2592000);

        $this->_ensure_session();

        $client = new Google_Client();
        $client->setClientId(getenv('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(base_url('auth/google_callback'));

        // Verifikasi state (anti-CSRF) sebelum menukar code
        $expected_state = $this->session->userdata('oauth_state');
        $this->session->unset_userdata('oauth_state');
        if (!$expected_state || !isset($_GET['state']) || !hash_equals($expected_state, (string) $_GET['state'])) {
            $this->session->set_flashdata('error', 'Sesi login Google tidak valid. Silakan coba lagi.');
            redirect('auth');
            return;
        }

        if (!isset($_GET['code'])) {
            $this->session->set_flashdata('error', 'Gagal login Google');
            redirect('auth');
            return;
        }

        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        if (isset($token['error'])) {
            $this->session->set_flashdata('error', 'Token Google gagal');
            redirect('auth');
            return;
        }

        $client->setAccessToken($token['access_token']);

        $google_oauth = new Google_Service_Oauth2($client);
        $google_info  = $google_oauth->userinfo->get();

        // Wajib email Google sudah diverifikasi Google (cegah account takeover via email tak terverifikasi)
        if (empty($google_info->emailVerified) && empty($google_info->verified_email)) {
            $this->session->set_flashdata('error', 'Akun Google kamu belum memiliki email terverifikasi. Verifikasi dulu di Google lalu coba lagi.');
            redirect('auth');
            return;
        }

        $email       = $google_info->email;
        $full_name   = $google_info->name;
        $google_id   = $google_info->id;

        $user = $this->Auth_model->get_user_by_google($google_id, $email);

        if (!$user) {
            $google_photo_url = $google_info->picture;
            $file_name = 'google_' . $google_id . '.jpg';
            $upload_path = FCPATH . 'assets/uploads/profile/';

            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            $image_content = @file_get_contents($google_photo_url);
            $profile_pic   = 'default.jpg'; // Default jika download gagal

            // Validasi benar-benar file gambar (defense-in-depth) sebelum disimpan
            if ($image_content !== false && @getimagesizefromstring($image_content) !== false) {
                if (file_put_contents($upload_path . $file_name, $image_content) !== false) {
                    $profile_pic = $file_name;
                }
            }

            $username_clean = explode('@', $email)[0];
            $username       = $this->generate_unique_username($username_clean);

            $data = [
                'google_id'      => $google_id,
                'username'       => $username,
                'display_name'   => $full_name,
                'avatar'         => $profile_pic,
                'email'          => $email,
                'password'       => null,
                'login_type'     => 'google',
                'status'         => 'active',
                'email_verified' => 1,
                'verified'       => 0,
                'created_at'     => date('Y-m-d H:i:s')
            ];

            $insert_id = $this->Auth_model->register_google_user($data);
            $data['id_user'] = $insert_id;
            $user = $data;
            $this->Activity_model->log($insert_id, $username, 'register', null, null, 'Akun baru via Google OAuth');

        } else {
            if ($user['status'] !== 'active') {
                $this->session->set_flashdata('error', 'Gagal masuk. Silakan hubungi admin jika ini akun kamu.');
                redirect('auth');
                return;
            }

            if ($user['login_type'] === 'regular') {
                $this->session->set_flashdata(
                    'error',
                    'Email ini sudah terdaftar melalui registrasi manual. Silakan masuk menggunakan email dan password Anda.'
                );
                redirect('auth');
                return;
            }

            if (empty($user['google_id'])) {
                $this->Auth_model->link_google_account($user['id_user'], $google_id);
                $user['google_id'] = $google_id;
                $user['login_type'] = 'google';
            }
        }

        $this->Auth_model->insert_successful_login($this->_get_real_ip(), $email);
        $this->setup_session($user, 2592000);
        $this->Activity_model->log($user['id_user'], $user['username'], 'login', null, null, 'Login via Google OAuth');
        redirect(base_url());
    }

    // --- BAGIAN FORGOT / RESET PASSWORD ---

    public function forgot_password() {
        $this->_ensure_session();
        if ($this->session->userdata('user_logged_in')) {
            redirect(base_url());
        }
        $this->load->view('forgot_password');
    }

    public function send_reset_link() {
        $this->_ensure_session();

        $ip_address = $this->_get_real_ip();

        // Rate limit: max 3 request reset per jam per IP
        if (!$this->Auth_model->check_rate_limit($ip_address, 'forgot_password', 3, 60)) {
            $this->session->set_flashdata('error', 'Terlalu banyak permintaan reset password. Coba lagi nanti.');
            redirect('auth/forgot_password');
            return;
        }

        $email = trim($this->input->post('email', true));

        if (empty($email)) {
            $this->session->set_flashdata('error', 'Masukkan alamat email terlebih dahulu.');
            redirect('auth/forgot_password');
            return;
        }

        $user = $this->Auth_model->get_user_by_email($email);

        // Anti-enumeration: pesan sama baik email terdaftar ataupun tidak
        $this->Auth_model->log_rate_limit_action($ip_address, 'forgot_password');
        $this->session->set_flashdata('success', 'Jika email terdaftar, tautan reset password telah dikirim ke email kamu.');

        if (!$user) {
            redirect('auth/forgot_password');
            return;
        }

        $token = $this->Auth_model->create_reset_token($email);
        $this->_send_reset_mail($email, $token);

        $this->Activity_model->log($user['id_user'], $user['username'], 'security', null, null, 'Lupa password: tautan reset dikirim ke email');

        redirect('auth/forgot_password');
    }

    /**
     * VERIFIKASI EMAIL: menerima link dari email, menandai user terverifikasi.
     */
    public function verify_email($token = null) {
        $this->_ensure_session();

        if (empty($token)) {
            show_404();
        }

        $user = $this->Auth_model->verify_email_token($token);

        if (!$user) {
            $this->session->set_flashdata('error', 'Tautan verifikasi email tidak valid atau sudah kedaluwarsa.');
            redirect('auth');
            return;
        }

        $this->Activity_model->log($user['id_user'], $user['username'], 'security', null, null, 'Email berhasil diverifikasi');

        // Update data session bila user sudah login, agar banner verifikasi hilang
        $session_data = $this->session->userdata('user_logged_in');
        if ($session_data) {
            $session_data['email_verified'] = 1;
            $this->session->set_userdata('user_logged_in', $session_data);
        }

        // Bersihkan pending verifikasi pasca-daftar bila email cocok
        $pending_email = $this->session->userdata('pending_email_verification');
        if ($pending_email && strtolower($pending_email) === strtolower($user['email'])) {
            $this->session->unset_userdata('pending_email_verification');
        }

        // Tampilkan halaman sukses: "Email telah diverifikasi, kembali ke halaman utama untuk login"
        $this->load->view('verify_success');
    }

    /**
     * KIRIM ULANG: email verifikasi (POST saja, rate-limited 3x/jam per email).
     */
    public function resend_verification() {
        $this->_ensure_session();

        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }

        $ip_address = $this->_get_real_ip();

        // Ambil email: dari user yang login, atau dari alur "pending verifikasi" pasca-daftar
        $session_data = $this->session->userdata('user_logged_in');
        $pending_email = $this->session->userdata('pending_email_verification');
        $email = isset($session_data['email']) && !empty($session_data['email']) ? $session_data['email'] : $pending_email;
        if (empty($email)) {
            redirect('auth');
            return;
        }

        if (!$this->Auth_model->check_rate_limit($ip_address, 'resend_verification', 3, 60, $email)) {
            $this->session->set_flashdata('error', 'Terlalu sering mengirim ulang. Coba lagi 1 jam lagi.');
            redirect($pending_email ? 'auth/check_email' : 'auth');
            return;
        }
        $this->Auth_model->log_rate_limit_action($ip_address, 'resend_verification', $email);

        $user_id = isset($session_data['user_id']) ? $session_data['user_id'] : null;
        if (empty($user_id)) {
            $user = $this->Auth_model->get_user_by_email($email);
            if (!$user) {
                redirect('auth');
                return;
            }
            $user_id = $user['id_user'];
        }

        $token = bin2hex(random_bytes(32));
        $this->Auth_model->set_email_verification_token($user_id, $token);

        $sent = $this->_send_verification_mail($email, $user_id, $token);

        $msg = $sent ? 'Email verifikasi baru dikirim. Cek inbox (termasuk folder spam).' : 'Gagal mengirim email verifikasi. Coba lagi nanti.';
        $this->session->set_flashdata($sent ? 'success' : 'error', $msg);

        redirect($pending_email ? 'auth/check_email' : base_url());
    }

    /**
     * HALAMAN PASCADAFTAR: instruksi "cek email kamu".
     * Hanya dapat diakses jika session memuat pending_email_verification.
     * Jika sudah verified → auto redirect ke login.
     */
    public function check_email() {
        $this->_ensure_session();

        $email = $this->session->userdata('pending_email_verification');
        if (empty($email)) {
            redirect('auth');
            return;
        }

        $user = $this->Auth_model->get_user_by_email($email);
        if (!$user || $user['login_type'] !== 'regular') {
            $this->session->unset_userdata('pending_email_verification');
            redirect('auth');
            return;
        }

        if ((int) $user['email_verified'] === 1) {
            $this->session->unset_userdata('pending_email_verification');
            $this->session->set_flashdata('success', 'Email kamu sudah terverifikasi! Silakan masuk.');
            redirect('auth');
            return;
        }

        $ip = $this->_get_real_ip();
        $count = $this->Auth_model->count_rate_limit($ip, 'resend_verification', 60, $email);
        $data['email_masked'] = $this->_mask_email($email);
        $data['resend_remaining'] = max(0, 3 - $count);
        $data['resend_available_at'] = null;
        if ($data['resend_remaining'] === 0) {
            $data['resend_available_at'] = $this->Auth_model->rate_limit_next_available('resend_verification', 60, $email);
        }

        $this->load->view('check_email', $data);
    }

    /**
     * STATUS (AJAX): dipakai halaman check_email untuk auto-redirect & tombol kirim ulang.
     */
    public function check_email_status() {
        $this->_ensure_session();

        $email = $this->session->userdata('pending_email_verification');
        $verified = 0;
        $can_resend = 0;
        $next_available = null;

        if (!empty($email)) {
            $user = $this->Auth_model->get_user_by_email($email);
            $verified = ($user && (int) $user['email_verified'] === 1) ? 1 : 0;

            $ip = $this->_get_real_ip();
            $count = $this->Auth_model->count_rate_limit($ip, 'resend_verification', 60, $email);
            $can_resend = (3 - $count) > 0 ? 1 : 0;
            if (!$can_resend) {
                $next_available = $this->Auth_model->rate_limit_next_available('resend_verification', 60, $email);
            }
        }

        $this->output->set_status_header(200);
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode(array(
            'verified'        => (int) $verified,
            'can_resend'      => (int) $can_resend,
            'next_available'  => $next_available,
        )));
    }

    /**
     * Sensor alamat email: msabilshahputra@gmail.com → ms***@gmail.com
     */
    private function _mask_email($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'a***@***';
        }
        list($local, $domain) = explode('@', $email, 2);
        $keep = strlen($local) > 4 ? 2 : 1;
        $masked_local = substr($local, 0, $keep) . str_repeat('*', max(3, strlen($local) - $keep));
        return $masked_local . '@' . $domain;
    }

    /**
     * HELPER PRIVAT: kirim email tautan reset password.
     */
    private function _send_reset_mail($to, $token) {
        $this->load->library('mailer');

        $link = base_url('auth/reset_password/' . $token);
        $body = "Kami menerima permintaan reset password untuk akun kamu.<br><br>"
              . "Tautan berikut berlaku <b>1 jam</b> dan hanya bisa dipakai <b>sekali</b>:<br>"
              . "Jika bukan kamu yang meminta, abaikan email ini — password kamu tidak berubah.";

        $html = $this->mailer->template('Reset Password', $body, array(
            'url'  => $link,
            'text' => 'Reset Password',
        ));

        return $this->mailer->send($to, 'Reset Password', $html);
    }

    /**
     * HELPER PRIVAT: kirim email verifikasi awal / ulang.
     */
    private function _send_verification_mail($to, $user_id, $token) {
        $this->load->library('mailer');

        $link = base_url('auth/verify_email/' . $token);
        $body = "Selamat datang di PaddockID!<br><br>"
              . "Selesaikan verifikasi email kamu untuk mengaktifkan posting & komentar:<br>"
              . "Tautan berlaku <b>24 jam</b> dan hanya untuk email ini.";

        $html = $this->mailer->template('Verifikasi Email', $body, array(
            'url'  => $link,
            'text' => 'Verifikasi Email',
        ));

        return $this->mailer->send($to, 'Verifikasi Email', $html);
    }

    public function reset_password($token = null) {
        $this->_ensure_session();
        if ($this->session->userdata('user_logged_in')) {
            redirect(base_url());
        }

        if (empty($token)) {
            show_404();
        }

        $data['token'] = $token;
        $data['valid'] = $this->Auth_model->validate_reset_token($token);

        if (!$data['valid']) {
            $this->load->view('reset_password', $data);
            return;
        }

        $this->load->view('reset_password', $data);
    }

    public function update_password_process() {
        $this->_ensure_session();

        $token    = $this->input->post('token', true);
        $password = $this->input->post('password', true);
        $confirm  = $this->input->post('confirm_password', true);

        if (empty($token)) {
            show_404();
        }

        $row = $this->Auth_model->validate_reset_token($token);
        if (!$row) {
            $this->session->set_flashdata('error', 'Tautan reset tidak valid atau sudah kedaluwarsa.');
            redirect('auth/reset_password/' . $token);
            return;
        }

        if ($password !== $confirm) {
            $this->session->set_flashdata('error', 'Konfirmasi password tidak cocok.');
            redirect('auth/reset_password/' . $token);
            return;
        }

        $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
        if (!preg_match($pattern, $password)) {
            $this->session->set_flashdata('error', 'Password minimal 8 karakter dengan huruf besar, kecil, angka, dan simbol (@\$!%*?&).');
            redirect('auth/reset_password/' . $token);
            return;
        }

        $this->Auth_model->update_password($row['email'], $password);
        $this->Auth_model->mark_token_used($token);

        $this->session->set_flashdata('success', 'Password berhasil diubah! Silakan masuk dengan password baru.');
        redirect('auth');
    }

    /**
     * PROSES LOGOUT (wajib POST + token CSRF — anti logout CSRF via GET)
     */
    public function logout()
    {
        if (!$this->input->is_ajax_request() && $this->input->method() !== 'post') {
            show_404();
            return;
        }

        $this->_ensure_session();
        $session_data = $this->session->userdata('user_logged_in');
        if ($session_data) {
            $this->Activity_model->log($session_data['user_id'], $session_data['username'], 'logout', null, null, 'Logout');
        }
        $this->session->unset_userdata('user_logged_in');
        $this->session->sess_destroy();
        delete_cookie('session_exp');
        redirect('auth');
    }

/**
     * HELPER PRIVAT: INISIALISASI SESSION (Sudah Mendukung Border Aktif)
     */
    private function setup_session($user, $lifetime = 604800)
    {
        $this->_ensure_session();
        $session_data = [
            'user_id'     => $user['id_user'],
            'username'    => $user['username'],
            'fullname'    => $user['display_name'],
            'email'       => $user['email'],
            'profile_pic' => $user['avatar'] ?? 'default.jpg',
            'border'         => $user['border_image'] ?? null,
            'login_type'     => $user['login_type'],
            'role'           => $user['role'] ?? 'user',
            'email_verified' => $user['email_verified'] ?? 0,
            'verified'       => $user['verified'] ?? 0,
            'coins'          => $user['coins'] ?? 0,
            'logged_in'      => true
        ];
        $this->session->set_userdata('user_logged_in', $session_data);
        // Cegah session fixation: regenerasi session ID setelah login sukses
        $this->session->sess_regenerate(TRUE);
        $this->input->set_cookie('session_exp', (string) $lifetime, $lifetime);
        $this->db->where('id_user', $user['id_user'])->update('users', ['last_activity' => date('Y-m-d H:i:s')]);
    }

    /**
     * HELPER PRIVAT: GENERATE USERNAME UNIK
     */
    private function generate_unique_username($username)
    {
        $base = $username;
        $i = 1;
        while ($this->Auth_model->is_username_exists($username)) {
            $username = $base . $i;
            $i++;
        }
        return $username;
    }
}