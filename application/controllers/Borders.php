<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Borders extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Border_model');
        $this->load->helper('assets_url_helper');
    }

    public function index($page = 1) {
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            redirect('auth');
        }

        $user_id = $session_data['user_id'];
        $owned_ids = $this->Border_model->get_user_border_ids($user_id);
        $active_id = $this->Border_model->get_active_border_id($user_id);

        $borders = $this->Border_model->get_all();
        $sorted = [];
        foreach ($borders as $b) {
            $owned = in_array($b['id_border'], $owned_ids);
            $active = $b['id_border'] == $active_id;
            $sorted[] = array_merge($b, ['owned' => $owned, 'active' => $active]);
        }
        usort($sorted, function ($a, $b) {
            if ($a['owned'] !== $b['owned']) return $a['owned'] ? -1 : 1;
            return strcasecmp($a['border_name'], $b['border_name']);
        });

        $per_page = 8;
        $total = count($sorted);
        $page = max(1, (int) $page);
        $offset = ($page - 1) * $per_page;
        $total_pages = max(1, (int) ceil($total / $per_page));

        $data['borders'] = array_slice($sorted, $offset, $per_page);
        $data['owned_ids'] = $owned_ids;
        $data['active_id'] = $active_id;
        $data['page'] = $page;
        $data['total_pages'] = $total_pages;
        $data['total'] = $total;
        $data['title'] = 'Border Shop | PaddockID';

        $active_border = null;
        if ($active_id) {
            foreach ($sorted as $b) {
                if ($b['id_border'] == $active_id) {
                    $active_border = $b;
                    break;
                }
            }
        }
        $data['active_border'] = $active_border;

        $data['user_avatar'] = avatar_url($session_data['profile_pic'] ?? 'default.jpg');
        $data['user_border'] = !empty($session_data['border']) ? $session_data['border'] : null;

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('border', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function equip() {
        if ($this->input->method() !== 'post') {
            return $this->_json_error('Method tidak diizinkan.', 405);
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->_json_error('Silakan login terlebih dahulu.', 401);
        }

        $border_id = $this->input->post('border_id', true);
        if (empty($border_id)) {
            return $this->_json_error('Border ID tidak valid.');
        }

        $owned_ids = $this->Border_model->get_user_border_ids($session_data['user_id']);
        if (!in_array($border_id, $owned_ids)) {
            return $this->_json_error('Kamu tidak memiliki border ini.');
        }

        $this->Border_model->equip($session_data['user_id'], $border_id);

        $border = $this->db->where('id_border', $border_id)->get('borders')->row_array();

        $session_data['border'] = !empty($border['image_url']) ? assets_url($border['image_url']) : null;
        $this->session->set_userdata('user_logged_in', $session_data);

        return $this->_json_success('Border berhasil dipasang!', [
            'border_image' => $session_data['border']
        ]);
    }

    public function remove() {
        if ($this->input->method() !== 'post') {
            return $this->_json_error('Method tidak diizinkan.', 405);
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->_json_error('Silakan login terlebih dahulu.', 401);
        }

        $this->Border_model->remove($session_data['user_id']);

        $session_data['border'] = null;
        $this->session->set_userdata('user_logged_in', $session_data);

        return $this->_json_success('Border berhasil dilepas!');
    }

    public function shop() {
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            redirect('auth');
        }

        $user_id = $session_data['user_id'];
        $owned_ids = $this->Border_model->get_user_border_ids($user_id);

        $all_borders = $this->Border_model->get_all();
        $borders = [];
        foreach ($all_borders as $b) {
            $b['owned'] = in_array($b['id_border'], $owned_ids);
            $b['formatted_price'] = number_format($b['price'], 0, ',', '.');
            $b['category'] = !empty($b['is_premium']) ? 'premium' : (($b['price'] > 0) ? 'team' : 'free');
            $borders[] = $b;
        }

        $data['title'] = 'Shop | PaddockID';
        $data['borders'] = $borders;
        $data['user_coins'] = $this->Border_model->get_user_coins($user_id);

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('shop', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function purchase() {
        if ($this->input->method() !== 'post') {
            return $this->_json_error('Method tidak diizinkan.', 405);
        }

        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->_json_error('Silakan login terlebih dahulu.', 401);
        }

        $border_id = $this->input->post('border_id', true);
        if (empty($border_id)) {
            return $this->_json_error('Border ID tidak valid.');
        }

        $result = $this->Border_model->purchase_with_coins($session_data['user_id'], $border_id);

        if ($result['success']) {
            return $this->_json_success($result['message'], [
                'remaining' => $result['remaining']
            ]);
        }
        return $this->_json_error($result['message']);
    }

    private function _json_error($message, $status = 400) {
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($status)
            ->set_output(json_encode(['status' => 'error', 'message' => $message]));
    }

    private function _json_success($message, $extra = []) {
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array_merge([
                'status' => 'success',
                'message' => $message
            ], $extra)));
    }
}
