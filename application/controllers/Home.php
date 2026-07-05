<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Post_model');
        $this->load->helper('waktu_helper');
    }

    public function index() {
        $data['show_category'] = true;
        $data['title'] = "PaddockID | Indonesia F1 Social Community";

        $session_data = $this->session->userdata('user_logged_in');
        $current_user_id = $session_data ? $session_data['user_id'] : 0;

        $data['all_posts'] = $this->Post_model->get_recent_posts(5, 0, $current_user_id);
        $data['categories'] = $this->Post_model->get_categories();

        // Batasi akses untuk guest: hanya 5 post, load more dinonaktifkan
        $data['is_guest'] = !$session_data;

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('home', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function category($slug = NULL) {
        if (empty($slug)) {
            redirect('home');
        }

        $data['show_category'] = true;
        $data['title'] = "Kategori: " . ucfirst($slug) . " | PaddockID";

        $session_data = $this->session->userdata('user_logged_in');
        $current_user_id = $session_data ? $session_data['user_id'] : 0;

        $data['all_posts'] = $this->Post_model->get_posts_by_category_slug($slug, 5, 0, $current_user_id);
        $data['categories'] = $this->Post_model->get_categories();
        $data['active_category_slug'] = $slug;

        // Batasi akses untuk guest: hanya 5 post, load more dinonaktifkan
        $data['is_guest'] = !$session_data;

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('home', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function get_live_status() {
        $now_str = date('Y-m-d H:i:s');

        $this->db->select('r.gp_name, r.gp_subtitle, s.session_name, s.start_datetime, s.Session_info');
        $this->db->from('race_session s');
        $this->db->join('races r', 's.race_id = r.id_race');
        $this->db->where('s.start_datetime >=', date('Y-m-d H:i:s', strtotime('-3 hours')));
        $this->db->order_by('s.start_datetime', 'ASC');
        $this->db->limit(1);

        $query = $this->db->get();
        $live_data = $query->row_array();

        if (!$live_data) {
            $countdown = [
                'status'       => 'Loading...',
                'event_name'   => 'Loading...',
                'location'     => '-',
                'session'      => '-',
                'target_date'  => date('Y-m-d\TH:i:s\Z'),
            ];
        } else {
            $now = time();
            $start_time = strtotime($live_data['start_datetime']);

            $countdown_status = ($now >= $start_time)
                ? (!empty($live_data['Session_info']) && $live_data['Session_info'] !== 'FINISHED' ? $live_data['Session_info'] : 'LIVE SESSION')
                : '';

            $countdown = [
                'status'       => $countdown_status,
                'event_name'   => $live_data['gp_name'],
                'location'     => $live_data['gp_subtitle'],
                'session'      => strtoupper($live_data['session_name']),
                'target_date'  => date('Y-m-d\TH:i:s\Z', $start_time),
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($countdown));
    }

    public function load_more_posts() {
        $offset = (int) $this->input->get('offset');
        $limit = 5;
        $slug = $this->input->get('category');

        $session_data = $this->session->userdata('user_logged_in');
        $current_user_id = $session_data ? $session_data['user_id'] : 0;

        if (!empty($slug)) {
            $posts = $this->Post_model->get_posts_by_category_slug($slug, $limit, $offset, $current_user_id);
        } else {
            $posts = $this->Post_model->get_recent_posts($limit, $offset, $current_user_id);
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($posts));
    }

    public function toggle_like_post($id_post) {
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

        try {
            $result = $this->Post_model->toggle_like($id_post, $session_data['user_id']);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'action' => $result['action'],
                    'likes_count' => $result['likes_count']
                ]));
        } catch (Exception $e) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        }
    }
}
