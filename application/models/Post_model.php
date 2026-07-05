<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('waktu_helper');
    }

    private function _base_post_select($current_user_id)
    {
        $current_user_id = (int) $current_user_id;
        return "
            p.id_post,
            u.username,
            u.display_name,
            u.avatar,
            u.verified,
            b.image_url as border,
            p.content,
            p.created_at,
            pc.category_name as category,
            (SELECT COUNT(*) FROM post_likes WHERE id_post = p.id_post) as likes_count,
            (SELECT COUNT(*) FROM post_comments WHERE id_post = p.id_post) as comments_count,
            (SELECT COUNT(*) FROM post_likes WHERE id_post = p.id_post AND user_id = {$current_user_id}) > 0 as is_liked,
            (SELECT GROUP_CONCAT(file_url ORDER BY id ASC SEPARATOR ',') FROM post_media WHERE id_post = p.id_post) as file_url
        ";
    }

    private function _format_posts(&$rows)
    {
        foreach ($rows as &$row) {
            $row['category'] = $row['category'] ?? '';
            $row['avatar'] = !empty($row['avatar'])
                ? assets_url($row['avatar'])
                : assets_url('default.jpg');
            $row['border'] = !empty($row['border'])
                ? assets_url($row['border'])
                : null;
            $row['is_liked'] = (bool) $row['is_liked'];
            $row['created_at'] = formatWaktuSosmed($row['created_at']);

            if (!empty($row['file_url'])) {
                $media_urls = explode(',', $row['file_url']);
                foreach ($media_urls as &$url) {
                    $url = trim($url);
                    if (strpos($url, 'http') !== 0) {
                        $url = assets_url($url);
                    }
                }
                $row['file_url'] = implode(',', $media_urls);
            }
        }
    }

    public function get_user_posts($user_id, $limit, $offset, $current_user_id = null)
    {
        if (!$current_user_id) {
            $current_user_id = $user_id;
        }

        $current_user_id = (int) $current_user_id;
        $user_id = (int) $user_id;

        $this->db->select($this->_base_post_select($current_user_id), false);
        $this->db->from('posts p');
        $this->db->join('users u', 'p.user_id = u.id_user');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->join('post_category pc', 'p.post_category = pc.id_category', 'left');
        $this->db->where('p.user_id', $user_id);
        $this->db->where('(p.deleted IS NULL OR p.deleted = 0)');
        $this->db->order_by('p.created_at', 'DESC');
        $this->db->limit($limit, $offset);

        $result = $this->db->get()->result_array();
        $this->_format_posts($result);
        return $result;
    }

    public function get_liked_posts($user_id, $limit, $offset, $current_user_id = null)
    {
        if (!$current_user_id) {
            $current_user_id = $user_id;
        }

        $current_user_id = (int) $current_user_id;
        $user_id = (int) $user_id;

        $this->db->select($this->_base_post_select($current_user_id), false);
        $this->db->from('post_likes pl');
        $this->db->join('posts p', 'pl.id_post = p.id_post');
        $this->db->join('users u', 'p.user_id = u.id_user');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->join('post_category pc', 'p.post_category = pc.id_category', 'left');
        $this->db->where('pl.user_id', $user_id);
        $this->db->where('(p.deleted IS NULL OR p.deleted = 0)');
        $this->db->order_by('pl.created_at', 'DESC');
        $this->db->limit($limit, $offset);

        $result = $this->db->get()->result_array();
        $this->_format_posts($result);
        return $result;
    }

    public function get_post_by_id($id_post, $current_user_id = null)
    {
        $id_post = (int) $id_post;
        $current_user_id = $current_user_id ? (int) $current_user_id : 0;

        $this->db->select($this->_base_post_select($current_user_id), false);
        $this->db->from('posts p');
        $this->db->join('users u', 'p.user_id = u.id_user');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->join('post_category pc', 'p.post_category = pc.id_category', 'left');
        $this->db->where('p.id_post', $id_post);
        $this->db->where('(p.deleted IS NULL OR p.deleted = 0)');

        $row = $this->db->get()->row_array();
        if (!$row) return null;

        $rows = [&$row];
        $this->_format_posts($rows);
        return $row;
    }

    public function get_recent_posts($limit, $offset, $current_user_id = null)
    {
        $current_user_id = $current_user_id ? (int) $current_user_id : 0;

        // Ambil daftar user yang diikuti
        $followed_ids = [0];
        if ($current_user_id > 0) {
            $this->db->select('id_following');
            $this->db->from('follows');
            $this->db->where('id_followers', $current_user_id);
            $rows = $this->db->get()->result_array();
            foreach ($rows as $row) {
                $followed_ids[] = (int) $row['id_following'];
            }
        }
        $followed_ids_str = implode(',', $followed_ids);

        $this->db->select($this->_base_post_select($current_user_id), false);
        $this->db->from('posts p');
        $this->db->join('users u', 'p.user_id = u.id_user');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->join('post_category pc', 'p.post_category = pc.id_category', 'left');
        $this->db->where('(p.deleted IS NULL OR p.deleted = 0)');
        $this->db->order_by("(CASE WHEN p.user_id IN ({$followed_ids_str}) THEN 0 ELSE 1 END), (CASE WHEN p.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 0 ELSE 1 END), RAND()");
        $this->db->limit($limit, $offset);

        $result = $this->db->get()->result_array();
        $this->_format_posts($result);
        return $result;
    }

    public function get_posts_by_category_slug($slug, $limit, $offset, $current_user_id = null)
    {
        $current_user_id = $current_user_id ? (int) $current_user_id : 0;

        $this->db->select($this->_base_post_select($current_user_id), false);
        $this->db->from('posts p');
        $this->db->join('users u', 'p.user_id = u.id_user');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->join('post_category pc', 'p.post_category = pc.id_category', 'left');
        $this->db->where('pc.slug', $slug);
        $this->db->where('(p.deleted IS NULL OR p.deleted = 0)');
        $this->db->order_by('p.created_at', 'DESC');
        $this->db->limit($limit, $offset);

        $result = $this->db->get()->result_array();
        $this->_format_posts($result);
        return $result;
    }

    public function get_post_comments($id_post, $current_user_id = null)
    {
        $id_post = (int) $id_post;
        $current_user_id = $current_user_id ? (int) $current_user_id : 0;

        $this->db->select("
            c.id_comment,
            c.id_post,
            c.user_id,
            c.parent_id,
            c.content as comment_text,
            c.created_at,
            u.username,
            u.display_name,
            u.avatar,
            u.verified,
            b.image_url as border,
            pu.username as parent_username,
            (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id_comment) as likes_count,
            (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id_comment AND user_id = {$current_user_id}) > 0 as is_liked_comment
        ", false);

        $this->db->from('post_comments c');
        $this->db->join('users u', 'c.user_id = u.id_user');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->join('post_comments pc', 'c.parent_id = pc.id_comment', 'left');
        $this->db->join('users pu', 'pc.user_id = pu.id_user', 'left');
        $this->db->where('c.id_post', $id_post);
        $this->db->order_by('c.created_at', 'ASC');

        $result = $this->db->get()->result_array();

        foreach ($result as &$row) {
            $row['avatar'] = !empty($row['avatar'])
                ? assets_url($row['avatar'])
                : assets_url('default.jpg');
            $row['border'] = !empty($row['border'])
                ? assets_url($row['border'])
                : null;
            $row['is_liked_comment'] = (bool) $row['is_liked_comment'];
            $row['created_at'] = formatWaktuSosmed($row['created_at']);
        }

        return $result;
    }

    public function get_categories()
    {
        return $this->db->get('post_category')->result_array();
    }

    public function toggle_like($id_post, $user_id)
    {
        $id_post = (int) $id_post;
        $user_id = (int) $user_id;

        $existing = $this->db->get_where('post_likes', [
            'id_post' => $id_post,
            'user_id' => $user_id
        ])->row();

        if ($existing) {
            $this->db->delete('post_likes', [
                'id_post' => $id_post,
                'user_id' => $user_id
            ]);
            $action = 'unliked';
        } else {
            $this->db->insert('post_likes', [
                'id_post' => $id_post,
                'user_id' => $user_id
            ]);
            $action = 'liked';
        }

        $likes_count = $this->db->where('id_post', $id_post)
            ->count_all_results('post_likes');

        return [
            'action' => $action,
            'likes_count' => $likes_count
        ];
    }

    public function count_user_posts($user_id)
    {
        $this->db->where('user_id', (int) $user_id);
        $this->db->where('(deleted IS NULL OR deleted = 0)');
        return $this->db->count_all_results('posts');
    }
}
