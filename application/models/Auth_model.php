<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

    // --- BAGIAN SECURITY / BRUTE FORCE PROTECTION ---

    /**
     * Menghitung total percobaan gagal dari IP tertentu dalam 10 menit terakhir
     */
    public function count_failed_attempts($ip_address) {
        $time_window = date('Y-m-d H:i:s', strtotime('-10 minutes'));
        
        $this->db->where('ip_address', $ip_address);
        $this->db->where('attempted_at >', $time_window);
        return $this->db->count_all_results('login_attempts');
    }

    /**
     * Mencatat setiap kegagalan login
     */
    public function insert_failed_attempt($ip_address, $identity) {
        $data = [
            'ip_address' => $ip_address,
            'identity' => $identity,
            'attempted_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->insert('login_attempts', $data);
    }

    /**
     * Menghapus log gagal jika user akhirnya berhasil login
     */
    public function clear_failed_attempts($ip_address) {
        $this->db->where('ip_address', $ip_address);
        return $this->db->delete('login_attempts');
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
        $this->db->insert('users', $data);
        return $this->db->insert_id(); // Kembalikan ID user yang baru masuk
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
    
}