<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ads extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Admin_model');
    }

    public function get_active() {
        $this->config->load('ads');

        if (!$this->config->item('ads_enabled')) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode([]));
            return;
        }

        $position = $this->input->get('position') ?: 'sidebar';
        $limit = (int) ($this->input->get('limit') ?: $this->config->item('ads_max_' . $position) ?: 1);

        $ads = $this->Admin_model->get_active_ads($position, $limit);

        $base = base_url();
        foreach ($ads as &$ad) {
            $ad['image_url_full'] = $base . $ad['image_url'];
            $ad['track_url'] = $base . 'ads/track_click/' . $ad['id_ad'];
        }

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($ads));
    }

    public function track_click($id) {
        $this->Admin_model->increment_click_count($id);
        $ad = $this->Admin_model->get_ad($id);

        if ($ad && !empty($ad['target_url'])) {
            redirect($ad['target_url']);
        } else {
            redirect('home');
        }
    }
}
