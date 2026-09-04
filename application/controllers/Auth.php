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
        $this->config->set_item('sess_expiration', $remember ? 2592000 : 604800);

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
            $this->session->set_flashdata('error', 'Akun anda ditangguhkan.');
            redirect('auth');
            return;
        }

        if ($user && $user['status'] === 'active') {
            if ($user['login_type'] === 'regular' && password_verify($password, $user['password'])) {

                $this->Auth_model->clear_failed_attempts($ip_address, $identity);
                $this->Auth_model->insert_successful_login($ip_address, $identity);

                $this->setup_session($user);
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

        if ($this->Auth_model->register_google_user($data)) {
            $this->Auth_model->log_rate_limit_action($ip_address, 'register');
            $this->Activity_model->log(null, $username, 'register', null, null, 'Akun baru dibuat: ' . $email);
            $this->session->set_flashdata('success', 'Akun berhasil dibuat! Silakan masuk.');
            redirect('auth');
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

        $this->_ensure_session();

        $client = new Google_Client();
        $client->setClientId(getenv('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(base_url('auth/google_callback'));

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

            if ($image_content !== false) {
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
                $this->session->set_flashdata('error', 'Akun Anda ditangguhkan.');
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
        $this->setup_session($user);
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

        if (!$user) {
            // Tetap kasih sukses biar attacker ga tau email terdaftar apa ngga
            $this->session->set_flashdata('success', 'Jika email terdaftar, tautan reset password akan dikirim.');
            redirect('auth/forgot_password');
            return;
        }

        $token = $this->Auth_model->create_reset_token($email);
        $reset_url = base_url('auth/reset_password/' . $token);

        // Kirim email via CI Email library
        $this->load->library('email');
        $this->load->config('email', true);
        $mail_configured = $this->config->item('smtp_host', 'email');

        $email_sent = false;

        if ($mail_configured) {
            $this->email->from($this->config->item('smtp_user', 'email'), 'PaddockID');
            $this->email->to($email);
            $this->email->subject('Reset Password - PaddockID');
            $this->email->message("
                <html>
                <body style='font-family: sans-serif; background: #05070c; color: #e2e8f0; padding: 40px;'>
                    <div style='max-width: 480px; margin: auto; background: rgba(15,22,38,0.9); border-radius: 16px; padding: 32px; border: 1px solid rgba(255,255,255,0.06);'>
                        <h2 style='color: #ef4444; font-size: 18px; margin-bottom: 16px;'>Reset Password</h2>
                        <p style='font-size: 13px; line-height: 1.6; margin-bottom: 20px;'>Klik tombol di bawah untuk mereset password akun PaddockID kamu.</p>
                        <a href='{$reset_url}' style='display: inline-block; background: #ef4444; color: white; text-decoration: none; padding: 12px 28px; border-radius: 12px; font-size: 13px; font-weight: 600;'>Reset Password</a>
                        <p style='font-size: 11px; color: #64748b; margin-top: 20px;'>Tautan ini berlaku selama 1 jam. Abaikan email ini jika kamu tidak meminta reset password.</p>
                    </div>
                </body>
                </html>
            ");
            $this->email->set_mailtype('html');

            if ($this->email->send()) {
                $email_sent = true;
            }
        }

        if ($email_sent) {
            $this->Auth_model->log_rate_limit_action($ip_address, 'forgot_password');
            $this->session->set_flashdata('success', 'Tautan reset password telah dikirim ke email kamu.');
        } else {
            log_coded_error('PAU-2003', 'Email reset password gagal terkirim ke: ' . $email);
            $this->session->set_flashdata('error', 'Gagal mengirim email. Silakan coba lagi nanti.');
        }

        redirect('auth/forgot_password');
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
     * PROSES LOGOUT
     */
    public function logout()
    {
        $this->_ensure_session();
        $session_data = $this->session->userdata('user_logged_in');
        if ($session_data) {
            $this->Activity_model->log($session_data['user_id'], $session_data['username'], 'logout', null, null, 'Logout');
        }
        $this->session->unset_userdata('user_logged_in');
        $this->session->sess_destroy();
        redirect('auth');
    }

/**
     * HELPER PRIVAT: INISIALISASI SESSION (Sudah Mendukung Border Aktif)
     */
    private function setup_session($user)
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
            'logged_in'      => true
        ];
        $this->session->set_userdata('user_logged_in', $session_data);
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