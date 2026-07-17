<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // =====================
    // DASHBOARD STATS
    // =====================

    public function get_stats() {
        $stats = [];

        $stats['total_users'] = $this->db->count_all('users');
        $stats['total_posts'] = $this->db->where('deleted', 0)->count_all('posts');
        $stats['pending_reports'] = $this->db->where('status', 'pending')->count_all('post_reports')
            + $this->db->where('status', 'pending')->count_all('user_reports');
        $stats['failed_logins_24h'] = $this->db->where('success', 0)
            ->where('attempted_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->count_all_results('login_attempts');
        $stats['new_users_7d'] = $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->count_all_results('users');
        $stats['total_comments'] = $this->db->count_all('post_comments');

        $stats['post_reports_count'] = $this->db->count_all('post_reports');
        $stats['user_reports_count'] = $this->db->count_all('user_reports');
        $stats['login_attempts_count'] = $this->db->count_all('login_attempts');

        return $stats;
    }

    public function get_pending_counts() {
        return [
            'post_reports'  => $this->db->where('status', 'pending')->count_all_results('post_reports'),
            'user_reports'  => $this->db->where('status', 'pending')->count_all_results('user_reports'),
            'failed_logins' => $this->db->where('success', 0)
                ->where('attempted_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                ->count_all_results('login_attempts'),
            'errors_today'  => count($this->_get_today_log_entries()),
        ];
    }

    private function _get_today_log_entries() {
        $filename = 'log-' . date('Y-m-d') . '.php';
        $filepath = APPPATH . 'logs/' . $filename;
        if (!file_exists($filepath)) return [];
        $raw = file_get_contents($filepath);
        return array_filter(explode("\n", $raw), function($line) {
            return stripos($line, 'ERROR') !== false || stripos($line, 'WARNING') !== false;
        });
    }

    public function get_recent_activity($limit = 10) {
        $activities = [];

        $reports = $this->db->select('pr.id_report, pr.reason, pr.created_at, pr.status,
            u.username as reporter, "post_report" as type, pr.id_post as target_id')
            ->from('post_reports pr')
            ->join('users u', 'u.id_user = pr.reporter_id', 'left')
            ->order_by('pr.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->result_array();

        $user_reports = $this->db->select('ur.id_report, ur.reason, ur.created_at, ur.status,
            u.username as reporter, "user_report" as type, ur.reported_id as target_id')
            ->from('user_reports ur')
            ->join('users u', 'u.id_user = ur.reporter_id', 'left')
            ->order_by('ur.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->result_array();

        $activities = array_merge($reports, $user_reports);
        usort($activities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return array_slice($activities, 0, $limit);
    }

    // =====================
    // REPORTS - POST REPORTS
    // =====================

    public function get_post_reports($status = 'all', $offset = 0, $limit = 20) {
        $this->db->select('pr.*, u.username as reporter, u.avatar as reporter_avatar,
            p.content as post_content, p.deleted as post_deleted, p.user_id as post_owner_id,
            pu.username as post_owner_name')
            ->from('post_reports pr')
            ->join('users u', 'u.id_user = pr.reporter_id', 'left')
            ->join('posts p', 'p.id_post = pr.id_post', 'left')
            ->join('users pu', 'pu.id_user = p.user_id', 'left');

        if ($status !== 'all') {
            $this->db->where('pr.status', $status);
        }

        $this->db->order_by('pr.created_at', 'DESC')
            ->limit($limit, $offset);

        return $this->db->get()->result_array();
    }

    public function count_post_reports($status = 'all') {
        if ($status === 'all') {
            return $this->db->count_all('post_reports');
        }
        return $this->db->where('status', $status)->count_all_results('post_reports');
    }

    public function resolve_post_report($id_report, $status, $admin_id) {
        return $this->db->where('id_report', $id_report)->update('post_reports', [
            'status'      => $status,
            'resolved_by' => $admin_id,
            'resolved_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function delete_post($id_post) {
        return $this->db->where('id_post', $id_post)->update('posts', ['deleted' => 1]);
    }

    public function delete_comment($id_comment) {
        return $this->db->where('id_comment', $id_comment)->delete('post_comments');
    }

    // =====================
    // REPORTS - USER REPORTS
    // =====================

    public function get_user_reports($status = 'all', $offset = 0, $limit = 20) {
        $this->db->select('ur.*, u.username as reporter, u.avatar as reporter_avatar,
            ru.username as reported_name, ru.avatar as reported_avatar, ru.status as reported_status')
            ->from('user_reports ur')
            ->join('users u', 'u.id_user = ur.reporter_id', 'left')
            ->join('users ru', 'ru.id_user = ur.reported_id', 'left');

        if ($status !== 'all') {
            $this->db->where('ur.status', $status);
        }

        $this->db->order_by('ur.created_at', 'DESC')
            ->limit($limit, $offset);

        return $this->db->get()->result_array();
    }

    public function count_user_reports($status = 'all') {
        if ($status === 'all') {
            return $this->db->count_all('user_reports');
        }
        return $this->db->where('status', $status)->count_all_results('user_reports');
    }

    public function resolve_user_report($id_report, $status, $admin_id) {
        return $this->db->where('id_report', $id_report)->update('user_reports', [
            'status'      => $status,
            'resolved_by' => $admin_id,
            'resolved_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function ban_user($id_user) {
        return $this->db->where('id_user', $id_user)->update('users', ['status' => 'banned']);
    }

    public function unban_user($id_user) {
        return $this->db->where('id_user', $id_user)->update('users', ['status' => 'active']);
    }

    // =====================
    // USER LIST
    // =====================

    public function get_users($offset = 0, $limit = 20, $filter = []) {
        $this->db->select('id_user, username, email, display_name, avatar, status, role, verified, login_type, created_at, last_activity')
            ->from('users');

        if (!empty($filter['status'])) {
            $this->db->where('status', $filter['status']);
        }
        if (!empty($filter['role'])) {
            $this->db->where('role', $filter['role']);
        }
        if (!empty($filter['search'])) {
            $this->db->group_start()
                ->like('username', $filter['search'])
                ->or_like('email', $filter['search'])
                ->or_like('display_name', $filter['search'])
                ->group_end();
        }

        $this->db->order_by('created_at', 'DESC')
            ->limit($limit, $offset);

        return $this->db->get()->result_array();
    }

    public function count_users($filter = []) {
        $this->db->from('users');

        if (!empty($filter['status'])) {
            $this->db->where('status', $filter['status']);
        }
        if (!empty($filter['role'])) {
            $this->db->where('role', $filter['role']);
        }
        if (!empty($filter['search'])) {
            $this->db->group_start()
                ->like('username', $filter['search'])
                ->or_like('email', $filter['search'])
                ->or_like('display_name', $filter['search'])
                ->group_end();
        }

        return $this->db->count_all_results();
    }

    // =====================
    // LOGIN ATTEMPTS
    // =====================

    public function get_login_attempts($offset = 0, $limit = 50, $filter = []) {
        $this->db->select('*')
            ->from('login_attempts');

        if (!empty($filter['success'])) {
            $this->db->where('success', $filter['success']);
        }
        if (!empty($filter['identity'])) {
            $this->db->like('identity', $filter['identity']);
        }
        if (!empty($filter['ip_address'])) {
            $this->db->where('ip_address', $filter['ip_address']);
        }
        if (!empty($filter['date_from'])) {
            $this->db->where('attempted_at >=', $filter['date_from'] . ' 00:00:00');
        }
        if (!empty($filter['date_to'])) {
            $this->db->where('attempted_at <=', $filter['date_to'] . ' 23:59:59');
        }

        $this->db->order_by('attempted_at', 'DESC')
            ->limit($limit, $offset);

        return $this->db->get()->result_array();
    }

    public function count_login_attempts($filter = []) {
        $this->db->from('login_attempts');

        if (!empty($filter['success'])) {
            $this->db->where('success', $filter['success']);
        }
        if (!empty($filter['identity'])) {
            $this->db->like('identity', $filter['identity']);
        }
        if (!empty($filter['ip_address'])) {
            $this->db->where('ip_address', $filter['ip_address']);
        }
        if (!empty($filter['date_from'])) {
            $this->db->where('attempted_at >=', $filter['date_from'] . ' 00:00:00');
        }
        if (!empty($filter['date_to'])) {
            $this->db->where('attempted_at <=', $filter['date_to'] . ' 23:59:59');
        }

        return $this->db->count_all_results();
    }

    public function clear_old_attempts($days = 30) {
        $this->db->where('attempted_at <', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        return $this->db->delete('login_attempts');
    }

    // =====================
    // ERROR LOGS
    // =====================

    public function get_log_files() {
        $log_path = APPPATH . 'logs/';
        $files = [];

        if (!is_dir($log_path)) return $files;

        $handle = opendir($log_path);
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') continue;
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;
            $filepath = $log_path . $file;
            $files[] = [
                'name'     => $file,
                'size'     => filesize($filepath),
                'modified' => date('Y-m-d H:i:s', filemtime($filepath)),
            ];
        }
        closedir($handle);

        usort($files, function($a, $b) {
            return strcmp($b['name'], $a['name']);
        });

        return $files;
    }

    public function parse_log_file($filename) {
        if (!preg_match('/^log-\d{4}-\d{2}-\d{2}\.php$/', $filename)) {
            return [];
        }

        $filepath = APPPATH . 'logs/' . $filename;
        if (!file_exists($filepath)) return [];

        $raw = file_get_contents($filepath);
        $entries = [];
        $blocks = preg_split('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\s*/m', $raw, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        $i = 0;
        $count = count($blocks);
        while ($i < $count) {
            $datetime = $blocks[$i];
            $message = isset($blocks[$i + 1]) ? trim($blocks[$i + 1]) : '';
            $i += 2;

            $level = 'debug';
            if (stripos($message, 'ERROR') === 0) $level = 'error';
            elseif (stripos($message, 'WARNING') === 0 || stripos($message, 'Severity: Warning') !== false) $level = 'warning';
            elseif (stripos($message, 'INFO') === 0) $level = 'info';

            $entries[] = [
                'datetime' => $datetime,
                'level'    => $level,
                'message'  => $message,
            ];
        }

        return array_reverse($entries);
    }

    public function delete_log_file($filename) {
        if (!preg_match('/^log-\d{4}-\d{2}-\d{2}\.php$/', $filename)) return false;
        $filepath = APPPATH . 'logs/' . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }

    // =====================
    // CUSTOM ADS
    // =====================

    public function get_ads($offset = 0, $limit = 20, $filter = []) {
        $this->db->from('custom_ads');

        if (!empty($filter['position'])) {
            $this->db->where('position', $filter['position']);
        }
        if (isset($filter['is_active']) && $filter['is_active'] !== '') {
            $this->db->where('is_active', (int) $filter['is_active']);
        }
        if (!empty($filter['search'])) {
            $this->db->group_start()
                ->like('title', $filter['search'])
                ->or_like('description', $filter['search'])
                ->group_end();
        }

        $this->db->order_by('created_at', 'DESC')
            ->limit($limit, $offset);

        return $this->db->get()->result_array();
    }

    public function count_ads($filter = []) {
        $this->db->from('custom_ads');

        if (!empty($filter['position'])) {
            $this->db->where('position', $filter['position']);
        }
        if (isset($filter['is_active']) && $filter['is_active'] !== '') {
            $this->db->where('is_active', (int) $filter['is_active']);
        }
        if (!empty($filter['search'])) {
            $this->db->group_start()
                ->like('title', $filter['search'])
                ->or_like('description', $filter['search'])
                ->group_end();
        }

        return $this->db->count_all_results();
    }

    public function get_ad($id) {
        return $this->db->where('id_ad', $id)->get('custom_ads')->row_array();
    }

    public function create_ad($data) {
        $this->db->insert('custom_ads', $data);
        return $this->db->insert_id();
    }

    public function update_ad($id, $data) {
        return $this->db->where('id_ad', $id)->update('custom_ads', $data);
    }

    public function delete_ad($id) {
        $ad = $this->get_ad($id);
        if ($ad && !empty($ad['image_url'])) {
            $image_path = FCPATH . $ad['image_url'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        return $this->db->where('id_ad', $id)->delete('custom_ads');
    }

    public function toggle_ad($id) {
        $ad = $this->get_ad($id);
        if (!$ad) return false;
        $new_status = $ad['is_active'] ? 0 : 1;
        return $this->db->where('id_ad', $id)->update('custom_ads', ['is_active' => $new_status]);
    }

    public function get_active_ads($position = 'sidebar', $limit = 3) {
        $this->db->from('custom_ads')
            ->where('is_active', 1)
            ->where('start_date <=', 'NOW()', FALSE)
            ->group_start()
            ->where('end_date IS NULL', NULL, FALSE)
            ->or_where('end_date >=', 'NOW()', FALSE)
            ->group_end();

        if ($position === 'both') {
            $this->db->where_in('position', ['sidebar', 'feed', 'both']);
        } else {
            $this->db->group_start()
                ->where('position', $position)
                ->or_where('position', 'both')
                ->group_end();
        }

        $this->db->order_by('RAND()')
            ->limit($limit);

        return $this->db->get()->result_array();
    }

    public function increment_click_count($id) {
        return $this->db->where('id_ad', $id)->set('click_count', 'click_count + 1', FALSE)->update('custom_ads');
    }
}
