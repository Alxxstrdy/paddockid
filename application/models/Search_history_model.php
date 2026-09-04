<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search_history_model extends CI_Model {

    private const MAX_HISTORY = 10;

    public function get_history($user_id, $limit = self::MAX_HISTORY)
    {
        if (empty($user_id)) {
            return [];
        }

        return $this->db->select('id, keyword, created_at')
            ->where('id_user', $user_id)
            ->order_by('created_at', 'DESC')
            ->order_by('id', 'DESC')
            ->limit((int) $limit)
            ->get('search_history')
            ->result_array();
    }

    public function add($user_id, $keyword)
    {
        $user_id  = (int) $user_id;
        $keyword  = trim($keyword);

        if ($user_id <= 0 || empty($keyword)) {
            return;
        }

        $this->db->where('id_user', $user_id)
            ->where('keyword', $keyword)
            ->delete('search_history');

        $this->db->insert('search_history', [
            'id_user'    => $user_id,
            'keyword'    => $keyword,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $excess = $this->db->select('id')
            ->where('id_user', $user_id)
            ->order_by('created_at', 'DESC')
            ->order_by('id', 'DESC')
            ->limit(PHP_INT_MAX, self::MAX_HISTORY)
            ->get('search_history')
            ->result_array();

        foreach ($excess as $row) {
            $this->db->where('id', $row['id'])
                ->where('id_user', $user_id)
                ->delete('search_history');
        }
    }

    public function clear($user_id)
    {
        if (empty($user_id)) {
            return;
        }

        $this->db->where('id_user', $user_id)
            ->delete('search_history');
    }

    public function delete_one($id, $user_id)
    {
        $id      = (int) $id;
        $user_id = (int) $user_id;

        if ($id <= 0 || $user_id <= 0) {
            return;
        }

        $this->db->where('id', $id)
            ->where('id_user', $user_id)
            ->delete('search_history');
    }
}
