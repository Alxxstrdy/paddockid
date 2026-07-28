<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Border_model extends CI_Model {

    public function get_all() {
        return $this->db->get('borders')->result_array();
    }

    public function get_user_border_ids($user_id) {
        $q = $this->db->select('id_border')
            ->from('user_borders')
            ->where('user_id', $user_id)
            ->get()
            ->result_array();
        return array_column($q, 'id_border');
    }

    public function get_active_border_id($user_id) {
        $q = $this->db->select('border_active')
            ->from('users')
            ->where('id_user', $user_id)
            ->get()
            ->row_array();
        return $q ? $q['border_active'] : null;
    }

    public function equip($user_id, $border_id) {
        return $this->db->where('id_user', $user_id)->update('users', ['border_active' => $border_id]);
    }

    public function remove($user_id) {
        return $this->db->where('id_user', $user_id)->update('users', ['border_active' => null]);
    }

    public function purchase($user_id, $border_id) {
        $exists = $this->db->where('user_id', $user_id)
            ->where('id_border', $border_id)
            ->get('user_borders')
            ->num_rows();
        if ($exists > 0) return true;
        return $this->db->insert('user_borders', [
            'user_id'   => $user_id,
            'id_border' => $border_id
        ]);
    }

    public function get_user_coins($user_id) {
        $q = $this->db->select('coins')
            ->from('users')
            ->where('id_user', $user_id)
            ->get()
            ->row_array();
        return $q ? (int) $q['coins'] : 0;
    }

    public function get_border($border_id) {
        return $this->db->where('id_border', $border_id)
            ->get('borders')
            ->row_array();
    }

    public function get_available_borders() {
        return $this->db->where('is_premium', 0)
            ->or_where('price >', 0)
            ->get('borders')
            ->result_array();
    }

    public function purchase_with_coins($user_id, $border_id) {
        $border = $this->get_border($border_id);
        if (!$border) return ['success' => false, 'message' => 'Border tidak ditemukan.'];

        if ((int) $border['price'] <= 0) {
            return ['success' => false, 'message' => 'Border ini tidak bisa dibeli.'];
        }

        $exists = $this->db->where('user_id', $user_id)
            ->where('id_border', $border_id)
            ->get('user_borders')
            ->num_rows();
        if ($exists > 0) return ['success' => false, 'message' => 'Kamu sudah memiliki border ini.'];

        $coins = $this->get_user_coins($user_id);
        $price = (int) $border['price'];
        if ($coins < $price) {
            return ['success' => false, 'message' => 'Koin tidak cukup. Butuh ' . number_format($price, 0, ',', '.') . ' koin.'];
        }

        $this->db->trans_start();

        $this->db->where('id_user', $user_id)
            ->set('coins', 'coins - ' . $price, FALSE)
            ->update('users');

        $this->db->insert('user_borders', [
            'user_id'   => $user_id,
            'id_border' => $border_id
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            return ['success' => true, 'message' => 'Pembelian berhasil!', 'remaining' => $coins - $price];
        }
        return ['success' => false, 'message' => 'Terjadi kesalahan saat memproses pembelian.'];
    }
}
