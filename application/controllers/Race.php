<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Race extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Race_model');
        $this->load->model('Post_model');
        $this->load->model('Notification_model');
        $this->load->helper('waktu_helper');
        $this->load->helper('assets_url_helper');
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

        $data['title'] = "Race Hub | PaddockID";
        $data['current_user_id'] = $session_data['user_id'];

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('race_hub', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function get_schedule() {
        $session_data = $this->_require_login();
        if (!$session_data) return;

        $schedule = $this->Race_model->format_schedule();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($schedule));
    }

    public function get_standings() {
        $session_data = $this->_require_login();
        if (!$session_data) return;

        $drivers = $this->Race_model->format_driver_standings();
        $constructors = $this->Race_model->format_constructor_standings();

        foreach ($drivers as &$d) {
            $d['constructorColor'] = $this->Race_model->get_constructor_color($d['constructorId']);
            $d['constructorImage'] = $this->Race_model->get_constructor_image_url($d['constructorId']);
        }
        foreach ($constructors as &$c) {
            $c['constructorColor'] = $this->Race_model->get_constructor_color($c['constructorId']);
            $c['constructorImage'] = $this->Race_model->get_constructor_image_url($c['constructorId']);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'drivers' => $drivers,
                'constructors' => $constructors,
            ]));
    }

    public function get_results($round = null) {
        if (!is_numeric($round) || (int) $round < 1 || (int) $round > 30) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'Round parameter required']));
            return;
        }

        $round = (int) $round;

        $session_data = $this->_require_login();
        if (!$session_data) return;

        $results = $this->Race_model->format_race_results($round);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($results ?: ['error' => 'No results available']));
    }
}
