<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Data statistik Game "Guess the Driver".
 *
 * Counter permainan per hari kalender. Identity: 'u:<user_id>' bila login,
 * 'ip:<address>' bila tamu — konsisten dengan pola throttle di app.
 */
class Game_model extends CI_Model {

    private $_table = 'game_daily_plays';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Identity kunci counter: pakai user_id bila login, jika tidak pakai IP.
     */
    public function build_identity($user_id = null) {
        if (!empty($user_id)) {
            return 'u:' . (int) $user_id;
        }
        $ci =& get_instance();
        $ci->load->helper('ip');
        return 'ip:' . get_real_ip();
    }

    /**
     * Jumlah permainan yang sudah dimulai oleh identity pada tanggal tertentu
     * (default: hari ini).
     */
    public function count_today($identity, $date = null) {
        $date = $date ?: date('Y-m-d');
        $this->db->where('identity', $identity);
        $this->db->where('play_date', $date);
        $row = $this->db->get($this->_table)->row_array();
        return $row ? (int) $row['play_count'] : 0;
    }

    /**
     * Naikkan counter permainan untuk identity pada tanggal tertentu.
     * Gunakan INSERT ... ON DUPLICATE KEY UPDATE agar aman 1 baris/identity/date.
     */
    public function increment($identity, $date = null) {
        $date = $date ?: date('Y-m-d');
        $sql = "INSERT INTO {$this->_table} (`identity`, `play_date`, `play_count`)
                VALUES (?, ?, 1)
                ON DUPLICATE KEY UPDATE `play_count` = `play_count` + 1";
        return $this->db->query($sql, [$identity, $date]);
    }

    /**
     * Award coins to a user for winning the game
     * @param int $user_id User ID
     * @param int $amount Amount of coins to award
     * @return bool
     */
    public function award_coins($user_id, $amount) {
        if ($amount <= 0) return false;
        return $this->db->where('id_user', (int) $user_id)
            ->set('coins', 'coins + ' . (int) $amount, FALSE)
            ->update('users');
    }
}