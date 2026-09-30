<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dm_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    private function _normalize_pair($user_a, $user_b)
    {
        $a = (string) $user_a;
        $b = (string) $user_b;
        return (strcmp($a, $b) <= 0) ? [$a, $b] : [$b, $a];
    }

    public function get_or_create_conversation($user_a, $user_b)
    {
        list($a, $b) = $this->_normalize_pair($user_a, $user_b);

        $existing = $this->db->get_where('dm_conversations', [
            'user_id_a' => $a,
            'user_id_b' => $b
        ])->row_array();

        if ($existing) {
            return ['id_dm' => $existing['id_dm'], 'is_new' => false];
        }

        $this->db->insert('dm_conversations', [
            'user_id_a'  => $a,
            'user_id_b'  => $b,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return ['id_dm' => $this->db->insert_id(), 'is_new' => true];
    }

    public function get_conversation($id_dm)
    {
        return $this->db->get_where('dm_conversations', ['id_dm' => $id_dm])->row_array();
    }

    public function is_participant($id_dm, $user_id)
    {
        $this->db->group_start()
            ->where('user_id_a', $user_id)
            ->or_where('user_id_b', $user_id)
            ->group_end();
        $this->db->where('id_dm', $id_dm);
        return $this->db->get('dm_conversations')->num_rows() > 0;
    }

    public function is_blocked_pair($user_a, $user_b)
    {
        $a = $this->db->escape((string) $user_a);
        $b = $this->db->escape((string) $user_b);
        $this->db->where("(blocker_id = {$a} AND blocked_id = {$b}) OR (blocker_id = {$b} AND blocked_id = {$a})");
        return $this->db->get('blocked_users')->num_rows() > 0;
    }

    public function get_user_for_dm($username)
    {
        $this->db->select('id_user, username, display_name, avatar, status');
        $this->db->where('username', $username);
        return $this->db->get('users')->row_array();
    }

    public function is_banned_user($id_user)
    {
        return $this->db->where('id_user', $id_user)
            ->where('status', 'banned')
            ->get('users')
            ->num_rows() > 0;
    }

    public function get_user_for_dm_by_id($id_user)
    {
        $this->db->select('id_user, username, display_name, avatar, status');
        $this->db->where('id_user', $id_user);
        return $this->db->get('users')->row_array();
    }

    public function get_other_user_info($id_dm, $user_id)
    {
        $conv = $this->get_conversation($id_dm);
        if (!$conv) return null;

        $partner_id = ((string) $conv['user_id_a'] === (string) $user_id)
            ? $conv['user_id_b']
            : $conv['user_id_a'];

        $this->db->select('u.id_user, u.username, u.display_name, u.avatar, u.verified, u.last_activity, u.status, b.image_url as border_image');
        $this->db->from('users u');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->where('u.id_user', $partner_id);
        $result = $this->db->get()->row_array();

        if ($result) {
            $online_threshold = date('Y-m-d H:i:s', strtotime('-2 minutes'));
            $result['is_online'] = !empty($result['last_activity']) && $result['last_activity'] >= $online_threshold;
            $result['avatar'] = avatar_url($result['avatar']);
            $result['border'] = !empty($result['border_image']) ? assets_url($result['border_image']) : null;
            $result['id_dm'] = $id_dm;
            unset($result['border_image']);
        }
        return $result;
    }

    public function get_conversations_for_user($user_id)
    {
        $escaped = $this->db->escape($user_id);

        $this->db->select("
            c.id_dm,
            c.created_at as conversation_created_at,
            p.id_user as partner_id,
            p.username as partner_username,
            p.display_name as partner_display_name,
            p.avatar as partner_avatar,
            p.verified as partner_verified,
            p.last_activity as partner_last_activity,
            b.image_url as partner_border,
            tm.content as last_message_content,
            tm.sender_id as last_message_sender,
            tm.image_url as last_message_image,
            tm.post_id as last_message_post,
            tm.created_at as last_message_at,
            (SELECT COUNT(*) FROM dm_messages m2
               WHERE m2.id_dm = c.id_dm
                 AND m2.sender_id <> {$escaped}
                 AND m2.deleted = 0
                 AND (
                     (c.user_id_a = {$escaped} AND (c.last_read_a IS NULL OR m2.created_at > c.last_read_a))
                     OR
                     (c.user_id_b = {$escaped} AND (c.last_read_b IS NULL OR m2.created_at > c.last_read_b))
                 )
            ) as unread_count
        ", false);
        $this->db->from('dm_conversations c');
        $this->db->join('users p', "p.id_user = (CASE WHEN c.user_id_a = {$escaped} THEN c.user_id_b ELSE c.user_id_a END)", 'inner');
        $this->db->join('borders b', 'p.border_active = b.id_border', 'left');
        $this->db->join('dm_messages tm', 'tm.id_message = (SELECT id_message FROM dm_messages WHERE id_dm = c.id_dm AND deleted = 0 ORDER BY id_message DESC LIMIT 1)', 'left');
        $this->db->join('blocked_users bu1', "bu1.blocker_id = p.id_user AND bu1.blocked_id = {$escaped}", 'left');
        $this->db->join('blocked_users bu2', "bu2.blocker_id = {$escaped} AND bu2.blocked_id = p.id_user", 'left');
        $this->db->where("(c.user_id_a = {$escaped} OR c.user_id_b = {$escaped})");
        $this->db->where('bu1.id_block IS NULL AND bu2.id_block IS NULL');
        $this->db->order_by('tm.id_message', 'DESC');

        $result = $this->db->get()->result_array();

        $online_threshold = date('Y-m-d H:i:s', strtotime('-2 minutes'));
        foreach ($result as &$row) {
            $row['partner_avatar'] = avatar_url($row['partner_avatar']);
            $row['partner_border'] = !empty($row['partner_border']) ? assets_url($row['partner_border']) : null;
            $row['partner_is_online'] = !empty($row['partner_last_activity']) && $row['partner_last_activity'] >= $online_threshold;
            $row['unread_count'] = (int) $row['unread_count'];
            $row['is_last_from_me'] = (string) $row['last_message_sender'] === (string) $user_id;
        }
        return $result;
    }

    public function count_messages($id_dm)
    {
        $this->db->where('id_dm', $id_dm);
        $this->db->where('deleted', 0);
        return $this->db->count_all_results('dm_messages');
    }

    public function save_message($id_dm, $sender_id, $content, $image_url = null, $post_id = null)
    {
        $this->db->insert('dm_messages', [
            'id_dm'      => $id_dm,
            'sender_id'  => $sender_id,
            'content'    => $content,
            'image_url'  => $image_url,
            'post_id'    => $post_id,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        return $this->db->insert_id();
    }

    public function get_messages($id_dm, $user_id, $limit = 50, $before_id = null)
    {
        $this->db->select("
            m.id_message, m.id_dm, m.sender_id, m.content, m.image_url, m.post_id, m.created_at,
            u.username, u.avatar, u.display_name,
            pp.id_post AS post_ref_id,
            pp.content AS post_content,
            pp.created_at AS post_created_at,
            ppu.username AS post_username,
            ppu.avatar AS post_avatar,
            (SELECT pm.file_url FROM post_media pm WHERE pm.id_post = pp.id_post ORDER BY pm.id ASC LIMIT 1) AS post_media_url
        ", false);
        $this->db->from('dm_messages m');
        $this->db->join('users u', 'm.sender_id = u.id_user');
        $this->db->join('posts pp', "pp.id_post = m.post_id AND (pp.deleted IS NULL OR pp.deleted = 0)", 'left');
        $this->db->join('users ppu', 'ppu.id_user = pp.user_id', 'left');
        $this->db->where('m.id_dm', $id_dm);
        $this->db->where('m.deleted', 0);
        if ($before_id) {
            $this->db->where('m.id_message <', $before_id);
        }
        $this->db->order_by('m.id_message', 'DESC');
        $this->db->limit($limit);

        $rows = $this->db->get()->result_array();
        $rows = array_reverse($rows);

        foreach ($rows as &$row) {
            $row['avatar'] = avatar_url($row['avatar']);
            if (!empty($row['post_ref_id'])) {
                $media = $row['post_media_url'];
                if ($media) {
                    if (strpos($media, 'http') !== 0) {
                        if (strpos($media, 'uploads/') === 0) $media = base_url($media);
                        else $media = assets_url($media);
                    }
                }
                $row['post'] = [
                    'id_post'    => $row['post_ref_id'],
                    'username'   => $row['post_username'] ?? '',
                    'avatar'     => $row['post_avatar'] ? avatar_url($row['post_avatar']) : null,
                    'content'    => $row['post_content'] ?? '',
                    'created_at' => $row['post_created_at'] ?? null,
                    'media'      => $media ?: null,
                ];
            }
            unset($row['post_ref_id'], $row['post_content'], $row['post_created_at'], $row['post_username'], $row['post_avatar'], $row['post_media_url']);
        }
        return $rows;
    }

    public function mark_read($id_dm, $user_id)
    {
        $conv = $this->get_conversation($id_dm);
        if (!$conv) return false;

        if ((string) $conv['user_id_a'] === (string) $user_id) {
            $this->db->where('id_dm', $id_dm)->update('dm_conversations', ['last_read_a' => date('Y-m-d H:i:s')]);
            return true;
        }
        if ((string) $conv['user_id_b'] === (string) $user_id) {
            $this->db->where('id_dm', $id_dm)->update('dm_conversations', ['last_read_b' => date('Y-m-d H:i:s')]);
            return true;
        }
        return false;
    }

    public function count_unread($user_id)
    {
        $escaped = $this->db->escape($user_id);
        $sql = "SELECT COALESCE(SUM(
                    CASE WHEN c.user_id_a = {$escaped} THEN
                        (SELECT COUNT(*) FROM dm_messages m
                           WHERE m.id_dm = c.id_dm AND m.deleted = 0 AND m.sender_id <> {$escaped}
                             AND (c.last_read_a IS NULL OR m.created_at > c.last_read_a))
                    ELSE
                        (SELECT COUNT(*) FROM dm_messages m
                           WHERE m.id_dm = c.id_dm AND m.deleted = 0 AND m.sender_id <> {$escaped}
                             AND (c.last_read_b IS NULL OR m.created_at > c.last_read_b))
                    END), 0) as total
                FROM dm_conversations c
                WHERE c.user_id_a = {$escaped} OR c.user_id_b = {$escaped}";
        $row = $this->db->query($sql)->row();
        return $row ? (int) $row->total : 0;
    }

    public function delete_conversation($id_dm)
    {
        $this->db->where('id_dm', $id_dm);
        $this->db->where('type', 'dm');
        $this->db->delete('notifications');

        return $this->db->where('id_dm', $id_dm)->delete('dm_conversations');
    }

    public function soft_delete_message($id_message, $user_id)
    {
        $this->db->where('id_message', $id_message);
        $this->db->where('sender_id', $user_id);
        return $this->db->update('dm_messages', ['deleted' => 1]);
    }

    public function get_message_by_id($id_message)
    {
        return $this->db->get_where('dm_messages', ['id_message' => $id_message])->row_array();
    }
}