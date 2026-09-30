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
        $secret = (string) getenv('ENCRYPTION_KEY');
        foreach ($ads as &$ad) {
            $ad['image_url_full'] = $base . $ad['image_url'];
            // Tanda tangan HMAC utk mencegah inflasi klik & abuse redirector via GET
            $sig = substr(hash_hmac('sha256', 'adclick:' . $ad['id_ad'], $secret), 0, 16);
            $ad['track_url'] = $base . 'ads/track_click/' . $ad['id_ad'] . '?sig=' . $sig;
        }

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($ads));
    }

    public function track_click($id) {
        // Hanya hitung klik bila tanda tangan HMAC valid (dibuat oleh server saat menu ads)
        $sig = $this->input->get('sig', true);
        $expected = substr(hash_hmac('sha256', 'adclick:' . $id, (string) getenv('ENCRYPTION_KEY')), 0, 16);

        if ($sig && hash_equals($expected, $sig)) {
            $this->Admin_model->increment_click_count($id);
        }

        $ad = $this->Admin_model->get_ad($id);

        if ($ad && !empty($ad['target_url'])) {
            redirect($ad['target_url']);
        } else {
            redirect('home');
        }
    }
}
