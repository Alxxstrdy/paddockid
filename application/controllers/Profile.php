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

        // Ambil data user, data border, team, serta total following, followers & posts
            $this->db->select('u.*, b.image_url as border_image, t.team_name, t.team_logo, t.team_color');
        
        $this->db->select('(SELECT COUNT(*) FROM follows WHERE id_followers = u.id_user) as total_following');
        
        $this->db->select('(SELECT COUNT(*) FROM follows WHERE id_following = u.id_user) as total_followers');
        
        $this->db->select('(SELECT COUNT(*) FROM posts WHERE user_id = u.id_user AND (deleted IS NULL OR deleted = 0)) as total_posts');
        
        $this->db->from('users u');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->join('team t', 'u.team_id = t.team_id', 'left');
        $this->db->where('u.id_user', $user_id);
        
        $data['user'] = $this->db->get()->row_array();
        $data['title'] = $data['user']['display_name'] . ' (@' . $data['user']['username'] . ') | PaddockID';

        $this->load->model('Auth_model');
        $data['teams'] = $this->Auth_model->get_all_teams();

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('profile_view', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function edit_profile_page()
    {
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            redirect('auth');
        }

        $user_id = $session_data['user_id'];

        $this->db->select('u.*, b.image_url as border_image, t.team_name, t.team_logo, t.team_color');
        $this->db->from('users u');
        $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
        $this->db->join('team t', 'u.team_id = t.team_id', 'left');
        $this->db->where('u.id_user', $user_id);

        $data['user'] = $this->db->get()->row_array();
        $data['title'] = 'Edit Profil | PaddockID';

        $this->load->model('Auth_model');
        $data['teams'] = $this->Auth_model->get_all_teams();

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('edit_profile', $data);
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
            $user_id = $this->input->get('user_id', true);

            if (!$type || !in_array($type, ['following', 'followers'])) {
                throw new Exception('Invalid type parameter');
            }

            if (!$user_id) {
                $session_data = $this->session->userdata('user_logged_in');
                $user_id = $session_data['user_id'];
            }

            $this->db->select('u.id_user, u.username, u.display_name, u.avatar, u.verified, u.last_activity, b.image_url as border_image');
            $this->db->from('follows f');

            if ($type === 'following') {
                $this->db->join('users u', 'f.id_following = u.id_user');
                $this->db->where('f.id_followers', $user_id);
            } else {
                $this->db->join('users u', 'f.id_followers = u.id_user');
                $this->db->where('f.id_following', $user_id);
            }

            $this->db->join('borders b', 'u.border_active = b.id_border', 'left');

            // Exclude user yang saling block
            $session_data = $this->session->userdata('user_logged_in');
            if ($session_data) {
                $cu = $this->db->escape($session_data['user_id']);
                $this->db->join('blocked_users bu1', "bu1.blocker_id = {$cu} AND bu1.blocked_id = u.id_user", 'left');
                $this->db->join('blocked_users bu2', "bu2.blocker_id = u.id_user AND bu2.blocked_id = {$cu}", 'left');
                $this->db->where('bu1.id_block IS NULL AND bu2.id_block IS NULL');
            }

            $this->db->order_by('f.created_at', 'DESC');

            $result = $this->db->get()->result_array();

            $online_threshold = date('Y-m-d H:i:s', strtotime('-2 minutes'));
            foreach ($result as &$row) {
                $row['avatar'] = avatar_url($row['avatar']);
                $row['border_image'] = !empty($row['border_image'])
                    ? assets_url($row['border_image'])
                    : null;
                $row['is_online'] = !empty($row['last_activity']) && $row['last_activity'] >= $online_threshold;
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
            $profile_user_id = intval($this->input->get('user_id', true));

            if (!$type || !in_array($type, ['uploads', 'liked'])) {
                throw new Exception('Invalid type parameter');
            }

            $session_data = $this->session->userdata('user_logged_in');
            $current_user_id = $session_data['user_id'];
            $target_user_id = $profile_user_id > 0 ? $profile_user_id : $current_user_id;

            if ($type === 'uploads') {
                $posts = $this->Post_model->get_user_posts($target_user_id, $limit, $offset, $current_user_id);
            } else {
                $posts = $this->Post_model->get_liked_posts($target_user_id, $limit, $offset, $current_user_id);
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

    public function edit_profile()
    {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        $user_id = $session_data['user_id'];

        $display_name = trim($this->input->post('display_name', true));
        $bio = trim($this->input->post('bio', true));
        $team_id = $this->input->post('team_id', true);

        $update_data = [];
        if (!empty($display_name)) {
            $update_data['display_name'] = $display_name;
        }
        $update_data['bio'] = !empty($bio) ? $bio : null;

        // Team F1
        if ($team_id !== null && $team_id !== '') {
            $update_data['team_id'] = $team_id ? (int) $team_id : null;
        }

        // Upload avatar
        if (!empty($_FILES['avatar']['name'])) {
            $upload_path = FCPATH . 'uploads/avatars/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            $config['upload_path']   = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
            $config['max_size']      = 5120;
            $config['encrypt_name']  = true;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('avatar')) {
                $upload_data = $this->upload->data();
                $update_data['avatar'] = 'uploads/avatars/' . $upload_data['file_name'];
            } else {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => strip_tags($this->upload->display_errors())
                    ]));
            }
        }

        // Hapus foto profil (kembalikan ke default)
        if ($this->input->post('remove_avatar')) {
            $update_data['avatar'] = 'default.jpg';
        }

        // Upload banner
        if (!empty($_FILES['banner']['name'])) {
            $upload_path = FCPATH . 'uploads/banners/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            $config['upload_path']   = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
            $config['max_size']      = 10240;
            $config['encrypt_name']  = true;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('banner')) {
                $upload_data = $this->upload->data();
                $update_data['banner'] = 'uploads/banners/' . $upload_data['file_name'];
            } else {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => strip_tags($this->upload->display_errors())
                    ]));
            }
        }

        // Hapus banner (kembalikan ke default/null)
        if ($this->input->post('remove_banner')) {
            $update_data['banner'] = null;
        }

        if (!empty($update_data)) {
            $this->db->where('id_user', $user_id);
            $this->db->update('users', $update_data);

            // Update session
            if (isset($update_data['display_name'])) {
                $session_data['fullname'] = $update_data['display_name'];
            }
            if (isset($update_data['avatar'])) {
                $session_data['profile_pic'] = $update_data['avatar'];
            }
            $this->session->set_userdata('user_logged_in', $session_data);

            // Refresh user data
        $this->db->select('u.*, b.image_url as border_image, t.team_name, t.team_logo, t.team_color');
            $this->db->from('users u');
            $this->db->join('borders b', 'u.border_active = b.id_border', 'left');
            $this->db->join('team t', 'u.team_id = t.team_id', 'left');
            $this->db->where('u.id_user', $user_id);
            $user = $this->db->get()->row_array();

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'Profil berhasil diperbarui!',
                    'user' => [
                        'display_name' => $user['display_name'],
                        'username' => $user['username'],
                        'avatar' => avatar_url($user['avatar']),
                        'banner' => !empty($user['banner']) ? base_url($user['banner']) : null,
                        'bio' => $user['bio'],
                        'border_image' => $user['border_image'] ? assets_url($user['border_image']) : null,
                        'verified' => $user['verified'],
                        'team_id' => $user['team_id'],
                        'team_name' => $user['team_name'],
                        'team_logo' => $user['team_logo'],
                        'team_color' => $user['team_color']
                    ]
                ]));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'message' => 'Tidak ada perubahan.']));
    }
}