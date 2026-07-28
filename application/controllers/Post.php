<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Post_model');
        $this->load->model('Notification_model');
        $this->load->model('Activity_model');
        $this->load->helper('waktu_helper');
    }

    public function index($username = NULL, $id_post = NULL) {
        if (empty($id_post)) {
            show_404();
        }
        $session_data = $this->session->userdata('user_logged_in');
        $current_user_id = $session_data ? $session_data['user_id'] : 0;

        $data['post'] = $this->Post_model->get_post_by_id($id_post, $current_user_id);

        if (!$data['post']) {
            show_404();
        }

        // Jika current_user diblokir oleh author post, jangan tampilkan
        if ($current_user_id && !empty($data['post']['user_id'])) {
            $this->db->where('blocker_id', $data['post']['user_id']);
            $this->db->where('blocked_id', $current_user_id);
            if ($this->db->get('blocked_users')->num_rows() > 0) {
                show_404();
            }
        }

        $data['comments'] = $this->Post_model->get_post_comments($id_post, $current_user_id);

        if ($session_data) {
            $data['current_user_avatar'] = avatar_url($session_data['profile_pic'] ?? 'default.jpg');
        } else {
            $data['current_user_avatar'] = assets_url('default.jpg');
        }

        $data['title'] = "Postingan oleh @" . $data['post']['username'] . " | PaddockID";
        $data['current_user_id'] = $current_user_id;

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('detail-post', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function create_post_page()
    {
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            redirect('auth');
        }

        $data['title'] = 'Buat Postingan | PaddockID';

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('create-post', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function edit_post_page($id_post = NULL)
    {
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            redirect('auth');
        }

        if (empty($id_post)) {
            show_404();
        }

        $post = $this->Post_model->get_post_by_id($id_post, $session_data['user_id']);
        if (!$post) {
            show_404();
        }

        if ((string)$post['user_id'] !== (string)$session_data['user_id']) {
            show_404();
        }

        $data['post'] = $post;
        $data['categories'] = $this->Post_model->get_categories();
        $data['title'] = 'Edit Postingan | PaddockID';

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('edit-post', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function add_comment() {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']));
        }

        $id_post      = $this->input->post('id_post', TRUE);
        $comment_text = $this->input->post('comment_text', TRUE);
        $parent_id    = $this->input->post('parent_id', TRUE);

        if (empty($id_post) || empty($comment_text)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Komentar tidak boleh kosong.']));
        }

        // Generate random comment ID
        do {
            $id_comment = (string) random_int(100000000, 999999999);
        } while ($this->db->get_where('post_comments', ['id_comment' => $id_comment])->num_rows() > 0);

        $save_data = [
            'id_comment' => $id_comment,
            'id_post'   => $id_post,
            'user_id'   => $session_data['user_id'],
            'content'   => $comment_text,
            'parent_id' => ($parent_id > 0) ? $parent_id : NULL,
        ];

        $this->db->insert('post_comments', $save_data);

        $this->Activity_model->log(
            $session_data['user_id'], $session_data['username'],
            'add_comment', 'comment', $id_comment,
            'Mengomentari post #' . $id_post
        );

        if ($parent_id > 0) {
            $parent = $this->db->select('user_id, id_post')
                ->from('post_comments')
                ->where('id_comment', $parent_id)
                ->get()
                ->row_array();
            if ($parent && $parent['user_id'] !== $session_data['user_id']) {
                $this->Notification_model->create([
                    'id_user'    => $parent['user_id'],
                    'type'       => 'reply',
                    'actor_id'   => $session_data['user_id'],
                    'id_post'    => $parent['id_post'],
                    'id_comment' => $id_comment
                ]);
            }
        } else {
            $post = $this->db->select('user_id')
                ->from('posts')
                ->where('id_post', $id_post)
                ->get()
                ->row_array();

            if ($post && $post['user_id'] !== $session_data['user_id']) {
                $this->Notification_model->create([
                    'id_user'  => $post['user_id'],
                    'type'     => 'comment',
                    'actor_id' => $session_data['user_id'],
                    'id_post'  => $id_post,
                ]);
            }
        }

        $user_avatar = avatar_url($session_data['profile_pic'] ?? 'default.jpg');

        $response = [
            'status' => 'success',
            'message' => 'Komentar berhasil dikirim!',
            'new_comment' => [
                'id_comment' => $id_comment,
                'username'   => $session_data['username'] ?? 'user',
                'avatar'     => $user_avatar,
                'created_at' => 'Baru saja'
            ]
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function toggle_like_comment($id_comment = NULL) {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        if (empty($id_comment)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => 'ID Komentar tidak ditemukan.']));
        }

        try {
            $session_data = $this->session->userdata('user_logged_in');
            if (!$session_data) {
                throw new Exception("Kamu harus login terlebih dahulu.");
            }

            $user_id = $session_data['user_id'];

            $check = $this->db->get_where('comment_likes', [
                'comment_id' => $id_comment,
                'user_id'    => $user_id
            ])->row();

            if ($check) {
                $this->db->delete('comment_likes', [
                    'comment_id' => $id_comment,
                    'user_id'    => $user_id
                ]);
                $action = 'unliked';
            } else {
                $this->db->insert('comment_likes', [
                    'comment_id' => $id_comment,
                    'user_id'    => $user_id
                ]);
                $action = 'liked';
            }

            $this->Activity_model->log(
                $session_data['user_id'], $session_data['username'],
                $action === 'liked' ? 'like_comment' : 'unlike_comment',
                'comment', $id_comment,
                ($action === 'liked' ? 'Menyukai' : 'Tidak menyukai') . ' komentar #' . $id_comment
            );

            $likes_count = $this->db->where('comment_id', $id_comment)
                ->count_all_results('comment_likes');

            $response = [
                'status' => 'success',
                'action' => $action,
                'likes_count' => $likes_count
            ];

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        }
    }

    public function create_post()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']));
        }

        $content    = trim($this->input->post('content', true));
        $category   = $this->input->post('category', true);

        if (empty($content)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Konten tidak boleh kosong.']));
        }

        // Upload images
        $media_files = [];
        if (!empty($_FILES['images']['name'][0])) {
            $total_files = count($_FILES['images']['name']);

            if ($total_files > 4) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Maksimal 4 gambar per postingan.']));
            }

            $upload_path = FCPATH . 'uploads/posts/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $max_size = 10 * 1024 * 1024; // 10 MB

            foreach ($_FILES['images']['name'] as $key => $name) {
                if ($_FILES['images']['error'][$key] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed_types)) {
                    continue;
                }

                if ($_FILES['images']['size'][$key] > $max_size) {
                    continue;
                }

                $new_name = md5(uniqid(mt_rand(), true)) . '.' . $ext;
                $dest = $upload_path . $new_name;

                if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $dest)) {
                    $media_files[] = 'uploads/posts/' . $new_name;
                }
            }
        }

        $id_post = $this->Post_model->create_post(
            $session_data['user_id'],
            $content,
            $category,
            $media_files
        );

        if ($id_post) {
            $this->Activity_model->log(
                $session_data['user_id'], $session_data['username'],
                'create_post', 'post', $id_post,
                'Membuat postingan baru'
            );
            $post = $this->Post_model->get_post_by_id($id_post, $session_data['user_id']);
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'Postingan berhasil dibuat!',
                    'post' => $post
                ]));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal membuat postingan.']));
    }

    public function report()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']));
        }

        $id_post = $this->input->post('id_post', true);
        $reason  = trim($this->input->post('reason', true));

        if (empty($id_post) || empty($reason)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Alasan laporan harus diisi.']));
        }

        $inserted = $this->Post_model->report_post($id_post, $session_data['user_id'], $reason);

        if ($inserted) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Laporan berhasil dikirim.']));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(500)
            ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal mengirim laporan.']));
    }

    public function report_comment()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']));
        }

        $id_comment = $this->input->post('id_comment', true);
        $reason     = trim($this->input->post('reason', true));

        if (empty($id_comment) || empty($reason)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Alasan laporan harus diisi.']));
        }

        $inserted = $this->Post_model->report_comment($id_comment, $session_data['user_id'], $reason);

        if ($inserted) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Laporan berhasil dikirim.']));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(500)
            ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal mengirim laporan.']));
    }

    public function edit_post()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']));
        }

        $id_post = $this->input->post('id_post', true);
        $content  = trim($this->input->post('content', true));
        $category = $this->input->post('category', true);

        if (empty($id_post) || empty($content)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Konten tidak boleh kosong.']));
        }

        $updated = $this->Post_model->update_post($id_post, $session_data['user_id'], $content, $category);

        if ($updated) {
            $this->Activity_model->log(
                $session_data['user_id'], $session_data['username'],
                'edit_post', 'post', $id_post,
                'Mengedit postingan #' . $id_post
            );
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Postingan berhasil diedit.']));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal mengedit postingan atau kamu bukan pemiliknya.']));
    }

    public function delete_post()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']));
        }

        $id_post = $this->input->post('id_post', true);

        if (empty($id_post)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => 'ID Postingan tidak ditemukan.']));
        }

        $deleted = $this->Post_model->delete_post($id_post, $session_data['user_id']);

        if ($deleted) {
            $this->Activity_model->log(
                $session_data['user_id'], $session_data['username'],
                'delete_post', 'post', $id_post,
                'Menghapus postingan #' . $id_post
            );
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Postingan berhasil dihapus.']));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menghapus postingan atau kamu bukan pemiliknya.']));
    }

    public function edit_comment()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']));
        }

        $id_comment = $this->input->post('id_comment', true);
        $content    = trim($this->input->post('content', true));

        if (empty($id_comment) || empty($content)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Komentar tidak boleh kosong.']));
        }

        $updated = $this->Post_model->update_comment($id_comment, $session_data['user_id'], $content);

        if ($updated) {
            $this->Activity_model->log(
                $session_data['user_id'], $session_data['username'],
                'edit_comment', 'comment', $id_comment,
                'Mengedit komentar #' . $id_comment
            );
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Komentar berhasil diedit.']));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal mengedit komentar atau kamu bukan pemiliknya.']));
    }

    public function delete_comment()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']));
        }

        $id_comment = $this->input->post('id_comment', true);

        if (empty($id_comment)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => 'ID Komentar tidak ditemukan.']));
        }

        $deleted = $this->Post_model->delete_comment($id_comment, $session_data['user_id']);

        if ($deleted) {
            $this->Activity_model->log(
                $session_data['user_id'], $session_data['username'],
                'delete_comment', 'comment', $id_comment,
                'Menghapus komentar #' . $id_comment
            );
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Komentar berhasil dihapus.']));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menghapus komentar atau kamu bukan pemiliknya.']));
    }
}
