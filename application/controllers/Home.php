<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Post_model');
        $this->load->model('Notification_model');
        $this->load->model('Admin_model');
        $this->load->helper('waktu_helper');
        $this->config->load('ads');
    }

    public function index() {
        $data['show_category'] = true;
        $data['title'] = "PaddockID | Indonesia F1 Social Community";

        $session_data = $this->session->userdata('user_logged_in');
        $current_user_id = $session_data ? $session_data['user_id'] : 0;

        $active_tab = $this->input->get('tab') === 'following' ? 'following' : 'for_you';
        $data['active_tab'] = $active_tab;
        $data['categories'] = $this->Post_model->get_categories();

        if ($active_tab === 'following' && $current_user_id) {
            $data['all_posts'] = $this->Post_model->get_following_posts(5, 0, $current_user_id);
        } else {
            $data['all_posts'] = $this->Post_model->get_for_you_posts(5, 0, $current_user_id);
        }

        // Batasi akses untuk guest: hanya 5 post, load more dinonaktifkan
        $data['is_guest'] = !$session_data;
        $data['current_user_id'] = $current_user_id;

        // Load feed ads
        $data['feed_ads'] = [];
        if ($this->config->item('ads_enabled')) {
            $data['feed_ads'] = $this->Admin_model->get_active_ads('feed', $this->config->item('ads_max_feed') ?: 3);
        }

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
        $data['current_user_id'] = $current_user_id;

        // Load feed ads
        $data['feed_ads'] = [];
        if ($this->config->item('ads_enabled')) {
            $data['feed_ads'] = $this->Admin_model->get_active_ads('feed', $this->config->item('ads_max_feed') ?: 3);
        }

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('home', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function get_live_status() {
        $now = time();
        $cache_file = APPPATH . 'cache/ergast_schedule.json';
        $cache_ttl = 3600;

        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_ttl) {
            $schedule = json_decode(file_get_contents($cache_file), true);
        } else {
            $ctx = stream_context_create(['http' => ['timeout' => 5, 'user_agent' => 'PaddockID/1.0']]);
            $json = @file_get_contents('https://api.jolpi.ca/ergast/f1/current/races.json', false, $ctx);
            if ($json) {
                $schedule = json_decode($json, true);
                if ($schedule && isset($schedule['MRData']['RaceTable']['Races'])) {
                    file_put_contents($cache_file, $json);
                }
            } else {
                $schedule = null;
            }
        }

        if (!$schedule || !isset($schedule['MRData']['RaceTable']['Races'])) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'Loading...', 'event_name' => 'Loading...', 'location' => '-', 'session' => '-', 'target_date' => gmdate('Y-m-d\TH:i:s\Z')]));
            return;
        }

        $races = $schedule['MRData']['RaceTable']['Races'];
        $start_idx = null;

        foreach ($races as $i => $race) {
            $race_ts = strtotime($race['time'] !== 'Z' ? $race['date'] . 'T' . $race['time'] : $race['date'] . ' 00:00:00');
            if ($race_ts >= $now - 86400 * 5) {
                $start_idx = $i;
                break;
            }
        }

        if ($start_idx === null) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'Loading...', 'event_name' => 'Loading...', 'location' => '-', 'session' => '-', 'target_date' => gmdate('Y-m-d\TH:i:s\Z')]));
            return;
        }

        for ($i = $start_idx; $i < count($races); $i++) {
            $race = $races[$i];
            $event_name = $race['raceName'];
            $location = $race['Circuit']['Location']['locality'] . ', ' . $race['Circuit']['Location']['country'];

            $sessions = [];
            $session_map = [
                'FirstPractice' => 'FP1',
                'SecondPractice' => 'FP2',
                'ThirdPractice' => 'FP3',
                'SprintQualifying' => 'Sprint Qualifying',
                'Sprint' => 'Sprint',
                'Qualifying' => 'Qualifying',
            ];

            foreach ($session_map as $key => $label) {
                if (isset($race[$key]['date'], $race[$key]['time'])) {
                    $sessions[] = ['name' => $label, 'start' => $race[$key]['date'] . ' ' . str_replace('Z', '', $race[$key]['time'])];
                }
            }

            $race_time = str_replace('Z', '', $race['time']);
            $sessions[] = ['name' => 'Race', 'start' => $race['date'] . ' ' . $race_time];

            foreach ($sessions as $session) {
                $session_time = strtotime($session['start'] . ' UTC');
                if ($session_time === false) continue;

                if ($session_time > $now) {
                    return $this->live_json('', $event_name, $location, $session['name'], $session_time);
                }

                $session_end = $session_time + 14400;
                $db_flag = null;

                $db_name_map = [
                    'FP1' => 'Practice 1', 'FP2' => 'Practice 2', 'FP3' => 'Practice 3',
                    'Sprint Qualifying' => 'Sprint Qualifying', 'Sprint' => 'Sprint',
                    'Qualifying' => 'Qualifying', 'Race' => 'Race',
                ];
                $db_name = $db_name_map[$session['name']] ?? null;

                if ($db_name) {
                    $range_start = date('Y-m-d H:i:s', $session_time - 21600);
                    $range_end = date('Y-m-d H:i:s', $session_time + 21600);
                    $q = $this->db->select('Session_info')->from('race_session')
                        ->where('session_name', $db_name)
                        ->where('start_datetime >=', $range_start)
                        ->where('start_datetime <=', $range_end)
                        ->limit(1)->get();
                    $r = $q->row_array();
                    if ($r && isset($r['Session_info']) && $r['Session_info'] !== '') {
                        $db_flag = strtoupper(trim($r['Session_info']));
                    }
                }

                if ($db_flag && in_array($db_flag, ['YELLOW FLAG', 'RED FLAG', 'SC', 'VSC'], true)) {
                    return $this->live_json($db_flag, $event_name, $location, $session['name'], $session_time);
                }

                if ($db_flag === 'FINISHED') {
                    if ($now - $session_end <= 10800) {
                        return $this->live_json('FINISHED', $event_name, $location, $session['name'], $session_time);
                    }
                    continue;
                }

                if ($now < $session_end) {
                    return $this->live_json('LIVE SESSION', $event_name, $location, $session['name'], $session_time);
                }
            }
        }

        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'Loading...', 'event_name' => 'Loading...', 'location' => '-', 'session' => '-', 'target_date' => gmdate('Y-m-d\TH:i:s\Z')]));
    }

    private function live_json($status, $event_name, $location, $session_name, $timestamp) {
        $this->output->set_content_type('application/json');

        $output = [
            'status' => $status,
            'event_name' => $event_name,
            'location' => $location,
            'session' => strtoupper($session_name),
            'target_date' => gmdate('Y-m-d\TH:i:s\Z', $timestamp),
        ];

        // Hanya tampilkan chat jika sudah 30 menit sebelum sesi mulai
        if (time() >= $timestamp - 1800) {
            $chat_session_map = [
                'FP1' => 'Practice 1',
                'FP2' => 'Practice 2',
                'FP3' => 'Practice 3',
                'Sprint Qualifying' => 'Sprint Qualifying',
                'Sprint' => 'Sprint',
                'Qualifying' => 'Qualifying',
                'Race' => 'Race',
            ];
            $full_session = $chat_session_map[$session_name] ?? $session_name;
            $race_slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($event_name)));
            $sess_slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($full_session)));
            $output['chat_slug'] = trim($race_slug . '-' . $sess_slug, '-');
        }

        $this->output->set_output(json_encode($output));
    }

    public function load_more_posts() {
        $offset = (int) $this->input->get('offset');
        $limit = 5;
        $slug = $this->input->get('category');
        $tab = $this->input->get('tab');

        $session_data = $this->session->userdata('user_logged_in');
        $current_user_id = $session_data ? $session_data['user_id'] : 0;

        if (!empty($slug)) {
            $posts = $this->Post_model->get_posts_by_category_slug($slug, $limit, $offset, $current_user_id);
        } elseif ($tab === 'following' && $current_user_id) {
            $posts = $this->Post_model->get_following_posts($limit, $offset, $current_user_id);
        } else {
            $posts = $this->Post_model->get_for_you_posts($limit, $offset, $current_user_id);
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($posts));
    }

    public function ping() {
        $session_data = $this->session->userdata('user_logged_in');
        if ($session_data) {
            $this->db->where('id_user', $session_data['user_id'])
                ->update('users', ['last_activity' => date('Y-m-d H:i:s')]);
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'ok']));
    }

    public function get_online_status() {
        $user_ids = $this->input->post('user_ids');
        $statuses = [];
        if ($user_ids && is_array($user_ids)) {
            $online_threshold = date('Y-m-d H:i:s', strtotime('-2 minutes'));
            $result = $this->db->select('id_user, last_activity')
                ->from('users')
                ->where_in('id_user', $user_ids)
                ->get()
                ->result_array();
            foreach ($result as $row) {
                $statuses[$row['id_user']] = !empty($row['last_activity']) && $row['last_activity'] >= $online_threshold;
            }
        }
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['statuses' => $statuses]));
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

            if ($result['action'] === 'liked') {
                $post = $this->db->select('user_id')
                    ->from('posts')
                    ->where('id_post', $id_post)
                    ->get()
                    ->row_array();

                if ($post && $post['user_id'] !== $session_data['user_id']) {
                    $this->Notification_model->create([
                        'id_user'  => $post['user_id'],
                        'type'     => 'like',
                        'actor_id' => $session_data['user_id'],
                        'id_post'  => $id_post,
                    ]);
                }
            }

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