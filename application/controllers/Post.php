<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Post_model');
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

        $data['comments'] = $this->Post_model->get_post_comments($id_post, $current_user_id);

        if ($session_data) {
            $profile_pic = $session_data['profile_pic'] ?? 'default.jpg';
            $data['current_user_avatar'] = (strpos($profile_pic, 'http') === 0)
                ? $profile_pic
                : assets_url($profile_pic);
        } else {
            $data['current_user_avatar'] = assets_url('default.jpg');
        }

        $data['title'] = "Postingan oleh @" . $data['post']['username'] . " | PaddockID";

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('detail-post', $data);
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

        $save_data = [
            'id_post'   => (int) $id_post,
            'user_id'   => (int) $session_data['user_id'],
            'content'   => $comment_text,
            'parent_id' => ($parent_id > 0) ? (int) $parent_id : NULL,
        ];

        $this->db->insert('post_comments', $save_data);
        $insert_id = $this->db->insert_id();

        $profile_pic = $session_data['profile_pic'] ?? 'default.jpg';
        $user_avatar = (strpos($profile_pic, 'http') === 0)
            ? $profile_pic
            : assets_url($profile_pic);

        $response = [
            'status' => 'success',
            'message' => 'Komentar berhasil dikirim!',
            'new_comment' => [
                'id_comment' => $insert_id,
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

            $id_comment = (int) $id_comment;
            $user_id = (int) $session_data['user_id'];

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

    public function report($id_post = NULL) {
        if (empty($id_post)) {
            show_404();
        }

        $data['title'] = 'Report Post | PaddockID';
        $data['id_post'] = $id_post;

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $data['content'] = '<div class="glass-card p-8 text-center text-slate-400"><p class="text-sm">Laporan untuk postingan #' . $id_post . ' telah diterima.</p></div>';
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }
}
