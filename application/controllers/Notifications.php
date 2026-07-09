<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Notification_model');
    }

    public function get_notifications() {
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([]));
        }

        $offset = (int) $this->input->get('offset', true);
        $notifications = $this->Notification_model->get_notifications($session_data['user_id'], 20, $offset);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($notifications));
    }

    public function get_unread_count() {
        $session_data = $this->session->userdata('user_logged_in');
        $count = $session_data ? $this->Notification_model->count_unread($session_data['user_id']) : 0;

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['count' => $count]));
    }

    public function mark_read() {
        if ($this->input->method() !== 'post') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['status' => 'error']));
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error']));
        }

        $id_notification = $this->input->post('id_notification', true);
        if ($id_notification) {
            $this->Notification_model->mark_read($id_notification, $session_data['user_id']);
        } else {
            $this->Notification_model->mark_all_read($session_data['user_id']);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success']));
    }
}
