<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Post_model');
        $this->load->helper('waktu_helper');
    }

    public function index() {
        $keyword = trim($this->input->get('q', true));
        if (empty($keyword)) {
            $data['title'] = 'Cari | PaddockID';
            $data['keyword'] = '';
            $data['posts'] = [];
            $data['users'] = [];
            $data['posts_count'] = 0;
            $data['users_count'] = 0;
            $this->_render($data);
            return;
        }

        $session_data = $this->session->userdata('user_logged_in');
        $current_user_id = $session_data ? $session_data['user_id'] : 0;

        $limit = 5;

        $data['title'] = 'Cari: ' . htmlspecialchars($keyword) . ' | PaddockID';
        $data['keyword'] = $keyword;
        $data['posts'] = $this->Post_model->search_posts($keyword, $limit, 0, $current_user_id);
        $data['users'] = $this->Post_model->search_users($keyword, $limit, 0, $current_user_id);
        $data['posts_count'] = $this->Post_model->count_search_posts($keyword);
        $data['users_count'] = $this->Post_model->count_search_users($keyword);
        $data['current_user_id'] = $current_user_id;

        $this->_render($data);
    }

    public function search_ajax() {
        $keyword = trim($this->input->get('q', true));
        $type = $this->input->get('type', true);
        $offset = (int) $this->input->get('offset', true);
        $limit = 5;

        if (empty($keyword) || !in_array($type, ['posts', 'users'], true)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([]));
        }

        $session_data = $this->session->userdata('user_logged_in');
        $current_user_id = $session_data ? $session_data['user_id'] : 0;

        if ($type === 'posts') {
            $results = $this->Post_model->search_posts($keyword, $limit, $offset, $current_user_id);
        } else {
            $results = $this->Post_model->search_users($keyword, $limit, $offset, $current_user_id);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($results));
    }

    private function _render($data) {
        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('search', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }
}
