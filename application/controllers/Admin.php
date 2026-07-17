<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Admin_model');
        $this->_check_admin();
    }

    private function _check_admin() {
        $session = $this->session->userdata('user_logged_in');
        if (!$session || empty($session['role']) || $session['role'] !== 'admin') {
            redirect('home');
        }
    }

    private function _render($view, $data = []) {
        $data['admin_page'] = $view;
        if ($this->input->get('ajax', true) === '1') {
            $this->output->set_content_type('text/html')->set_output(
                $this->load->view('admin/' . $view, $data, true)
            );
            return;
        }
        $this->load->view('admin/master', $data);
        $this->load->view('admin/' . $view, $data);
        $this->load->view('admin/closure');
    }

    // =====================
    // DASHBOARD
    // =====================

    public function index() {
        $data['title'] = 'Admin Dashboard | PaddockID';
        $data['stats'] = $this->Admin_model->get_stats();
        $data['recent_activity'] = $this->Admin_model->get_recent_activity(8);
        $this->_render('dashboard', $data);
    }

    // =====================
    // POST REPORTS
    // =====================

    public function post_reports() {
        $status = $this->input->get('status', true) ?: 'all';
        $page = max(1, (int) $this->input->get('page', true));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $data['title'] = 'Post Reports | PaddockID Admin';
        $data['reports'] = $this->Admin_model->get_post_reports($status, $offset, $limit);
        $data['total'] = $this->Admin_model->count_post_reports($status);
        $data['total_pages'] = ceil($data['total'] / $limit);
        $data['current_page'] = $page;
        $data['current_status'] = $status;
        $this->_render('post_reports', $data);
    }

    public function resolve_post_report() {
        $id = $this->input->post('id_report');
        $status = $this->input->post('status');
        $admin_id = $this->session->userdata('user_logged_in')['user_id'];

        if (!in_array($status, ['reviewed', 'dismissed'])) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Status tidak valid']));
            return;
        }

        $this->Admin_model->resolve_post_report($id, $status, $admin_id);
        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'message' => 'Report ditandai sebagai ' . $status]));
    }

    public function delete_reported_post() {
        $id_post = $this->input->post('id_post');
        $this->Admin_model->delete_post($id_post);
        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'message' => 'Post dihapus.']));
    }

    // =====================
    // USER REPORTS
    // =====================

    public function user_reports() {
        $status = $this->input->get('status', true) ?: 'all';
        $page = max(1, (int) $this->input->get('page', true));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $data['title'] = 'User Reports | PaddockID Admin';
        $data['reports'] = $this->Admin_model->get_user_reports($status, $offset, $limit);
        $data['total'] = $this->Admin_model->count_user_reports($status);
        $data['total_pages'] = ceil($data['total'] / $limit);
        $data['current_page'] = $page;
        $data['current_status'] = $status;
        $this->_render('user_reports', $data);
    }

    public function resolve_user_report() {
        $id = $this->input->post('id_report');
        $status = $this->input->post('status');
        $admin_id = $this->session->userdata('user_logged_in')['user_id'];

        if (!in_array($status, ['reviewed', 'dismissed'])) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Status tidak valid']));
            return;
        }

        $this->Admin_model->resolve_user_report($id, $status, $admin_id);
        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'message' => 'Report ditandai sebagai ' . $status]));
    }

    public function ban_user() {
        $user_id = $this->input->post('user_id');
        $this->Admin_model->ban_user($user_id);
        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'message' => 'User dibanned.']));
    }

    public function unban_user() {
        $user_id = $this->input->post('user_id');
        $this->Admin_model->unban_user($user_id);
        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'message' => 'User diaktifkan kembali.']));
    }

    // =====================
    // USER LIST
    // =====================

    public function users() {
        $page = max(1, (int) $this->input->get('page', true));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $filter = [];
        if ($this->input->get('status', true)) $filter['status'] = $this->input->get('status', true);
        if ($this->input->get('role', true)) $filter['role'] = $this->input->get('role', true);
        if ($this->input->get('search', true)) $filter['search'] = $this->input->get('search', true);

        $data['title'] = 'User List | PaddockID Admin';
        $data['users'] = $this->Admin_model->get_users($offset, $limit, $filter);
        $data['total'] = $this->Admin_model->count_users($filter);
        $data['total_pages'] = ceil($data['total'] / $limit);
        $data['current_page'] = $page;
        $data['filter'] = $filter;
        $this->_render('users', $data);
    }

    // =====================
    // LOGIN ATTEMPTS
    // =====================

    public function login_attempts() {
        $page = max(1, (int) $this->input->get('page', true));
        $limit = 30;
        $offset = ($page - 1) * $limit;

        $filter = [];
        if ($this->input->get('success', true) !== '') $filter['success'] = (int) $this->input->get('success', true);
        if ($this->input->get('identity', true)) $filter['identity'] = $this->input->get('identity', true);
        if ($this->input->get('ip_address', true)) $filter['ip_address'] = $this->input->get('ip_address', true);
        if ($this->input->get('date_from', true)) $filter['date_from'] = $this->input->get('date_from', true);
        if ($this->input->get('date_to', true)) $filter['date_to'] = $this->input->get('date_to', true);

        $data['title'] = 'Login Attempts | PaddockID Admin';
        $data['attempts'] = $this->Admin_model->get_login_attempts($offset, $limit, $filter);
        $data['total'] = $this->Admin_model->count_login_attempts($filter);
        $data['total_pages'] = ceil($data['total'] / $limit);
        $data['current_page'] = $page;
        $data['filter'] = $filter;
        $this->_render('login_attempts', $data);
    }

    // =====================
    // ERROR LOGS
    // =====================

    public function errors() {
        $file = $this->input->get('file', true);

        $data['title'] = 'Error Logs | PaddockID Admin';
        $data['log_files'] = $this->Admin_model->get_log_files();
        $data['active_file'] = $file;
        $data['log_entries'] = [];
        $data['log_count'] = 0;

        if ($file) {
            $data['log_entries'] = $this->Admin_model->parse_log_file($file);
            $data['log_count'] = count($data['log_entries']);
        }

        $this->_render('errors', $data);
    }

    public function delete_log() {
        $file = $this->input->post('file');
        if ($this->Admin_model->delete_log_file($file)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'message' => 'Log dihapus.']));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menghapus log.']));
        }
    }

    // =====================
    // CUSTOM ADS MANAGEMENT
    // =====================

    public function ads() {
        $status = $this->input->get('status', true) ?: 'all';
        $page = max(1, (int) $this->input->get('page', true));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $filter = [];
        if ($this->input->get('position', true)) $filter['position'] = $this->input->get('position', true);
        if ($this->input->get('active', true) !== '') $filter['is_active'] = $this->input->get('active', true);
        if ($this->input->get('search', true)) $filter['search'] = $this->input->get('search', true);

        $data['title'] = 'Ads Management | PaddockID Admin';
        $data['ads'] = $this->Admin_model->get_ads($offset, $limit, $filter);
        $data['total'] = $this->Admin_model->count_ads($filter);
        $data['total_pages'] = ceil($data['total'] / $limit);
        $data['current_page'] = $page;
        $data['filter'] = $filter;
        $this->_render('ads', $data);
    }

    public function create_ad() {
        if ($this->input->method() === 'post') {
            $this->load->library('upload');

            $config_upload = [
                'upload_path'   => FCPATH . 'uploads/ads/',
                'allowed_types' => 'jpg|jpeg|png|gif|webp',
                'max_size'      => 2048,
                'encrypt_name'  => TRUE,
                'overwrite'     => FALSE,
            ];
            $this->upload->initialize($config_upload);

            $image_url = '';
            if (!empty($_FILES['banner_image']['name'])) {
                if (!$this->upload->do_upload('banner_image')) {
                    $this->output->set_content_type('application/json')
                        ->set_output(json_encode(['status' => 'error', 'message' => $this->upload->display_errors('', '')]));
                    return;
                }
                $upload_data = $this->upload->data();
                $image_url = 'uploads/ads/' . $upload_data['file_name'];
            } else {
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Gambar banner wajib diupload.']));
                return;
            }

            $start_date = $this->input->post('start_date') ?: date('Y-m-d H:i:s');
            $start_date = str_replace('T', ' ', $start_date);
            $end_date = !empty($this->input->post('end_date')) ? str_replace('T', ' ', $this->input->post('end_date')) : NULL;

            $ad_data = [
                'title'       => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'image_url'   => $image_url,
                'target_url'  => $this->input->post('target_url'),
                'position'    => $this->input->post('position'),
                'is_active'   => $this->input->post('is_active') ? 1 : 0,
                'start_date'  => $start_date,
                'end_date'    => $end_date,
            ];

            $id = $this->Admin_model->create_ad($ad_data);
            if (!$id) {
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan iklan ke database.']));
                return;
            }
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Iklan berhasil dibuat.', 'id' => $id]));
            return;
        }

        $data['title'] = 'Create Ad | PaddockID Admin';
        $data['ad'] = NULL;
        $data['form_action'] = 'create_ad';
        $this->_render('ad_form', $data);
    }

    public function edit_ad($id) {
        $ad = $this->Admin_model->get_ad($id);
        if (!$ad) {
            show_404();
        }

        if ($this->input->method() === 'post') {
            $this->load->library('upload');

            $start_date = $this->input->post('start_date') ?: date('Y-m-d H:i:s');
            $start_date = str_replace('T', ' ', $start_date);
            $end_date = !empty($this->input->post('end_date')) ? str_replace('T', ' ', $this->input->post('end_date')) : NULL;

            $ad_data = [
                'title'       => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'target_url'  => $this->input->post('target_url'),
                'position'    => $this->input->post('position'),
                'is_active'   => $this->input->post('is_active') ? 1 : 0,
                'start_date'  => $start_date,
                'end_date'    => $end_date,
            ];

            if (!empty($_FILES['banner_image']['name'])) {
                $config_upload = [
                    'upload_path'   => FCPATH . 'uploads/ads/',
                    'allowed_types' => 'jpg|jpeg|png|gif|webp',
                    'max_size'      => 2048,
                    'encrypt_name'  => TRUE,
                    'overwrite'     => FALSE,
                ];
                $this->upload->initialize($config_upload);

                if ($this->upload->do_upload('banner_image')) {
                    $old_image = FCPATH . $ad['image_url'];
                    if (file_exists($old_image)) {
                        unlink($old_image);
                    }
                    $upload_data = $this->upload->data();
                    $ad_data['image_url'] = 'uploads/ads/' . $upload_data['file_name'];
                } else {
                    $this->output->set_content_type('application/json')
                        ->set_output(json_encode(['status' => 'error', 'message' => $this->upload->display_errors('', '')]));
                    return;
                }
            }

            $this->Admin_model->update_ad($id, $ad_data);
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Iklan berhasil diperbarui.']));
            return;
        }

        $data['title'] = 'Edit Ad | PaddockID Admin';
        $data['ad'] = $ad;
        $data['form_action'] = 'edit_ad/' . $id;
        $this->_render('ad_form', $data);
    }

    public function delete_ad() {
        $id = $this->input->post('id_ad');
        $this->Admin_model->delete_ad($id);
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'message' => 'Iklan dihapus.']));
    }

    public function toggle_ad() {
        $id = $this->input->post('id_ad');
        $this->Admin_model->toggle_ad($id);
        $ad = $this->Admin_model->get_ad($id);
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'is_active' => $ad['is_active']]));
    }

    // =====================
    // AJAX - NOTIFICATION COUNTS
    // =====================

    public function get_counts() {
        $counts = $this->Admin_model->get_pending_counts();
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($counts));
    }
}
