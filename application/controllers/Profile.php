<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Post_model');
        $this->load->helper('waktu_helper');
        
        if (!$this->session->userdata('user_logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('auth');
        }
    }

    public function index()
    {
        $session_data = $this->session->userdata('user_logged_in');
        $user_id = $session_data['user_id'];

        // Ambil data user, data border, serta total following, followers & posts
        $this->db->select('u.*, b.image_url as border_image');
        
        $this->db->select('(SELECT COUNT(*) FROM follows WHERE id_followers = u.id_user) as total_following');
        
        $this->db->select('(SELECT COUNT(*) FROM follows WHERE id_following = u.id_user) as total_followers');
        
        $this->db->select('(SELECT COUNT(*) FROM posts WHERE user_id = u.id_user AND (deleted IS NULL OR deleted = 0)) as total_posts');
        
        $this->db->from('users u');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->where('u.id_user', $user_id);
        
        $data['user'] = $this->db->get()->row_array();
        $data['title'] = $data['user']['display_name'] . ' (@' . $data['user']['username'] . ') | PaddockID';

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('profile_view', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    /**
     * AJAX endpoint untuk mendapatkan daftar following/followers
     * @param type 'following' atau 'followers'
     * @param user_id ID user yang profilnya sedang dilihat (default: user login sendiri)
     */
    public function get_follows_ajax()
    {
        try {
            $type = $this->input->get('type', true);
            $user_id = intval($this->input->get('user_id', true));

            if (!$type || !in_array($type, ['following', 'followers'])) {
                throw new Exception('Invalid type parameter');
            }

            if (!$user_id) {
                $session_data = $this->session->userdata('user_logged_in');
                $user_id = $session_data['user_id'];
            }

            $this->db->select('u.id_user, u.username, u.display_name, u.avatar, u.verified, b.image_url as border_image');
            $this->db->from('follows f');

            if ($type === 'following') {
                $this->db->join('users u', 'f.id_following = u.id_user');
                $this->db->where('f.id_followers', $user_id);
            } else {
                $this->db->join('users u', 'f.id_followers = u.id_user');
                $this->db->where('f.id_following', $user_id);
            }

            $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
            $this->db->order_by('f.created_at', 'DESC');

            $result = $this->db->get()->result_array();

            foreach ($result as &$row) {
                $row['avatar'] = !empty($row['avatar'])
                    ? assets_url($row['avatar'])
                    : assets_url('default.jpg');
                $row['border_image'] = !empty($row['border_image'])
                    ? assets_url($row['border_image'])
                    : null;
            }

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($result));
        } catch (Exception $e) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function get_profile_posts_ajax()
    {
        try {
            $type = $this->input->get('type', true);
            $offset = intval($this->input->get('offset', true));
            $limit = intval($this->input->get('limit', true));

            if (!$type || !in_array($type, ['uploads', 'liked'])) {
                throw new Exception('Invalid type parameter');
            }

            $session_data = $this->session->userdata('user_logged_in');
            $user_id = $session_data['user_id'];

            if ($type === 'uploads') {
                $posts = $this->Post_model->get_user_posts($user_id, $limit, $offset, $user_id);
            } else {
                $posts = $this->Post_model->get_liked_posts($user_id, $limit, $offset, $user_id);
            }

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($posts));
        } catch (Exception $e) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['error' => $e->getMessage()]));
        }
    }
}