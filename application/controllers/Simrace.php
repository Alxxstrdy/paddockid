<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Simrace — FITUR EKSPERIMEN (terisolasi).
 *
 * Akses: /simrace
 * Endpoint: /simrace/api?season=&round=&driver=&pit_lap=&duration=
 *           /simrace/drivers?season=&round=
 *
 * Untuk menghapus fitur ini cukup hapus:
 *   - Simrace.php (controller ini)
 *   - Simrace_model.php (model)
 *   - application/views/simrace.php
 *   - uploads/css/simrace.css
 * Tidak ada file lain yang dirubah/di-infeksi.
 */

class Simrace extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Simrace_model');
    }

    private function _json_error($message, $status = 400) {
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($status)
            ->set_output(json_encode(['error' => $message]));
    }

    private function _clean_season($raw) {
        $season = trim((string) $raw);
        if ($season === '') return date('Y');
        if ($season === 'current') return $season;
        if (!preg_match('/^\d{4}$/', $season)) return null;
        $year = (int) $season;
        if ($year < 1950 || $year > (int) date('Y') + 1) return null;
        return (string) $year;
    }

    private function _clean_round($raw) {
        $round = trim((string) $raw);
        if ($round === '' || !preg_match('/^\d{1,2}$/', $round)) return null;
        $round = (int) $round;
        return ($round >= 1 && $round <= 30) ? $round : null;
    }

    private function _clean_driver($raw) {
        $driver = trim((string) $raw);
        if ($driver === '') return '';
        return preg_match('/^[a-z0-9_]{1,40}$/i', $driver) ? $driver : null;
    }

    private function _clean_duration($raw) {
        $duration = trim((string) $raw);
        if ($duration === '') return null;
        return preg_match('/^\d{1,3}(?:[.,]\d{1,3})?$/', $duration) ? $duration : null;
    }

    public function index() {
        $data['title'] = 'Simulasi Race | PaddockID';
        $data['page_css'][] = 'simrace';
        $data['schedule'] = $this->Simrace_model->get_schedule_list();

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('simrace', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    public function api() {
        if (!throttle('simrace_api', 30, 1)) {
            return $this->_json_error('Terlalu banyak permintaan. Coba lagi sebentar lagi.', 429);
        }

        $season  = $this->_clean_season($this->input->get('season'));
        $round   = $this->_clean_round($this->input->get('round'));
        $driver  = $this->_clean_driver($this->input->get('driver'));
        $pit_lap = (int) $this->input->get('pit_lap');
        $duration = $this->_clean_duration($this->input->get('duration'));

        if ($season === null || $round === null || $driver === null) {
            return $this->_json_error('Parameter tidak valid. season harus tahun 4 digit, round 1-30, driver hanya huruf/angka.');
        }

        if ($pit_lap < 1 || $pit_lap > 100) {
            return $this->_json_error('Parameter wajib: season, round, pit_lap (1-100). (driver opsional, kalau kosong pakai pemenang race)');
        }

        // Kalau driver tidak dipilih, pakai pemenang race sebagai contoh
        if ($driver === '') {
            $drivers = $this->Simrace_model->get_drivers($season, $round);
            $driver = $drivers[0]['driverId'] ?? null;
            if (!$driver) {
                return $this->_json_error('Tidak ada data driver untuk race ini.', 404);
            }
        }

        $result = $this->Simrace_model->simulate($season, $round, $driver, $pit_lap, $duration);
        if ($result === null) {
            return $this->_json_error('Data race tidak ditemukan / API mirror sedang tidak dijangkau.', 404);
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    public function drivers() {
        if (!throttle('simrace_drivers', 60, 1)) {
            return $this->_json_error('Terlalu banyak permintaan. Coba lagi sebentar lagi.', 429);
        }

        $season = $this->_clean_season($this->input->get('season'));
        $round  = $this->_clean_round($this->input->get('round'));

        if ($season === null || $round === null) {
            return $this->_json_error('Parameter season & round wajib dan harus valid.');
        }

        $drivers = $this->Simrace_model->get_drivers($season, $round);
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($drivers));
    }
}