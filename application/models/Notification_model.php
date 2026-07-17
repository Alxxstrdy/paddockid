<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function create($data) {
        return $this->db->insert('notifications', [
            'id_user'    => $data['id_user'],
            'type'       => $data['type'],
            'actor_id'   => $data['actor_id'],
            'id_post'    => $data['id_post'] ?? null,
            'id_comment' => $data['id_comment'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function get_notifications($id_user, $limit = 20, $offset = 0) {
        $this->db->select("
            n.id_notification,
            n.type,
            n.actor_id,
            n.id_post,
            n.id_comment,
            n.is_read,
            n.created_at,
            u.username as actor_username,
            u.display_name as actor_display_name,
            u.avatar as actor_avatar,
            u.last_activity as actor_last_activity,
            post_author.username as post_author_username
        ", false);
        $this->db->from('notifications n');
        $this->db->join('users u', 'n.actor_id = u.id_user', 'left');
        $this->db->join('posts p', 'n.id_post = p.id_post', 'left');
        $this->db->join('users post_author', 'p.user_id = post_author.id_user', 'left');
        $this->db->where('n.id_user', $id_user);
        $this->db->order_by('n.created_at', 'DESC');
        $this->db->limit($limit, $offset);

        $result = $this->db->get()->result_array();
        $online_threshold = date('Y-m-d H:i:s', strtotime('-2 minutes'));
        foreach ($result as &$notif) {
            $notif['actor_avatar'] = avatar_url($notif['actor_avatar']);
            $notif['created_at'] = $this->_time_ago($notif['created_at']);
            $notif['message'] = $this->_format_message($notif);
            $notif['actor_is_online'] = !empty($notif['actor_last_activity']) && $notif['actor_last_activity'] >= $online_threshold;
        }
        return $result;
    }

    public function count_unread($id_user) {
        $this->db->where('id_user', $id_user);
        $this->db->where('is_read', 0);
        return $this->db->count_all_results('notifications');
    }

    public function mark_read($id_notification, $id_user) {
        $this->db->where('id_notification', $id_notification);
        $this->db->where('id_user', $id_user);
        return $this->db->update('notifications', ['is_read' => 1]);
    }

    public function mark_all_read($id_user) {
        $this->db->where('id_user', $id_user);
        $this->db->where('is_read', 0);
        return $this->db->update('notifications', ['is_read' => 1]);
    }

    private function _time_ago($datetime) {
        $time = strtotime($datetime);
        $diff = time() - $time;

        if ($diff < 60) return 'Baru saja';
        if ($diff < 3600) return floor($diff / 60) . 'm';
        if ($diff < 86400) return floor($diff / 3600) . 'j';
        if ($diff < 604800) return floor($diff / 86400) . 'h';
        return date('d M', $time);
    }

    private function _format_message($notif) {
        switch ($notif['type']) {
            case 'like':
                return 'menyukai postinganmu';
            case 'comment':
                return 'berkomentar di postinganmu';
            case 'reply':
                return 'membalas komentarmu';
            case 'follow':
                return 'mulai mengikutimu';
            default:
                return 'berinteraksi denganmu';
        }
    }
}
