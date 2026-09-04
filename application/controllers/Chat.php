<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Chat_model');
        $this->load->model('Post_model');
        $this->load->model('Notification_model');
        $this->load->helper('waktu_helper');
        $this->load->helper('assets_url_helper');
        $this->load->config('pusher', TRUE);
    }

    private function _require_login() {
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            redirect('auth');
            return null;
        }
        return $session_data;
    }

    public function index() {
        $session_data = $this->_require_login();
        if (!$session_data) return;

        $this->Chat_model->sync_rooms_from_schedule();

        $data['title'] = 'Live Chat | PaddockID';
        $data['current_user_id'] = $session_data['user_id'];
        $data['active_rooms'] = $this->Chat_model->get_active_rooms();
        $data['upcoming_rooms'] = $this->Chat_model->get_upcoming_rooms();

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('chat/index', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function room($slug = null) {
        $session_data = $this->_require_login();
        if (!$session_data) return;

        $this->Chat_model->sync_rooms_from_schedule();

        $room = $this->Chat_model->get_room_by_slug($slug);
        if (!$room) {
            show_404();
            return;
        }

        $data['title'] = $room['session_name'] . ' - ' . $room['race_name'] . ' | PaddockID';
        $data['current_user_id'] = $session_data['user_id'];
        $data['session_data'] = $session_data;
        $data['room'] = $room;
        $data['pusher_key'] = $this->config->item('pusher_key', 'pusher');
        $data['pusher_cluster'] = $this->config->item('pusher_cluster', 'pusher');

        $this->load->view('layout/header', $data);
        $this->load->view('chat/room', $data);
        $this->load->view('layout/footer');
    }

    public function send_message() {
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            $this->output->set_status_header(401)->set_output(json_encode(['error' => 'Unauthorized']));
            return;
        }

        $id_room = $this->input->post('id_room');
        $content = trim($this->input->post('content'));

        if (!$id_room || !$content) {
            $this->output->set_status_header(400)->set_output(json_encode(['error' => 'Missing fields']));
            return;
        }

        $room = $this->Chat_model->get_room_by_id($id_room);
        if (!$room || $room['room_status'] !== 'active') {
            $this->output->set_status_header(403)->set_output(json_encode(['error' => 'Room is not active']));
            return;
        }

        $content = strip_tags(mb_substr($content, 0, 1000, 'UTF-8'));
        if (trim($content) === '') {
            $this->output->set_status_header(400)->set_output(json_encode(['error' => 'Missing fields']));
            return;
        }
        $message_data = [
            'id_room'  => $id_room,
            'user_id'  => $session_data['user_id'],
            'content'  => $content,
        ];

        $message_id = $this->Chat_model->save_message($message_data);

        $pusher_app_id = $this->config->item('pusher_app_id', 'pusher');
        $pusher_key = $this->config->item('pusher_key', 'pusher');
        $pusher_secret = $this->config->item('pusher_secret', 'pusher');
        $pusher_cluster = $this->config->item('pusher_cluster', 'pusher');

        if ($pusher_app_id && $pusher_key && $pusher_secret) {
            try {
                $pusher = new Pusher\Pusher($pusher_key, $pusher_secret, $pusher_app_id, ['cluster' => $pusher_cluster]);
                $pusher->trigger('private-chat-' . $room['slug'], 'new-message', [
                    'id_message' => (string) $message_id,
                    'user_id'    => (string) $session_data['user_id'],
                    'username'   => $session_data['username'],
                    'avatar'     => avatar_url($session_data['profile_pic']),
                    'content'    => $content,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                log_message('debug', 'Pusher triggered: private-chat-' . $room['slug'] . ' msg_id=' . $message_id);
            } catch (Exception $e) {
                log_coded_error('PPS-4001', 'Pusher trigger failed: ' . $e->getMessage());
            }
        }

        $this->output->set_content_type('application/json')->set_output(json_encode([
            'success' => true,
            'message' => [
                'id_message' => (string) $message_id,
                'user_id'    => (string) $session_data['user_id'],
                'username'   => $session_data['username'],
                'avatar'     => avatar_url($session_data['profile_pic']),
                'content'    => $content,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ]));
    }

    public function get_messages() {
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            $this->output->set_status_header(401)->set_output(json_encode(['error' => 'Unauthorized']));
            return;
        }

        $id_room = $this->input->get('id_room');
        $before_id = $this->input->get('before_id');
        if (!$id_room) {
            $this->output->set_status_header(400)->set_output(json_encode(['error' => 'Missing room ID']));
            return;
        }

        $messages = $this->Chat_model->get_messages($id_room, 50, $before_id);

        foreach ($messages as &$msg) {
            $msg['avatar'] = avatar_url($msg['avatar']);
            unset($msg['deleted']);
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($messages));
    }

    public function pusher_auth() {
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            $this->output->set_status_header(401)->set_output(json_encode(['error' => 'Unauthorized']));
            return;
        }

        $channel_name = $this->input->post('channel_name');
        $socket_id = $this->input->post('socket_id');

        if (!$channel_name || !$socket_id) {
            $this->output->set_status_header(400)->set_output(json_encode(['error' => 'Missing params']));
            return;
        }

        // Validate channel corresponds to a real room
        $slug = str_replace('private-chat-', '', $channel_name);
        $room = $this->Chat_model->get_room_by_slug($slug);
        if (!$room) {
            $this->output->set_status_header(403)->set_output(json_encode(['error' => 'Invalid channel']));
            return;
        }

        $pusher_app_id = $this->config->item('pusher_app_id', 'pusher');
        $pusher_key = $this->config->item('pusher_key', 'pusher');
        $pusher_secret = $this->config->item('pusher_secret', 'pusher');
        $pusher_cluster = $this->config->item('pusher_cluster', 'pusher');

        if (!$pusher_app_id || !$pusher_key || !$pusher_secret) {
            $this->output->set_status_header(500)->set_output(json_encode(['error' => 'Pusher not configured']));
            return;
        }

        try {
            $pusher = new Pusher\Pusher($pusher_key, $pusher_secret, $pusher_app_id, ['cluster' => $pusher_cluster]);
            $auth = $pusher->authorizeChannel($channel_name, $socket_id);
            $this->output
                ->set_content_type('application/json')
                ->set_output($auth);
        } catch (Exception $e) {
            log_coded_error('PPS-4002', 'Pusher auth failed: ' . $e->getMessage());
            $this->output->set_status_header(500)->set_output(json_encode(['error' => 'Auth failed']));
        }
    }
}
