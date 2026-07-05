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
    }

    /**
     * Helper Privat untuk Mendapatkan Real IP Address dari Client
     * Mengamankan deteksi IP dari Cloudflare, Ngrok, atau Load Balancer
     */
    private function _get_real_ip()
    {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        
        if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP'];
        }

        return $this->input->ip_address();
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
        
        $ip_address = $this->_get_real_ip();

        // Set config BEFORE session loads so Remember Me cookie works
        if ($remember) {
            $this->config->set_item('sess_expiration', 2592000);
            $this->config->set_item('sess_expire_on_close', FALSE);
        }

        $this->_ensure_session();

        // 1. Cek limit percobaan gagal via Model
        $attempts = $this->Auth_model->count_failed_attempts($ip_address);

        if ($attempts >= 3) {
            $this->session->set_flashdata('error', 'Terlalu banyak percobaan login. Silakan tunggu 10 menit lagi.');
            redirect('auth');
            return;
        }

        // 2. Ambil data user menggunakan method model baru
        $user = $this->Auth_model->get_user_by_identity($identity);

        if ($user && $user['status'] === 'active') {
            if ($user['login_type'] === 'regular' && password_verify($password, $user['password'])) {
                
                $this->Auth_model->clear_failed_attempts($ip_address);

                // Set Session 
                $this->setup_session($user);
                
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
            'username'     => $username,
            'display_name' => $username, // Diisi username sebagai nama tampilan default
            'email'        => $email,
            'password'     => password_hash($password, PASSWORD_BCRYPT), // Enkripsi murni server
            'login_type'   => 'regular',
            'status'       => 'active',
            'verified'     => 0,
            'avatar'       => 'default.jpg',
            'created_at'   => date('Y-m-d H:i:s')
        ];

        if ($this->Auth_model->register_google_user($data)) {
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
        $client->setClientId('680175235855-cg1b9h9eseoqjl2occpnt55qnos03lql.apps.googleusercontent.com');
        $client->setClientSecret('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');
        $client->setRedirectUri(base_url('auth/google_callback'));
        $client->addScope('email');
        $client->addScope('profile');

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
        $this->config->set_item('sess_expire_on_close', FALSE);

        $this->_ensure_session();

        $client = new Google_Client();
        $client->setClientId('680175235855-cg1b9h9eseoqjl2occpnt55qnos03lql.apps.googleusercontent.com');
        $client->setClientSecret('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');
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
                'google_id'    => $google_id,
                'username'     => $username,
                'display_name' => $full_name,
                'avatar'       => $profile_pic, // Menggunakan variabel profile_pic hasil download
                'email'        => $email,
                'password'     => null,
                'login_type'   => 'google',
                'status'       => 'active',
                'verified'     => 0,
                'created_at'   => date('Y-m-d H:i:s')
            ];

            $insert_id = $this->Auth_model->register_google_user($data);
            $data['id_user'] = $insert_id;
            $user = $data;

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

        $this->setup_session($user);
        redirect(base_url());
    }

    /**
     * PROSES LOGOUT
     */
    public function logout()
    {
        $this->_ensure_session();
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
            'border'      => $user['border_image'] ?? null,
            'login_type'  => $user['login_type'],
            'logged_in'   => true
        ];
        $this->session->set_userdata('user_logged_in', $session_data);
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