<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Post_model');
        $this->load->model('Notification_model');
        $this->load->helper('waktu_helper');
    }

    public function index($username = NULL)
    {
        if (empty($username)) {
            show_404();
        }

        $session_data = $this->session->userdata('user_logged_in');
        $current_user_id = $session_data ? $session_data['user_id'] : 0;

        $user = $this->Post_model->get_user_by_username($username, $current_user_id);

        if (!$user) {
            show_404();
        }

        // Redirect ke profil sendiri jika akses /user/{username} milik sendiri
        if ($session_data && $session_data['user_id'] === $user['id_user']) {
            redirect('profile');
        }

        $data['title'] = $user['display_name'] . ' (@' . $user['username'] . ') | PaddockID';
        $data['user'] = $user;
        $data['current_user_id'] = $current_user_id;
        $data['profile_user_id'] = $user['id_user'];

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('user_view', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function get_user_posts_ajax()
    {
        try {
            $offset = intval($this->input->get('offset', true));
            $limit = intval($this->input->get('limit', true));
            $user_id = $this->input->get('user_id', true);

            if (!$user_id) {
                throw new Exception('Invalid user_id');
            }

            $session_data = $this->session->userdata('user_logged_in');
            $current_user_id = $session_data ? $session_data['user_id'] : 0;

            // Jika current_user diblokir oleh pemilik profil, jangan tampilkan postingan
            if ($current_user_id && $current_user_id !== $user_id) {
                $this->db->where('blocker_id', $user_id);
                $this->db->where('blocked_id', $current_user_id);
                $is_blocked_by_author = $this->db->get('blocked_users')->num_rows() > 0;
                if ($is_blocked_by_author) {
                    return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(200)
                        ->set_output(json_encode([]));
                }
            }

            $posts = $this->Post_model->get_user_posts($user_id, $limit, $offset, $current_user_id);

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

    public function get_follows_ajax()
    {
        try {
            $type = $this->input->get('type', true);
            $user_id = $this->input->get('user_id', true);

            if (!$type || !in_array($type, ['following', 'followers'])) {
                throw new Exception('Invalid type parameter');
            }

            if (!$user_id) {
                throw new Exception('Invalid user_id');
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

            foreach ($result as &$row) {
                $row['avatar'] = avatar_url($row['avatar']);
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

    public function toggle_follow()
    {
        try {
            $session_data = $this->session->userdata('user_logged_in');
            if (!$session_data) {
                throw new Exception('Authentication required');
            }

            $follower_id = $session_data['user_id'];
            $following_id = $this->input->post('user_id', true);

            if (!$following_id || $follower_id === $following_id) {
                throw new Exception('Invalid user_id');
            }

            // Cek block 2 arah
            $ef = $this->db->escape($follower_id);
            $eg = $this->db->escape($following_id);
            $this->db->where("(blocker_id = {$eg} AND blocked_id = {$ef}) OR (blocker_id = {$ef} AND blocked_id = {$eg})");
            if ($this->db->get('blocked_users')->num_rows() > 0) {
                throw new Exception('Tidak dapat mengikuti pengguna ini');
            }

            $result = $this->Post_model->toggle_follow($follower_id, $following_id);

            if ($result['action'] === 'followed' && $following_id !== $follower_id) {
                $this->Notification_model->create([
                    'id_user'  => $following_id,
                    'type'     => 'follow',
                    'actor_id' => $follower_id,
                ]);
            }

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => 'success',
                    'action' => $result['action'],
                    'followers_count' => $result['followers_count']
                ]));
        } catch (Exception $e) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        }
    }

    public function report_user()
    {
        try {
            $session_data = $this->session->userdata('user_logged_in');
            if (!$session_data) {
                throw new Exception('Authentication required');
            }

            $reporter_id = $session_data['user_id'];
            $reported_id = $this->input->post('user_id', true);
            $reason = trim($this->input->post('reason', true));

            if (!$reported_id || $reporter_id === $reported_id) {
                throw new Exception('Invalid user_id');
            }

            if (empty($reason)) {
                throw new Exception('Alasan laporan harus diisi.');
            }

            $this->Post_model->report_user($reporter_id, $reported_id, $reason);

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'Laporan berhasil dikirim.'
                ]));
        } catch (Exception $e) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        }
    }

    public function block_user()
    {
        try {
            $session_data = $this->session->userdata('user_logged_in');
            if (!$session_data) {
                throw new Exception('Authentication required');
            }

            $blocker_id = $session_data['user_id'];
            $blocked_id = $this->input->post('user_id', true);

            if (!$blocked_id || $blocker_id === $blocked_id) {
                throw new Exception('Invalid user_id');
            }

            $result = $this->Post_model->block_user($blocker_id, $blocked_id);

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => 'success',
                    'action' => $result['action'],
                    'message' => 'Pengguna berhasil diblokir.'
                ]));
        } catch (Exception $e) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        }
    }

    public function unblock_user()
    {
        try {
            $session_data = $this->session->userdata('user_logged_in');
            if (!$session_data) {
                throw new Exception('Authentication required');
            }

            $blocker_id = $session_data['user_id'];
            $blocked_id = $this->input->post('user_id', true);

            if (!$blocked_id || $blocker_id === $blocked_id) {
                throw new Exception('Invalid user_id');
            }

            $result = $this->Post_model->unblock_user($blocker_id, $blocked_id);

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => 'success',
                    'action' => $result['action'],
                    'message' => 'Blokir berhasil dibatalkan.'
                ]));
        } catch (Exception $e) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        }
    }
}
