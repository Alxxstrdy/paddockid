<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Auth_model');
        $this->load->model('Activity_model');

        if (!$this->session->userdata('user_logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('auth');
        }
    }

    public function index()
    {
        $session_data = $this->session->userdata('user_logged_in');
        $user_id = $session_data['user_id'];

        $this->db->select('id_user, username, display_name, email, login_type, google_id, created_at');
        $this->db->where('id_user', $user_id);
        $data['user'] = $this->db->get('users')->row_array();
        $data['title'] = 'Pengaturan Akun | PaddockID';

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('settings', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function change_password()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        $user_id = $session_data['user_id'];

        $current_password = $this->input->post('current_password', true);
        $new_password = $this->input->post('new_password', true);
        $confirm_password = $this->input->post('confirm_password', true);

        // Get current user
        $user = $this->db->where('id_user', $user_id)->get('users')->row_array();

        // Check if user has a password
        if (empty($user['password'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Akun kamu belum memiliki password. Silakan atur password terlebih dahulu.'
                ]));
        }

        // Verify current password
        if (!password_verify($current_password, $user['password'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Password lama tidak sesuai.'
                ]));
        }

        // Validate new password
        if (strlen($new_password) < 8) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error', 'message' => 'Password baru minimal 8 karakter.'
            ]));
        }
        if (!preg_match('/[A-Z]/', $new_password)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error', 'message' => 'Password harus mengandung huruf besar.'
            ]));
        }
        if (!preg_match('/[a-z]/', $new_password)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error', 'message' => 'Password harus mengandung huruf kecil.'
            ]));
        }
        if (!preg_match('/[0-9]/', $new_password)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error', 'message' => 'Password harus mengandung angka.'
            ]));
        }
        if (!preg_match('/[@$!%*?&]/', $new_password)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error', 'message' => 'Password harus mengandung simbol (@$!%*?&).'
            ]));
        }
        if ($new_password !== $confirm_password) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error', 'message' => 'Konfirmasi password tidak cocok.'
            ]));
        }
        if ($current_password === $new_password) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error', 'message' => 'Password baru tidak boleh sama dengan password lama.'
            ]));
        }

        $this->Auth_model->change_password($user_id, $new_password);
        $this->Activity_model->log($user_id, $session_data['username'], 'change_password', null, null, 'Password diubah');

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => 'Password berhasil diubah!'
            ]));
    }

    public function set_password()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        $user_id = $session_data['user_id'];

        $user = $this->db->where('id_user', $user_id)->get('users')->row_array();

        if (!empty($user['password'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Akun kamu sudah memiliki password. Gunakan form ubah password.'
                ]));
        }

        $new_password = $this->input->post('new_password', true);
        $confirm_password = $this->input->post('confirm_password', true);

        // Validate
        if (strlen($new_password) < 8) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error', 'message' => 'Password minimal 8 karakter.'
            ]));
        }
        if (!preg_match('/[A-Z]/', $new_password)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error', 'message' => 'Password harus mengandung huruf besar.'
            ]));
        }
        if (!preg_match('/[a-z]/', $new_password)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error', 'message' => 'Password harus mengandung huruf kecil.'
            ]));
        }
        if (!preg_match('/[0-9]/', $new_password)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error', 'message' => 'Password harus mengandung angka.'
            ]));
        }
        if (!preg_match('/[@$!%*?&]/', $new_password)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error', 'message' => 'Password harus mengandung simbol (@$!%*?&).'
            ]));
        }
        if ($new_password !== $confirm_password) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error', 'message' => 'Konfirmasi password tidak cocok.'
            ]));
        }

        $this->Auth_model->set_password($user_id, $new_password);
        $this->Activity_model->log($user_id, $session_data['username'], 'set_password', null, null, 'Password dibuat untuk akun Google');

        // Update session login_type
        $session_data['login_type'] = 'both';
        $this->session->set_userdata('user_logged_in', $session_data);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => 'Password berhasil dibuat! Sekarang kamu bisa login dengan email dan Google.'
            ]));
    }

    public function change_email()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        $user_id = $session_data['user_id'];

        $new_email = trim($this->input->post('new_email', true));
        $password = $this->input->post('password', true);

        $user = $this->db->where('id_user', $user_id)->get('users')->row_array();

        // Verify password
        if (!empty($user['password'])) {
            if (!password_verify($password, $user['password'])) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'Password tidak sesuai.'
                    ]));
            }
        }

        // Validate email
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error', 'message' => 'Format email tidak valid.'
            ]));
        }

        if ($new_email === $user['email']) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error', 'message' => 'Email baru sama dengan email saat ini.'
            ]));
        }

        if ($this->Auth_model->is_email_exists($new_email, $user_id)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error', 'message' => 'Email sudah digunakan oleh akun lain.'
            ]));
        }

        $this->Auth_model->change_email($user_id, $new_email);
        $this->Activity_model->log($user_id, $session_data['username'], 'change_email', null, null, 'Email diubah');

        // Update session
        $session_data['email'] = $new_email;
        $this->session->set_userdata('user_logged_in', $session_data);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => 'Email berhasil diubah!'
            ]));
    }

    public function unlink_google()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        $user_id = $session_data['user_id'];

        $user = $this->db->where('id_user', $user_id)->get('users')->row_array();

        if (empty($user['google_id'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Akun kamu tidak terhubung dengan Google.'
                ]));
        }

        // If user has no password, they can't unlink
        if (empty($user['password'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Kamu harus membuat password terlebih dahulu sebelum memutuskan hubungan Google.'
                ]));
        }

        $this->Auth_model->unlink_google($user_id);
        $this->Activity_model->log($user_id, $session_data['username'], 'unlink_google', null, null, 'Google account unlinked');

        // Update session
        $session_data['login_type'] = 'regular';
        $this->session->set_userdata('user_logged_in', $session_data);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => 'Hubungan dengan Google berhasil diputuskan.'
            ]));
    }

    public function delete_account()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        $user_id = $session_data['user_id'];
        $username = $this->input->post('confirm_username', true);
        $password = $this->input->post('password', true);

        $user = $this->db->where('id_user', $user_id)->get('users')->row_array();

        // Verify username
        if ($username !== $user['username']) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Username tidak sesuai.'
                ]));
        }

        // Verify password (if user has one)
        if (!empty($user['password'])) {
            if (!password_verify($password, $user['password'])) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'Password tidak sesuai.'
                    ]));
            }
        }

        $this->Auth_model->delete_user_data($user_id);
        $this->session->sess_destroy();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => 'Akun berhasil dihapus.'
            ]));
    }
}
