<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

    // --- BAGIAN SECURITY / BRUTE FORCE PROTECTION ---

    /**
     * Cek batas percobaan gagal dari IP dan/atau identity (username/email)
     * dalam 10 menit terakhir
     */
    public function count_failed_attempts($ip_address, $identity = null) {
        $time_window = date('Y-m-d H:i:s', strtotime('-10 minutes'));

        $this->db->where('attempted_at >', $time_window);
        $this->db->where('success', 0);

        if (func_num_args() === 2 && $identity !== null) {
            $this->db->group_start();
            $this->db->where('ip_address', $ip_address);
            $this->db->or_where('identity', $identity);
            $this->db->group_end();
        } else {
            $this->db->where('ip_address', $ip_address);
        }

        return $this->db->count_all_results('login_attempts');
    }

    /**
     * Mencatat setiap kegagalan login
     */
    public function insert_failed_attempt($ip_address, $identity) {
        $data = [
            'ip_address' => $ip_address,
            'identity' => $identity,
            'success' => 0,
            'attempted_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->insert('login_attempts', $data);
    }

    /**
     * Log successful login
     */
    public function insert_successful_login($ip_address, $identity) {
        $data = [
            'ip_address' => $ip_address,
            'identity' => $identity,
            'success' => 1,
            'attempted_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->insert('login_attempts', $data);
    }

    /**
     * Menghapus log gagal jika user akhirnya berhasil login
     */
    public function clear_failed_attempts($ip_address, $identity = null) {
        $this->db->group_start();
        $this->db->where('ip_address', $ip_address);
        if ($identity !== null) {
            $this->db->or_where('identity', $identity);
        }
        $this->db->group_end();
        return $this->db->delete('login_attempts');
    }

    // --- BAGIAN RATE LIMITING ---

    /**
     * Cek apakah action dari IP ini sudah melebihi batas dalam window tertentu
     */
    public function check_rate_limit($ip_address, $action, $max_attempts, $window_minutes = 60, $identity = null) {
        $time_window = date('Y-m-d H:i:s', strtotime('-' . $window_minutes . ' minutes'));

        if ($identity !== null) {
            $this->db->where('identity', $identity);
        } else {
            $this->db->where('ip_address', $ip_address);
        }
        $this->db->where('action', $action);
        $this->db->where('created_at >', $time_window);

        $count = $this->db->count_all_results('rate_limits');
        return $count < $max_attempts;
    }

    /**
     * Jumlah aksi yang tercatat dalam window (untuk hitung sisa kuota di UI).
     */
    public function count_rate_limit($ip_address, $action, $window_minutes = 60, $identity = null) {
        $time_window = date('Y-m-d H:i:s', strtotime('-' . $window_minutes . ' minutes'));

        if ($identity !== null) {
            $this->db->where('identity', $identity);
        } else {
            $this->db->where('ip_address', $ip_address);
        }
        $this->db->where('action', $action);
        $this->db->where('created_at >', $time_window);

        return (int) $this->db->count_all_results('rate_limits');
    }

    /**
     * Kapan kuota aksi berikutnya tersedia lagi (created_at terlama dalam window + window).
     */
    public function rate_limit_next_available($action, $window_minutes = 60, $identity = null) {
        $time_window = date('Y-m-d H:i:s', strtotime('-' . $window_minutes . ' minutes'));

        if ($identity !== null) {
            $this->db->where('identity', $identity);
        }
        $this->db->where('action', $action);
        $this->db->where('created_at >', $time_window);
        $this->db->order_by('created_at', 'asc');
        $this->db->limit(1);

        $row = $this->db->get('rate_limits')->row_array();
        if (!$row) return null;

        return date('Y-m-d H:i:s', strtotime($row['created_at']) + $window_minutes * 60);
    }

    /**
     * Catat action ke tabel rate_limits
     */
    public function log_rate_limit_action($ip_address, $action, $identity = null) {
        return $this->db->insert('rate_limits', [
            'ip_address' => $ip_address,
            'action'     => $action,
            'identity'   => $identity,
        ]);
    }


    // --- BAGIAN USER MANAGEMENT & OAUTH ---

/**
     * Ambil data user berdasarkan email atau username + gabung data border aktif
     */
    public function get_user_by_identity($identity)
    {
        $this->db->select('u.*, b.image_url as border_image');
        $this->db->from('users u');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left'); // LEFT JOIN agar user tanpa border tetap bisa login
        $this->db->group_start();
        $this->db->where('u.username', $identity);
        $this->db->or_where('u.email', $identity);
        $this->db->group_end();
        
        return $this->db->get()->row_array();
    }

    /**
     * Ambil data user berdasarkan google_id + gabung data border aktif
     */
    public function get_user_by_google($google_id, $email)
    {
        $this->db->select('u.*, b.image_url as border_image');
        $this->db->from('users u');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->group_start();
        $this->db->where('u.google_id', $google_id);
        $this->db->or_where('u.email', $email);
        $this->db->group_end();

        return $this->db->get()->row_array();
    }

    /**
     * Mendaftarkan user baru secara otomatis via Google OAuth
     */
    public function register_google_user($data) {
        do {
            $id_user = (string) random_int(100000000, 999999999);
        } while ($this->db->get_where('users', ['id_user' => $id_user])->num_rows() > 0);
        $data['id_user'] = $id_user;
        if ($this->db->insert('users', $data)) {
            return $id_user;
        }
        return false;
    }

    /**
     * Menghubungkan akun reguler lama dengan Google ID jika emailnya sama
     */
    public function link_google_account($id_user, $google_id) {
        $this->db->where('id_user', $id_user);
        return $this->db->update('users', [
            'google_id' => $google_id,
            'login_type' => 'google'
        ]);
    }

    /**
     * Helper untuk memastikan username bentukan Google tidak kembar di DB
     */
    public function is_username_exists($username) {
        $this->db->where('username', $username);
        return $this->db->count_all_results('users') > 0;
    }

    // --- BAGIAN FORGOT / RESET PASSWORD ---

    public function get_user_by_email($email) {
        $this->db->select('u.*, b.image_url as border_image');
        $this->db->from('users u');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->where('u.email', $email);
        $this->db->where('u.login_type', 'regular');
        $this->db->where('u.status', 'active');
        return $this->db->get()->row_array();
    }

    public function create_reset_token($email) {
        $token = bin2hex(random_bytes(64));
        $hash = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Hapus token lama untuk email ini
        $this->db->where('email', $email);
        $this->db->delete('password_resets');

        $this->db->insert('password_resets', [
            'email'      => $email,
            'token'      => $hash,
            'expires_at' => $expires,
        ]);

        return $token;
    }

    public function validate_reset_token($token) {
        $hash = hash('sha256', $token);
        $row = $this->db->get_where('password_resets', [
            'token'      => $hash,
            'used'       => 0,
        ])->row_array();

        if (!$row) return null;
        if (strtotime($row['expires_at']) < time()) return null;

        return $row;
    }

    public function mark_token_used($token) {
        $hash = hash('sha256', $token);
        $this->db->where('token', $hash);
        return $this->db->update('password_resets', ['used' => 1]);
    }

    public function update_password($email, $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $this->db->where('email', $email);
        return $this->db->update('users', ['password' => $hash]);
    }

    // --- BAGIAN EMAIL VERIFICATION ---

    public function set_email_verification_token($user_id, $token) {
        $this->db->where('id_user', $user_id);
        return $this->db->update('users', [
            'email_token'         => hash('sha256', $token),
            'email_token_expires' => date('Y-m-d H:i:s', strtotime('+24 hours')),
        ]);
    }

    /**
     * Validasi token verifikasi email. Token sekali pakai & kedaluwarsa 24 jam.
     * Returns user row array jika valid, null jika tidak.
     */
    public function verify_email_token($token) {
        $hash = hash('sha256', $token);

        $row = $this->db
            ->where('email_token', $hash)
            ->where('email_token_expires >', date('Y-m-d H:i:s'))
            ->get('users')
            ->row_array();

        if (!$row) return null;

        $this->db->where('id_user', $row['id_user']);
        $this->db->update('users', [
            'email_verified'      => 1,
            'email_token'         => null,
            'email_token_expires' => null,
        ]);

        return $row;
    }

    public function is_email_verified($user_id) {
        $this->db->select('email_verified');
        $this->db->where('id_user', $user_id);
        $row = $this->db->get('users')->row_array();
        return $row ? (int) $row['email_verified'] === 1 : false;
    }

    public function get_all_teams()
    {
        return $this->db->get('team')->result_array();
    }

    public function change_password($user_id, $new_password)
    {
        $hash = password_hash($new_password, PASSWORD_BCRYPT);
        $this->db->where('id_user', $user_id);
        return $this->db->update('users', ['password' => $hash]);
    }

    public function set_password($user_id, $new_password)
    {
        return $this->change_password($user_id, $new_password);
    }

    public function is_email_exists($email, $exclude_user_id = null)
    {
        $this->db->where('email', $email);
        if ($exclude_user_id !== null) {
            $this->db->where('id_user !=', $exclude_user_id);
        }
        return $this->db->count_all_results('users') > 0;
    }

    public function change_email($user_id, $new_email)
    {
        $this->db->where('id_user', $user_id);
        return $this->db->update('users', ['email' => $new_email]);
    }

    public function unlink_google($user_id)
    {
        $this->db->where('id_user', $user_id);
        return $this->db->update('users', [
            'google_id'  => null,
            'login_type' => 'regular'
        ]);
    }

    public function delete_user_data($user_id)
    {
        $tables = ['activity_logs', 'blocked_users', 'chat_messages', 'comment_likes',
                    'follows', 'login_attempts', 'notifications', 'password_resets',
                    'post_comments', 'post_likes', 'post_reports', 'posts',
                    'user_borders', 'user_effects', 'user_reports'];

        foreach ($tables as $table) {
            $this->db->where('id_user', $user_id)->delete($table);
        }

        // also check actor_id in notifications and reporter_id in reports
        $this->db->where('actor_id', $user_id)->delete('notifications');
        $this->db->where('reporter_id', $user_id)->delete('user_reports');
        $this->db->where('reported_id', $user_id)->delete('user_reports');
        $this->db->where('reporter_id', $user_id)->delete('post_reports');
        $this->db->where('blocker_id', $user_id)->delete('blocked_users');
        $this->db->where('blocked_id', $user_id)->delete('blocked_users');
        $this->db->where('id_following', $user_id)->delete('follows');
        $this->db->where('id_followers', $user_id)->delete('follows');

        // DM (FK cascade menangani dm_messages via percakapan dihapus)
        $this->db->where('user_id_a', $user_id)->or_where('user_id_b', $user_id)->delete('dm_conversations');
        $this->db->where('sender_id', $user_id)->delete('dm_messages');

        $this->db->where('id_user', $user_id)->delete('users');
        return true;
    }
}