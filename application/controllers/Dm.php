<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dm extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Dm_model');
        $this->load->model('Post_model');
        $this->load->model('Notification_model');
        $this->load->model('Activity_model');
        $this->load->helper('assets_url_helper');
        $this->load->helper('waktu_helper');
        $this->load->config('pusher', TRUE);
    }

    private function _require_login()
    {
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            redirect('auth');
            return null;
        }
        return $session_data;
    }

    private function _json($data = [], $status = 200)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header($status)
            ->set_output(json_encode($data));
        return null;
    }

    private function _api_user()
    {
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            $this->_json(['error' => 'Unauthorized'], 401);
            return null;
        }
        return $session_data;
    }

    private function _partner_id($id_dm, $user_id)
    {
        $conv = $this->Dm_model->get_conversation($id_dm);
        if (!$conv) return null;
        return ((string) $conv['user_id_a'] === (string) $user_id)
            ? $conv['user_id_b']
            : $conv['user_id_a'];
    }

    private function _upload_image()
    {
        if (empty($_FILES['image']['name'])) return null;

        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Gagal mengunggah gambar.'];
        }

        if ($_FILES['image']['size'] > 10 * 1024 * 1024) {
            return ['error' => 'Ukuran gambar maksimal 10MB.'];
        }

        $info = @getimagesize($_FILES['image']['tmp_name']);
        $mime_to_ext = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
        ];
        $ext = $info ? ($mime_to_ext[$info['mime']] ?? null) : null;
        if ($ext === null) {
            return ['error' => 'File harus berupa gambar valid (jpg, png, gif, webp).'];
        }

        $upload_path = FCPATH . 'uploads/dm_images/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $new_name = md5(uniqid(mt_rand(), true)) . '.' . $ext;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path . $new_name)) {
            return ['error' => 'Gagal menyimpan gambar.'];
        }

        return ['path' => 'uploads/dm_images/' . $new_name];
    }

    private function _trigger_pusher($id_dm, $payload)
    {
        $app_id  = $this->config->item('pusher_app_id', 'pusher');
        $key     = $this->config->item('pusher_key', 'pusher');
        $secret  = $this->config->item('pusher_secret', 'pusher');
        $cluster = $this->config->item('pusher_cluster', 'pusher');
        if (!$app_id || !$key || !$secret) return;

        try {
            $pusher = new Pusher\Pusher($key, $secret, $app_id, ['cluster' => $cluster]);
            $pusher->trigger('private-dm-' . $id_dm, 'new-message', $payload);
            log_message('debug', 'Pusher triggered: private-dm-' . $id_dm . ' msg_id=' . $payload['id_message']);
        } catch (Exception $e) {
            log_coded_error('PPS-5001', 'Pusher DM trigger failed: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $session_data = $this->_require_login();
        if (!$session_data) return;

        $data['title'] = 'Pesan Pribadi | PaddockID';
        $data['current_user_id'] = $session_data['user_id'];
        $data['conversations'] = $this->Dm_model->get_conversations_for_user($session_data['user_id']);

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('dm/index', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function new($username = null)
    {
        $session_data = $this->_require_login();
        if (!$session_data) return;

        if (empty($username)) {
            redirect('dm');
            return;
        }

        $target = $this->Dm_model->get_user_for_dm($username);
        if (!$target) {
            show_404();
            return;
        }

        if ((string) $target['id_user'] === (string) $session_data['user_id']) {
            redirect('profile');
            return;
        }

        if (($target['status'] ?? 'active') === 'banned') {
            show_404();
            return;
        }

        if ($this->Dm_model->is_blocked_pair($session_data['user_id'], $target['id_user'])) {
            show_404();
            return;
        }

        $result = $this->Dm_model->get_or_create_conversation($session_data['user_id'], $target['id_user']);
        redirect('dm/conversation/' . $result['id_dm']);
    }

    public function conversation($id_dm = null)
    {
        $session_data = $this->_require_login();
        if (!$session_data) return;

        if (!$id_dm || !$this->Dm_model->is_participant($id_dm, $session_data['user_id'])) {
            show_404();
            return;
        }

        $other = $this->Dm_model->get_other_user_info($id_dm, $session_data['user_id']);
        if (!$other) {
            show_404();
            return;
        }

        $data['title'] = $other['display_name'] . ' | Pesan Pribadi | PaddockID';
        $data['current_user_id'] = $session_data['user_id'];
        $data['id_dm'] = $id_dm;
        $data['other'] = $other;
        $data['is_blocked'] = $this->Dm_model->is_blocked_pair($session_data['user_id'], $other['id_user']);
        $data['pusher_key'] = $this->config->item('pusher_key', 'pusher');
        $data['pusher_cluster'] = $this->config->item('pusher_cluster', 'pusher');

        $this->load->view('layout/header', $data);
        $this->load->view('dm/conversation', $data);
        $this->load->view('layout/footer');
    }

    public function send_message()
    {
        $session_data = $this->_api_user();
        if (!$session_data) return;

        $id_dm = $this->input->post('id_dm');
        $content = trim($this->input->post('content'));
        $content = strip_tags(mb_substr($content, 0, 1000, 'UTF-8'));
        $has_image = !empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK;

        if (!$id_dm || (!$content && !$has_image)) {
            return $this->_json(['error' => 'Pesan tidak boleh kosong.'], 400);
        }

        if (!$this->Dm_model->is_participant($id_dm, $session_data['user_id'])) {
            return $this->_json(['error' => 'Forbidden'], 403);
        }

        // Anti-spam: maks 30 pesan DM per menit
        if (!throttle('dm_send', 30, 1, $session_data['user_id'])) {
            return $this->_json(['error' => 'Terlalu banyak pesan. Coba beberapa saat lagi.'], 429);
        }

        $partner_id = $this->_partner_id($id_dm, $session_data['user_id']);
        if ($partner_id && $this->Dm_model->is_blocked_pair($session_data['user_id'], $partner_id)) {
            return $this->_json(['error' => 'Tidak dapat mengirim pesan pada pengguna ini.'], 403);
        }

        if ($partner_id && $this->Dm_model->is_banned_user($partner_id)) {
            return $this->_json(['error' => 'Penerima tidak dapat menerima pesan saat ini.'], 403);
        }

        if ($has_image) {
            $upload = $this->_upload_image();
            if (!empty($upload['error'])) {
                return $this->_json(['error' => $upload['error']], 400);
            }
            $image_url = $upload['path'];
        } else {
            $image_url = null;
        }

        $is_first = $this->Dm_model->count_messages($id_dm) === 0;

        $message_id = $this->Dm_model->save_message($id_dm, $session_data['user_id'], $content, $image_url);

        if ($is_first && $partner_id) {
            $this->Notification_model->create([
                'id_user'  => $partner_id,
                'type'     => 'dm',
                'actor_id' => $session_data['user_id'],
                'id_dm'    => $id_dm,
            ]);
        }

        $this->Activity_model->log(
            $session_data['user_id'], $session_data['username'],
            'send_dm', 'dm', $id_dm,
            'Mengirim pesan pribadi ke user #' . $partner_id
        );

        $message_payload = $this->_message_payload($message_id, $id_dm, $session_data, $content, $image_url);

        $this->_trigger_pusher($id_dm, $message_payload);

        return $this->_json([
            'success' => true,
            'message' => $message_payload,
            'is_first' => $is_first,
        ]);
    }

    public function share_recipients()
    {
        $session_data = $this->_api_user();
        if (!$session_data) return;

        $uid = $session_data['user_id'];
        $esc = $this->db->escape($uid);
        $online_threshold = date('Y-m-d H:i:s', strtotime('-2 minutes'));

        $conv = $this->db->query("
            SELECT p.id_user, p.username, p.display_name, p.avatar, p.verified, p.last_activity,
                   b.image_url AS border_image,
                   tm.id_message AS last_msg_id
            FROM dm_conversations c
            JOIN users p ON p.id_user = (CASE WHEN c.user_id_a = {$esc} THEN c.user_id_b ELSE c.user_id_a END)
            LEFT JOIN borders b ON p.border_active = b.id_border
            LEFT JOIN dm_messages tm ON tm.id_message = (SELECT id_message FROM dm_messages WHERE id_dm = c.id_dm AND deleted = 0 ORDER BY id_message DESC LIMIT 1)
            LEFT JOIN blocked_users bu1 ON bu1.blocker_id = p.id_user AND bu1.blocked_id = {$esc}
            LEFT JOIN blocked_users bu2 ON bu2.blocker_id = {$esc} AND bu2.blocked_id = p.id_user
            WHERE (c.user_id_a = {$esc} OR c.user_id_b = {$esc})
              AND p.status <> 'banned'
              AND bu1.id_block IS NULL AND bu2.id_block IS NULL
            ORDER BY tm.id_message DESC
            LIMIT 15
        ")->result_array();

        $fol = $this->db->query("
            SELECT u.id_user, u.username, u.display_name, u.avatar, u.verified, u.last_activity,
                   b.image_url AS border_image
            FROM follows f
            JOIN users u ON u.id_user = f.id_following
            LEFT JOIN borders b ON u.border_active = b.id_border
            LEFT JOIN blocked_users bu1 ON bu1.blocker_id = u.id_user AND bu1.blocked_id = {$esc}
            LEFT JOIN blocked_users bu2 ON bu2.blocker_id = {$esc} AND bu2.blocked_id = u.id_user
            WHERE f.id_followers = {$esc}
              AND u.status <> 'banned'
              AND bu1.id_block IS NULL AND bu2.id_block IS NULL
            ORDER BY u.display_name ASC
            LIMIT 30
        ")->result_array();

        $users = [];
        $seen = [];
        foreach ($conv as $row) {
            if (isset($seen[$row['id_user']])) continue;
            $seen[$row['id_user']] = true;
            $users[] = [
                'id_user'     => (string) $row['id_user'],
                'username'    => $row['username'],
                'display_name' => $row['display_name'],
                'avatar'      => avatar_url($row['avatar']),
                'verified'    => (int) $row['verified'] === 1,
                'is_online'   => !empty($row['last_activity']) && $row['last_activity'] >= $online_threshold,
                'border'      => !empty($row['border_image']) ? assets_url($row['border_image']) : null,
                'is_recent'   => true,
            ];
            if (count($users) >= 30) break;
        }
        foreach ($fol as $row) {
            if (isset($seen[$row['id_user']])) continue;
            $seen[$row['id_user']] = true;
            $users[] = [
                'id_user'     => (string) $row['id_user'],
                'username'    => $row['username'],
                'display_name' => $row['display_name'],
                'avatar'      => avatar_url($row['avatar']),
                'verified'    => (int) $row['verified'] === 1,
                'is_online'   => !empty($row['last_activity']) && $row['last_activity'] >= $online_threshold,
                'border'      => !empty($row['border_image']) ? assets_url($row['border_image']) : null,
                'is_recent'   => false,
            ];
            if (count($users) >= 30) break;
        }

        return $this->_json($users);
    }

    public function share_search()
    {
        $session_data = $this->_api_user();
        if (!$session_data) return;

        $q = trim((string) $this->input->get('q'));
        if ($q === '') {
            return $this->_json([]);
        }
        $q = mb_substr($q, 0, 50);

        $uid = $session_data['user_id'];
        $rows = $this->Post_model->search_users($q, 20, 0, $uid);

        $users = [];
        foreach ($rows as $row) {
            if ((string) $row['id_user'] === (string) $uid) continue;
            $users[] = [
                'id_user'     => (string) $row['id_user'],
                'username'    => $row['username'],
                'display_name'=> $row['display_name'],
                'avatar'      => $row['avatar'],
                'verified'    => (int) $row['verified'] === 1,
                'is_online'   => !!$row['is_online'],
                'border'      => $row['border'],
                'is_recent'   => false,
            ];
        }
        return $this->_json($users);
    }

    public function share_send()
    {
        $session_data = $this->_api_user();
        if (!$session_data) return;

        $id_user = $this->input->post('id_user');
        $id_post = trim((string) $this->input->post('id_post'));

        if (!$id_user || !$id_post) {
            return $this->_json(['error' => 'Penerima atau postingan tidak valid.'], 400);
        }

        if ((string) $id_user === (string) $session_data['user_id']) {
            return $this->_json(['error' => 'Tidak dapat mengirim DM ke diri sendiri.'], 400);
        }

        $target = $this->Dm_model->get_user_for_dm_by_id($id_user);
        if (!$target || ($target['status'] ?? 'active') === 'banned') {
            return $this->_json(['error' => 'Pengguna tidak ditemukan.'], 404);
        }

        if ($this->Dm_model->is_blocked_pair($session_data['user_id'], $id_user)) {
            return $this->_json(['error' => 'Tidak dapat mengirim pesan pada pengguna ini.'], 403);
        }

        $post = $this->db
            ->select('p.id_post, u.username')
            ->from('posts p')
            ->join('users u', 'u.id_user = p.user_id')
            ->where('p.id_post', $id_post)
            ->where('(p.deleted IS NULL OR p.deleted = 0)')
            ->get()
            ->row_array();

        if (!$post) {
            return $this->_json(['error' => 'Postingan tidak ditemukan.'], 404);
        }

        $content = base_url('post/' . rawurlencode($post['username']) . '/' . $post['id_post']);

        $result = $this->Dm_model->get_or_create_conversation($session_data['user_id'], $id_user);
        $id_dm = $result['id_dm'];

        $is_first = $this->Dm_model->count_messages($id_dm) === 0;
        $message_id = $this->Dm_model->save_message($id_dm, $session_data['user_id'], $content, null, $id_post);

        if ($is_first) {
            $this->Notification_model->create([
                'id_user'  => $id_user,
                'type'     => 'dm',
                'actor_id' => $session_data['user_id'],
                'id_dm'    => $id_dm,
            ]);
        }

        $this->Activity_model->log(
            $session_data['user_id'], $session_data['username'],
            'send_dm', 'dm', $id_dm,
            'Membagikan postingan lewat DM ke user #' . $id_user
        );

        $message_payload = $this->_message_payload($message_id, $id_dm, $session_data, $content, null, $id_post);
        $this->_trigger_pusher($id_dm, $message_payload);

        return $this->_json([
            'success'  => true,
            'id_dm'    => (string) $id_dm,
            'url'      => base_url('dm/conversation/' . $id_dm),
            'message'  => $message_payload,
            'is_first' => $is_first,
        ]);
    }

    private function _load_post_card($id_post)
    {
        $row = $this->db->query("
            SELECT p.id_post, p.content, p.created_at, u.username, u.avatar,
                   (SELECT pm.file_url FROM post_media pm WHERE pm.id_post = p.id_post ORDER BY pm.id ASC LIMIT 1) AS media_url
            FROM posts p
            JOIN users u ON u.id_user = p.user_id
            WHERE p.id_post = " . $this->db->escape($id_post) . "
              AND (p.deleted IS NULL OR p.deleted = 0)
        ")->row_array();

        if (!$row) return null;

        $media = $row['media_url'];
        if ($media) {
            if (strpos($media, 'http') !== 0) {
                if (strpos($media, 'uploads/') === 0) $media = base_url($media);
                else $media = assets_url($media);
            }
        }

        return [
            'id_post'    => $row['id_post'],
            'username'   => $row['username'],
            'avatar'     => $row['avatar'] ? avatar_url($row['avatar']) : null,
            'content'    => $row['content'],
            'created_at' => $row['created_at'],
            'media'      => $media ?: null,
        ];
    }

    private function _message_payload($id_message, $id_dm, $session_data, $content, $image_url = null, $post_id = null)
    {
        $payload = [
            'id_message' => (string) $id_message,
            'id_dm'      => (string) $id_dm,
            'sender_id'  => (string) $session_data['user_id'],
            'username'   => $session_data['username'],
            'avatar'     => avatar_url($session_data['profile_pic']),
            'content'    => $content,
            'image_url'  => $image_url ? base_url($image_url) : null,
            'post_id'    => $post_id ? (string) $post_id : null,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($post_id) {
            $post = $this->_load_post_card($post_id);
            if ($post) $payload['post'] = $post;
        }

        return $payload;
    }

    public function get_messages()
    {
        $session_data = $this->_api_user();
        if (!$session_data) return;

        $id_dm = $this->input->get('id_dm');
        $before_id = $this->input->get('before_id');

        if (!$id_dm || !$this->Dm_model->is_participant($id_dm, $session_data['user_id'])) {
            return $this->_json(['error' => 'Invalid conversation'], 400);
        }

        $partner_id = $this->_partner_id($id_dm, $session_data['user_id']);
        if ($partner_id && $this->Dm_model->is_blocked_pair($session_data['user_id'], $partner_id)) {
            return $this->_json([]);
        }

        $messages = $this->Dm_model->get_messages($id_dm, $session_data['user_id'], 50, $before_id);
        foreach ($messages as &$msg) {
            if (!empty($msg['image_url'])) {
                $msg['image_url'] = (strpos($msg['image_url'], 'http') === 0) ? $msg['image_url'] : base_url($msg['image_url']);
            }
        }

        $this->Dm_model->mark_read($id_dm, $session_data['user_id']);

        return $this->_json($messages);
    }

    public function get_unread_count()
    {
        $session_data = $this->session->userdata('user_logged_in');
        $count = $session_data ? $this->Dm_model->count_unread($session_data['user_id']) : 0;
        return $this->_json(['count' => $count]);
    }

    public function pusher_auth()
    {
        $session_data = $this->_api_user();
        if (!$session_data) return;

        $channel_name = $this->input->post('channel_name');
        $socket_id = $this->input->post('socket_id');

        if (!$channel_name || !$socket_id) {
            return $this->_json(['error' => 'Missing params'], 400);
        }

        $id_dm = (int) str_replace('private-dm-', '', $channel_name);
        if (!$id_dm || !$this->Dm_model->is_participant($id_dm, $session_data['user_id'])) {
            return $this->_json(['error' => 'Invalid channel'], 403);
        }

        $app_id  = $this->config->item('pusher_app_id', 'pusher');
        $key     = $this->config->item('pusher_key', 'pusher');
        $secret  = $this->config->item('pusher_secret', 'pusher');
        $cluster = $this->config->item('pusher_cluster', 'pusher');

        if (!$app_id || !$key || !$secret) {
            return $this->_json(['error' => 'Pusher not configured'], 500);
        }

        try {
            $pusher = new Pusher\Pusher($key, $secret, $app_id, ['cluster' => $cluster]);
            $auth = $pusher->authorizeChannel($channel_name, $socket_id);
            $this->output
                ->set_content_type('application/json')
                ->set_output($auth);
        } catch (Exception $e) {
            log_coded_error('PPS-5002', 'Pusher DM auth failed: ' . $e->getMessage());
            return $this->_json(['error' => 'Auth failed'], 500);
        }
    }

    public function delete_conversation()
    {
        $session_data = $this->_api_user();
        if (!$session_data) return;

        $id_dm = $this->input->post('id_dm');
        if (!$id_dm || !$this->Dm_model->is_participant($id_dm, $session_data['user_id'])) {
            return $this->_json(['error' => 'Invalid conversation'], 403);
        }

        $this->Dm_model->delete_conversation($id_dm);

        $this->Activity_model->log(
            $session_data['user_id'], $session_data['username'],
            'delete_dm', 'dm', $id_dm,
            'Menghapus percakapan DM #' . $id_dm
        );

        return $this->_json(['success' => true]);
    }

    public function delete_message()
    {
        $session_data = $this->_api_user();
        if (!$session_data) return;

        $id_message = $this->input->post('id_message');
        if (!$id_message) {
            return $this->_json(['error' => 'Missing message'], 400);
        }

        if ($this->Dm_model->soft_delete_message($id_message, $session_data['user_id'])) {
            return $this->_json(['success' => true]);
        }

        return $this->_json(['error' => 'Gagal menghapus pesan.'], 403);
    }
}