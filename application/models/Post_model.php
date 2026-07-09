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
        $current_user_id = $current_user_id ? $this->db->escape($current_user_id) : '0';
        return "
            p.id_post,
            p.user_id,
            p.post_category,
            u.username,
            u.display_name,
            u.avatar,
            u.verified,
            u.team_id,
            t.team_name,
            t.team_logo,
            t.team_color,
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
            $row['avatar'] = avatar_url($row['avatar']);
            $row['border'] = !empty($row['border'])
                ? assets_url($row['border'])
                : null;
            $row['is_liked'] = (bool) $row['is_liked'];
            $row['created_at'] = formatWaktuSosmed($row['created_at']);

            if (!empty($row['file_url'])) {
                $media_urls = explode(',', $row['file_url']);
                foreach ($media_urls as &$url) {
                    $url = trim($url);
                    if (strpos($url, 'http') === 0) continue;
                    if (strpos($url, 'uploads/') === 0) $url = base_url($url);
                    else $url = assets_url($url);
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

        $this->db->select($this->_base_post_select($current_user_id), false);
        $this->db->from('posts p');
        $this->db->join('users u', 'p.user_id = u.id_user');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->join('team t', 'u.team_id = t.team_id', 'left');
        $this->db->join('post_category pc', 'p.post_category = pc.id_category', 'left');

        // Filter block — jika melihat profil user lain, exclude jika saling block
        if ($current_user_id && $current_user_id !== $user_id) {
            $escaped_cuid = $this->db->escape($current_user_id);
            $this->db->join('blocked_users bu_viewer_blocked', "bu_viewer_blocked.blocker_id = {$escaped_cuid} AND bu_viewer_blocked.blocked_id = p.user_id", 'left');
            $this->db->join('blocked_users bu_author_blocked', "bu_author_blocked.blocked_id = {$escaped_cuid} AND bu_author_blocked.blocker_id = p.user_id", 'left');
            $this->db->where('bu_viewer_blocked.id_block IS NULL AND bu_author_blocked.id_block IS NULL');
        }

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

        $this->db->select($this->_base_post_select($current_user_id), false);
        $this->db->from('post_likes pl');
        $this->db->join('posts p', 'pl.id_post = p.id_post');
        $this->db->join('users u', 'p.user_id = u.id_user');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->join('team t', 'u.team_id = t.team_id', 'left');
        $this->db->join('post_category pc', 'p.post_category = pc.id_category', 'left');

        // Filter block
        if ($current_user_id && $current_user_id !== $user_id) {
            $escaped_cuid = $this->db->escape($current_user_id);
            $this->db->join('blocked_users bu_viewer_blocked', "bu_viewer_blocked.blocker_id = {$escaped_cuid} AND bu_viewer_blocked.blocked_id = p.user_id", 'left');
            $this->db->join('blocked_users bu_author_blocked', "bu_author_blocked.blocked_id = {$escaped_cuid} AND bu_author_blocked.blocker_id = p.user_id", 'left');
            $this->db->where('bu_viewer_blocked.id_block IS NULL AND bu_author_blocked.id_block IS NULL');
        }

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
        $this->db->select($this->_base_post_select($current_user_id), false);
        $this->db->from('posts p');
        $this->db->join('users u', 'p.user_id = u.id_user');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->join('team t', 'u.team_id = t.team_id', 'left');
        $this->db->join('post_category pc', 'p.post_category = pc.id_category', 'left');
        $this->db->where('p.id_post', $id_post);
        $this->db->where('(p.deleted IS NULL OR p.deleted = 0)');

        $row = $this->db->get()->row_array();
        if (!$row) return null;

        // Jika post author memblockir viewer, sembunyikan
        if ($current_user_id && !empty($row['user_id'])) {
            $this->db->where('blocker_id', $row['user_id']);
            $this->db->where('blocked_id', $current_user_id);
            $blocked = $this->db->get('blocked_users')->num_rows() > 0;
            if ($blocked) return null;
        }

        $rows = [&$row];
        $this->_format_posts($rows);
        return $row;
    }

    public function get_recent_posts($limit, $offset, $current_user_id = null)
    {
        // Ambil daftar user yang diikuti
        $followed_ids = [];
        if ($current_user_id) {
            $this->db->select('id_following');
            $this->db->from('follows');
            $this->db->where('id_followers', $current_user_id);
            $rows = $this->db->get()->result_array();
            foreach ($rows as $row) {
                $followed_ids[] = $this->db->escape($row['id_following']);
            }
        }
        $followed_ids_str = $followed_ids ? implode(',', $followed_ids) : "''";

        $this->db->select($this->_base_post_select($current_user_id), false);
        $this->db->from('posts p');
        $this->db->join('users u', 'p.user_id = u.id_user');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->join('team t', 'u.team_id = t.team_id', 'left');
        $this->db->join('post_category pc', 'p.post_category = pc.id_category', 'left');

        // Filter block — exclude post dari user yang diblokir atau memblokir
        if ($current_user_id) {
            $escaped_cuid = $this->db->escape($current_user_id);
            $this->db->join('blocked_users bu_blocked', "bu_blocked.blocker_id = {$escaped_cuid} AND bu_blocked.blocked_id = p.user_id", 'left');
            $this->db->join('blocked_users bu_blocker', "bu_blocker.blocked_id = {$escaped_cuid} AND bu_blocker.blocker_id = p.user_id", 'left');
            $this->db->where('bu_blocked.id_block IS NULL AND bu_blocker.id_block IS NULL');
        }

        $this->db->where('(p.deleted IS NULL OR p.deleted = 0)');
        $this->db->order_by("(CASE WHEN p.user_id IN ({$followed_ids_str}) THEN 0 ELSE 1 END), (CASE WHEN p.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 0 ELSE 1 END), RAND()");
        $this->db->limit($limit, $offset);

        $result = $this->db->get()->result_array();
        $this->_format_posts($result);
        return $result;
    }

    public function get_for_you_posts($limit, $offset, $current_user_id = null)
    {
        // Ambil followed IDs dulu (sebelum main query, biar ga reset query builder)
        $fid_str = "''";
        if ($current_user_id) {
            $fq = $this->db->select('id_following')
                ->from('follows')
                ->where('id_followers', $current_user_id)
                ->get()
                ->result_array();
            $fids = [];
            foreach ($fq as $r) $fids[] = $this->db->escape($r['id_following']);
            $fid_str = $fids ? implode(',', $fids) : "''";
        }

        $this->db->select($this->_base_post_select($current_user_id), false);
        $this->db->from('posts p');
        $this->db->join('users u', 'p.user_id = u.id_user');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->join('team t', 'u.team_id = t.team_id', 'left');
        $this->db->join('post_category pc', 'p.post_category = pc.id_category', 'left');

        if ($current_user_id) {
            $escaped_cuid = $this->db->escape($current_user_id);
            $this->db->join('blocked_users bu_blocked', "bu_blocked.blocker_id = {$escaped_cuid} AND bu_blocked.blocked_id = p.user_id", 'left');
            $this->db->join('blocked_users bu_blocker', "bu_blocker.blocked_id = {$escaped_cuid} AND bu_blocker.blocker_id = p.user_id", 'left');
            $this->db->where('bu_blocked.id_block IS NULL AND bu_blocker.id_block IS NULL');
        }

        $this->db->where('(p.deleted IS NULL OR p.deleted = 0)');

        // Mix algorithm:
        //   Followed user → massive boost (10^10) + timestamp → always on top, sorted by time
        //   Non-followed  → (likes * 86400 + comments * 172800) + timestamp → trending first
        $this->db->order_by("
            (CASE WHEN p.user_id IN ({$fid_str})
                THEN 10000000000 + UNIX_TIMESTAMP(p.created_at)
                ELSE likes_count * 86400 + comments_count * 172800 + UNIX_TIMESTAMP(p.created_at)
            END) DESC
        ", '', false);
        $this->db->limit($limit, $offset);

        $result = $this->db->get()->result_array();
        $this->_format_posts($result);
        return $result;
    }

    public function get_following_posts($limit, $offset, $current_user_id)
    {
        if (!$current_user_id) return [];

        $this->db->select($this->_base_post_select($current_user_id), false);
        $this->db->from('posts p');
        $this->db->join('users u', 'p.user_id = u.id_user');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->join('team t', 'u.team_id = t.team_id', 'left');
        $this->db->join('post_category pc', 'p.post_category = pc.id_category', 'left');

        $escaped_cuid = $this->db->escape($current_user_id);
        $this->db->join('blocked_users bu_blocked', "bu_blocked.blocker_id = {$escaped_cuid} AND bu_blocked.blocked_id = p.user_id", 'left');
        $this->db->join('blocked_users bu_blocker', "bu_blocker.blocked_id = {$escaped_cuid} AND bu_blocker.blocker_id = p.user_id", 'left');
        $this->db->where('bu_blocked.id_block IS NULL AND bu_blocker.id_block IS NULL');

        $this->db->where('p.user_id IN (SELECT id_following FROM follows WHERE id_followers = ' . $escaped_cuid . ')');
        $this->db->where('(p.deleted IS NULL OR p.deleted = 0)');
        $this->db->order_by('p.created_at', 'DESC');
        $this->db->limit($limit, $offset);

        $result = $this->db->get()->result_array();
        $this->_format_posts($result);
        return $result;
    }

    public function get_posts_by_category_slug($slug, $limit, $offset, $current_user_id = null)
    {
        $this->db->select($this->_base_post_select($current_user_id), false);
        $this->db->from('posts p');
        $this->db->join('users u', 'p.user_id = u.id_user');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->join('team t', 'u.team_id = t.team_id', 'left');
        $this->db->join('post_category pc', 'p.post_category = pc.id_category', 'left');

        // Filter block
        if ($current_user_id) {
            $escaped_cuid = $this->db->escape($current_user_id);
            $this->db->join('blocked_users bu_blocked', "bu_blocked.blocker_id = {$escaped_cuid} AND bu_blocked.blocked_id = p.user_id", 'left');
            $this->db->join('blocked_users bu_blocker', "bu_blocker.blocked_id = {$escaped_cuid} AND bu_blocker.blocker_id = p.user_id", 'left');
            $this->db->where('bu_blocked.id_block IS NULL AND bu_blocker.id_block IS NULL');
        }

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
            $row['avatar'] = avatar_url($row['avatar']);
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
        $this->db->where('user_id', $user_id);
        $this->db->where('(deleted IS NULL OR deleted = 0)');
        return $this->db->count_all_results('posts');
    }

    public function get_user_by_username($username, $current_user_id = 0)
    {
        $this->db->select('u.*, b.image_url as border_image, t.team_name, t.team_logo, t.team_color');
        $this->db->select('(SELECT COUNT(*) FROM follows WHERE id_followers = u.id_user) as total_following');
        $this->db->select('(SELECT COUNT(*) FROM follows WHERE id_following = u.id_user) as total_followers');
        $this->db->select('(SELECT COUNT(*) FROM posts WHERE user_id = u.id_user AND (deleted IS NULL OR deleted = 0)) as total_posts');
        if ($current_user_id) {
            $escaped_cuid = $this->db->escape($current_user_id);
            $this->db->select("(SELECT COUNT(*) FROM follows WHERE id_following = u.id_user AND id_followers = {$escaped_cuid}) > 0 as is_following");
            $this->db->select("(SELECT COUNT(*) FROM blocked_users WHERE blocker_id = {$escaped_cuid} AND blocked_id = u.id_user) > 0 as is_blocked");
            $this->db->select("(SELECT COUNT(*) FROM blocked_users WHERE blocker_id = u.id_user AND blocked_id = {$escaped_cuid}) > 0 as is_blocked_by");
        } else {
            $this->db->select('0 as is_following');
            $this->db->select('0 as is_blocked');
            $this->db->select('0 as is_blocked_by');
        }
        $this->db->from('users u');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->join('team t', 'u.team_id = t.team_id', 'left');
        $this->db->where('u.username', $username);
        $result = $this->db->get()->row_array();
        if ($result) {
            $result['is_following'] = (bool) $result['is_following'];
            $result['is_blocked'] = (bool) $result['is_blocked'];
            $result['is_blocked_by'] = (bool) $result['is_blocked_by'];
        }
        return $result;
    }

    public function toggle_follow($follower_id, $following_id)
    {
        $existing = $this->db->get_where('follows', [
            'id_following' => $following_id,
            'id_followers' => $follower_id
        ])->row();

        if ($existing) {
            $this->db->delete('follows', [
                'id_following' => $following_id,
                'id_followers' => $follower_id
            ]);
            $action = 'unfollowed';
        } else {
            $this->db->insert('follows', [
                'id_following' => $following_id,
                'id_followers' => $follower_id
            ]);
            $action = 'followed';
        }

        $followers_count = $this->db->where('id_following', $following_id)
            ->count_all_results('follows');

        return [
            'action' => $action,
            'followers_count' => $followers_count
        ];
    }

    public function is_following($follower_id, $following_id)
    {
        return $this->db->get_where('follows', [
            'id_following' => $following_id,
            'id_followers' => $follower_id
        ])->num_rows() > 0;
    }

    public function report_user($reporter_id, $reported_id, $reason)
    {
        return $this->db->insert('user_reports', [
            'reporter_id' => $reporter_id,
            'reported_id' => $reported_id,
            'reason' => $reason,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function block_user($blocker_id, $blocked_id)
    {
        $existing = $this->db->get_where('blocked_users', [
            'blocker_id' => $blocker_id,
            'blocked_id' => $blocked_id
        ])->row();

        if ($existing) {
            return ['action' => 'already_blocked'];
        }

        $this->db->insert('blocked_users', [
            'blocker_id' => $blocker_id,
            'blocked_id' => $blocked_id
        ]);

        // Auto-unfollow 2 arah
        $b1 = $this->db->escape($blocker_id);
        $b2 = $this->db->escape($blocked_id);
        $this->db->where("(id_following = {$b1} AND id_followers = {$b2})");
        $this->db->or_where("(id_following = {$b2} AND id_followers = {$b1})");
        $this->db->delete('follows');

        return ['action' => 'blocked'];
    }

    public function unblock_user($blocker_id, $blocked_id)
    {
        $this->db->delete('blocked_users', [
            'blocker_id' => $blocker_id,
            'blocked_id' => $blocked_id
        ]);

        return ['action' => 'unblocked'];
    }

    public function is_blocked($blocker_id, $blocked_id)
    {
        return $this->db->get_where('blocked_users', [
            'blocker_id' => $blocker_id,
            'blocked_id' => $blocked_id
        ])->num_rows() > 0;
    }

    public function create_post($user_id, $content, $category_id, $media_files = [])
    {
        // Generate post ID: YYYYMMDD + 3 digit sequential
        $date_prefix = date('Ymd');
        $this->db->select('id_post');
        $this->db->like('id_post', $date_prefix, 'after');
        $this->db->order_by('id_post', 'DESC');
        $this->db->limit(1);
        $this->db->from('posts');
        $row = $this->db->get()->row();
        if ($row) {
            $last_num = (int) substr($row->id_post, -3);
            $next_num = $last_num + 1;
        } else {
            $next_num = 1;
        }
        $id_post = $date_prefix . str_pad($next_num, 3, '0', STR_PAD_LEFT);

        $data = [
            'id_post'       => $id_post,
            'user_id'       => $user_id,
            'content'       => $content,
            'post_category' => $category_id ? (int) $category_id : null,
            'created_at'    => date('Y-m-d H:i:s')
        ];

        $this->db->insert('posts', $data);

        if (!empty($media_files)) {
            foreach ($media_files as $file_path) {
                $this->db->insert('post_media', [
                    'id_post'  => $id_post,
                    'type'     => 'image',
                    'file_url' => $file_path
                ]);
            }
        }

        return $id_post;
    }

    public function update_post($id_post, $user_id, $content, $category_id)
    {
        $data = [
            'content' => $content,
            'post_category' => $category_id ? (int) $category_id : null,
        ];

        $this->db->where('id_post', $id_post);
        $this->db->where('user_id', $user_id);
        $this->db->update('posts', $data);
        return $this->db->affected_rows() > 0;
    }

    public function delete_post($id_post, $user_id)
    {
        $this->db->where('id_post', $id_post);
        $this->db->where('user_id', $user_id);
        $this->db->update('posts', ['deleted' => 1]);
        return $this->db->affected_rows() > 0;
    }

    public function update_comment($id_comment, $user_id, $content)
    {
        $this->db->where('id_comment', $id_comment);
        $this->db->where('user_id', $user_id);
        $this->db->update('post_comments', ['content' => $content]);
        return $this->db->affected_rows() > 0;
    }

    public function delete_comment($id_comment, $user_id)
    {
        $this->db->where('id_comment', $id_comment);
        $this->db->where('user_id', $user_id);
        $this->db->delete('post_comments');
        return $this->db->affected_rows() > 0;
    }

    public function report_post($id_post, $reporter_id, $reason)
    {
        return $this->db->insert('post_reports', [
            'id_post'     => $id_post,
            'reporter_id' => $reporter_id,
            'reason'      => $reason,
            'created_at'  => date('Y-m-d H:i:s')
        ]);
    }

    public function report_comment($id_comment, $reporter_id, $reason)
    {
        return $this->db->insert('post_reports', [
            'id_comment'  => $id_comment,
            'reporter_id' => $reporter_id,
            'reason'      => $reason,
            'created_at'  => date('Y-m-d H:i:s')
        ]);
    }

    public function search_posts($keyword, $limit, $offset, $current_user_id = null)
    {
        $this->db->select($this->_base_post_select($current_user_id), false);
        $this->db->from('posts p');
        $this->db->join('users u', 'p.user_id = u.id_user');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->join('team t', 'u.team_id = t.team_id', 'left');
        $this->db->join('post_category pc', 'p.post_category = pc.id_category', 'left');

        if ($current_user_id) {
            $escaped_cuid = $this->db->escape($current_user_id);
            $this->db->join('blocked_users bu_blocked', "bu_blocked.blocker_id = {$escaped_cuid} AND bu_blocked.blocked_id = p.user_id", 'left');
            $this->db->join('blocked_users bu_blocker', "bu_blocker.blocked_id = {$escaped_cuid} AND bu_blocker.blocker_id = p.user_id", 'left');
            $this->db->where('bu_blocked.id_block IS NULL AND bu_blocker.id_block IS NULL');
        }

        $this->db->where('(p.deleted IS NULL OR p.deleted = 0)');
        $this->db->group_start();
        $this->db->like('p.content', $keyword);
        $this->db->or_like('u.display_name', $keyword);
        $this->db->or_like('u.username', $keyword);
        $this->db->group_end();
        $this->db->order_by('p.created_at', 'DESC');
        $this->db->limit($limit, $offset);

        $result = $this->db->get()->result_array();
        $this->_format_posts($result);
        return $result;
    }

    public function count_search_posts($keyword)
    {
        $this->db->from('posts p');
        $this->db->join('users u', 'p.user_id = u.id_user');
        $this->db->where('(p.deleted IS NULL OR p.deleted = 0)');
        $this->db->group_start();
        $this->db->like('p.content', $keyword);
        $this->db->or_like('u.display_name', $keyword);
        $this->db->or_like('u.username', $keyword);
        $this->db->group_end();
        return $this->db->count_all_results();
    }

    public function search_users($keyword, $limit, $offset, $current_user_id = null)
    {
        $logged_in_id = $current_user_id ? $this->db->escape($current_user_id) : '0';

        $this->db->select("
            u.id_user,
            u.username,
            u.display_name,
            u.avatar,
            u.verified,
            b.image_url as border,
            (SELECT COUNT(*) FROM follows WHERE id_following = u.id_user) as followers_count,
            (SELECT COUNT(*) FROM follows WHERE id_following = u.id_user AND id_followers = {$logged_in_id}) > 0 as is_followed
        ", false);
        $this->db->from('users u');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');

        if ($current_user_id) {
            $escaped_cuid = $this->db->escape($current_user_id);
            $this->db->join('blocked_users bu_blocked', "bu_blocked.blocker_id = {$escaped_cuid} AND bu_blocked.blocked_id = u.id_user", 'left');
            $this->db->join('blocked_users bu_blocker', "bu_blocker.blocked_id = {$escaped_cuid} AND bu_blocker.blocker_id = u.id_user", 'left');
            $this->db->where('bu_blocked.id_block IS NULL AND bu_blocker.id_block IS NULL');
        }

        $this->db->where('u.status', 'active');
        $this->db->group_start();
        $this->db->like('u.username', $keyword);
        $this->db->or_like('u.display_name', $keyword);
        $this->db->group_end();
        $this->db->order_by('followers_count', 'DESC');
        $this->db->limit($limit, $offset);

        $result = $this->db->get()->result_array();
        foreach ($result as &$user) {
            $user['avatar'] = avatar_url($user['avatar']);
            $user['border'] = !empty($user['border']) ? assets_url($user['border']) : null;
            $user['is_followed'] = (bool) $user['is_followed'];
        }
        return $result;
    }

    public function count_search_users($keyword)
    {
        $this->db->from('users u');
        $this->db->where('u.status', 'active');
        $this->db->group_start();
        $this->db->like('u.username', $keyword);
        $this->db->or_like('u.display_name', $keyword);
        $this->db->group_end();
        return $this->db->count_all_results();
    }
}
