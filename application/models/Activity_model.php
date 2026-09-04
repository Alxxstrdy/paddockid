<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function log($id_user, $username, $action, $target_type = null, $target_id = null, $details = null) {
        $data = [
            'id_user'    => $id_user,
            'username'   => $username,
            'action'     => $action,
            'target_type' => $target_type,
            'target_id'  => $target_id,
            'details'    => $details,
            'ip_address' => $this->_get_real_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        return $this->db->insert('activity_logs', $data);
    }

    public function get_logs($offset = 0, $limit = 30, $filter = []) {
        $this->db->select('*')->from('activity_logs');

        if (!empty($filter['action'])) {
            $this->db->where('action', $filter['action']);
        }
        if (!empty($filter['username'])) {
            $this->db->like('username', $filter['username']);
        }
        if (!empty($filter['id_user'])) {
            $this->db->where('id_user', $filter['id_user']);
        }
        if (!empty($filter['date_from'])) {
            $this->db->where('created_at >=', $filter['date_from'] . ' 00:00:00');
        }
        if (!empty($filter['date_to'])) {
            $this->db->where('created_at <=', $filter['date_to'] . ' 23:59:59');
        }

        $this->db->order_by('created_at', 'DESC')
            ->limit($limit, $offset);

        return $this->db->get()->result_array();
    }

    public function count_logs($filter = []) {
        $this->db->from('activity_logs');

        if (!empty($filter['action'])) {
            $this->db->where('action', $filter['action']);
        }
        if (!empty($filter['username'])) {
            $this->db->like('username', $filter['username']);
        }
        if (!empty($filter['id_user'])) {
            $this->db->where('id_user', $filter['id_user']);
        }
        if (!empty($filter['date_from'])) {
            $this->db->where('created_at >=', $filter['date_from'] . ' 00:00:00');
        }
        if (!empty($filter['date_to'])) {
            $this->db->where('created_at <=', $filter['date_to'] . ' 23:59:59');
        }

        return $this->db->count_all_results();
    }

    public function get_actions_list() {
        $q = $this->db->select('action')->distinct()->from('activity_logs')->order_by('action', 'ASC')->get();
        return array_column($q->result_array(), 'action');
    }

    public function cleanup_old_logs($days = 90) {
        return $this->db->where('created_at <', date('Y-m-d H:i:s', strtotime("-{$days} days")))
            ->delete('activity_logs');
    }

    public function get_combined_logs($offset = 0, $limit = 30, $filter = []) {
        $sql = "
            SELECT id_log, id_user, username, action, target_type, target_id, details, ip_address, user_agent, created_at FROM activity_logs
            UNION ALL
            SELECT
                id AS id_log,
                NULL AS id_user,
                identity AS username,
                IF(success = 1, 'login_success', 'login_failed') AS action,
                NULL AS target_type,
                NULL AS target_id,
                IF(success = 1, 'Login berhasil', CONCAT('Login gagal dari ', identity)) AS details,
                ip_address,
                NULL AS user_agent,
                attempted_at AS created_at
            FROM login_attempts
            ORDER BY created_at DESC
        ";

        $params = [];
        $where_clauses = [];

        if (!empty($filter['action'])) {
            $where_clauses[] = 'action = ?';
            $params[] = $filter['action'];
        }
        if (!empty($filter['search'])) {
            $where_clauses[] = '(username LIKE ? OR details LIKE ?)';
            $params[] = '%' . $filter['search'] . '%';
            $params[] = '%' . $filter['search'] . '%';
        }
        if (!empty($filter['date_from'])) {
            $where_clauses[] = 'created_at >= ?';
            $params[] = $filter['date_from'] . ' 00:00:00';
        }
        if (!empty($filter['date_to'])) {
            $where_clauses[] = 'created_at <= ?';
            $params[] = $filter['date_to'] . ' 23:59:59';
        }

        if (!empty($where_clauses)) {
            $sql = "SELECT * FROM (" . $sql . ") AS combined WHERE " . implode(' AND ', $where_clauses) . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = (int) $limit;
            $params[] = (int) $offset;
        } else {
            $sql = "SELECT * FROM (" . $sql . ") AS combined ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = (int) $limit;
            $params[] = (int) $offset;
        }

        return $this->db->query($sql, $params)->result_array();
    }

    public function count_combined_logs($filter = []) {
        $sql = "
            SELECT 1 FROM activity_logs
            UNION ALL
            SELECT 1 FROM login_attempts
        ";

        $params = [];
        $where_clauses = [];

        if (!empty($filter['action'])) {
            $where_clauses[] = 'action = ?';
            $params[] = $filter['action'];
        }
        if (!empty($filter['search'])) {
            $where_clauses[] = '(username LIKE ? OR details LIKE ?)';
            $params[] = '%' . $filter['search'] . '%';
            $params[] = '%' . $filter['search'] . '%';
        }
        if (!empty($filter['date_from'])) {
            $where_clauses[] = 'created_at >= ?';
            $params[] = $filter['date_from'] . ' 00:00:00';
        }
        if (!empty($filter['date_to'])) {
            $where_clauses[] = 'created_at <= ?';
            $params[] = $filter['date_to'] . ' 23:59:59';
        }

        $count_sql = "SELECT COUNT(*) AS total FROM (" . $sql . ") AS combined";
        if (!empty($where_clauses)) {
            $count_sql .= " WHERE " . implode(' AND ', $where_clauses);
        }

        $result = $this->db->query($count_sql, $params)->row_array();
        return (int) ($result['total'] ?? 0);
    }

    public function get_all_actions() {
        $sql = "SELECT DISTINCT action FROM activity_logs
                UNION
                SELECT DISTINCT IF(success = 1, 'login_success', 'login_failed') AS action FROM login_attempts
                ORDER BY action ASC";
        return array_column($this->db->query($sql)->result_array(), 'action');
    }

    public function clear_combined_logs($before = null) {
        if ($before) {
            $this->db->where('created_at <', $before . ' 00:00:00')->delete('activity_logs');
            $this->db->where('attempted_at <', $before . ' 00:00:00')->delete('login_attempts');
        } else {
            $this->db->empty_table('activity_logs');
            $this->db->empty_table('login_attempts');
        }
    }

    private function _get_real_ip() {
        return get_real_ip();
    }
}
