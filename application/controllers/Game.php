<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Guess the Driver — game tebak pembalap F1 ala "Mastermind".
 *
 * Flow: halaman menampilkan LOBBY (aturan + tombol Play). Klik Play me-
 * mulai game baru via AJAX, lalu board game dirender di tempat yang sama.
 * Tebakan dikirim via fetch (AJAX) sehingga feedback muncul tanpa reload.
 *
 * Jawaban adalah satu pembalap acak; pemain menebak nama pembalap; sistem
 * memberi feedback per-petunjuk (↑ lebih tinggi /↓ lebih rendah /✓ sama /✗ beda)
 * untuk 5 petunjuk acak dari 8 atribut. Maksimal 5 percobaan.
 *
 * State game disimpan di session CI (bukan $_SESSION) supaya aman terhadap
 * rotasi cookie sesi; fitur bisa dimainkan tamu maupun yang sudah login.
 */
class Game extends CI_Controller {

    private $_pools = [
        'number'=>['label' => 'Number'],
        'wins' => ['label' => 'Win Total'],
        'champ'=> ['label' => 'Gelar Dunia'],
        'nation'=>['label' => 'Nationality', 'text' => true],
        'team' => ['label' => 'Tim Terakhir', 'text' => true],
        'birth'=> ['label' => 'Tahun Lahir'],
        'debut'=> ['label' => 'Debut F1'],
        'points'=>['label' => 'Total Poin'],
    ];

    private $_clue_meta = [
        'number' => 'num',
        'wins'   => 'num',
        'champ'  => 'num',
        'birth'  => 'num',
        'debut'  => 'num',
        'points' => 'num',
        'nation' => 'text',
        'team'   => 'text',
    ];

    private $_drivers = [
        ['answer' => 'Andrea Kimi Antonelli','number'=>12,'wins'=>8,'champ'=>0,'nation'=>'Italian','team'=>'Mercedes','birth'=>2006,'debut'=>2024,'points'=>429],
        ['answer' => 'George Russell','number'=>63,'wins'=>8,'champ'=>0,'nation'=>'British','team'=>'Mercedes','birth'=>1998,'debut'=>2019,'points'=>1155],
        ['answer' => 'Lewis Hamilton','number'=>44,'wins'=>106,'champ'=>7,'nation'=>'British','team'=>'Ferrari','birth'=>1985,'debut'=>2007,'points'=>5149],
        ['answer' => 'Charles Leclerc','number'=>16,'wins'=>9,'champ'=>0,'nation'=>'Monegasque','team'=>'Ferrari','birth'=>1997,'debut'=>2018,'points'=>1751],
        ['answer' => 'Lando Norris','number'=>1,'wins'=>13,'champ'=>1,'nation'=>'British','team'=>'McLaren','birth'=>1999,'debut'=>2019,'points'=>1500],
        ['answer' => 'Max Verstappen','number'=>3,'wins'=>72,'champ'=>4,'nation'=>'Dutch','team'=>'Red Bull','birth'=>1997,'debut'=>2015,'points'=>3477],
        ['answer' => 'Oscar Piastri','number'=>81,'wins'=>9,'champ'=>0,'nation'=>'Australian','team'=>'McLaren','birth'=>2001,'debut'=>2023,'points'=>835],
        ['answer' => 'Isack Hadjar','number'=>6,'wins'=>0,'champ'=>0,'nation'=>'French','team'=>'Red Bull','birth'=>2004,'debut'=>2025,'points'=>146],
        ['answer' => 'Liam Lawson','number'=>30,'wins'=>0,'champ'=>0,'nation'=>'New Zealander','team'=>'RB F1 Team','birth'=>2002,'debut'=>2023,'points'=>106],
        ['answer' => 'Pierre Gasly','number'=>10,'wins'=>1,'champ'=>0,'nation'=>'French','team'=>'Alpine F1 Team','birth'=>1996,'debut'=>2017,'points'=>485],
        ['answer' => 'Arvid Lindblad','number'=>41,'wins'=>0,'champ'=>0,'nation'=>'British','team'=>'RB F1 Team','birth'=>2007,'debut'=>2025,'points'=>37],
        ['answer' => 'Franco Colapinto','number'=>43,'wins'=>0,'champ'=>0,'nation'=>'Argentine','team'=>'Alpine F1 Team','birth'=>2003,'debut'=>2024,'points'=>32],
        ['answer' => 'Oliver Bearman','number'=>87,'wins'=>0,'champ'=>0,'nation'=>'British','team'=>'Haas F1 Team','birth'=>2005,'debut'=>2024,'points'=>65],
        ['answer' => 'Gabriel Bortoleto','number'=>5,'wins'=>0,'champ'=>0,'nation'=>'Brazilian','team'=>'Audi','birth'=>2004,'debut'=>2025,'points'=>29],
        ['answer' => 'Nico Hülkenberg','number'=>27,'wins'=>0,'champ'=>0,'nation'=>'German','team'=>'Audi','birth'=>1987,'debut'=>2010,'points'=>621],
        ['answer' => 'Esteban Ocon','number'=>31,'wins'=>1,'champ'=>0,'nation'=>'French','team'=>'Haas F1 Team','birth'=>1996,'debut'=>2016,'points'=>481],
        ['answer' => 'Fernando Alonso','number'=>14,'wins'=>32,'champ'=>2,'nation'=>'Spanish','team'=>'Aston Martin','birth'=>1981,'debut'=>2001,'points'=>2387],
        ['answer' => 'Carlos Sainz','number'=>55,'wins'=>4,'champ'=>0,'nation'=>'Spanish','team'=>'Williams','birth'=>1994,'debut'=>2015,'points'=>1264],
        ['answer' => 'Alexander Albon','number'=>23,'wins'=>0,'champ'=>0,'nation'=>'Thai','team'=>'Williams','birth'=>1996,'debut'=>2019,'points'=>313],
        ['answer' => 'Yuki Tsunoda','number'=>22,'wins'=>0,'champ'=>0,'nation'=>'Japanese','team'=>'RB F1 Team','birth'=>2000,'debut'=>2021,'points'=>109],
        ['answer' => 'Lance Stroll','number'=>18,'wins'=>0,'champ'=>0,'nation'=>'Canadian','team'=>'Aston Martin','birth'=>1998,'debut'=>2017,'points'=>315],
        ['answer' => 'Valtteri Bottas','number'=>77,'wins'=>10,'champ'=>0,'nation'=>'Finnish','team'=>'Cadillac F1 Team','birth'=>1989,'debut'=>2013,'points'=>1788],
        ['answer' => 'Sergio Pérez','number'=>11,'wins'=>6,'champ'=>0,'nation'=>'Mexican','team'=>'Cadillac F1 Team','birth'=>1990,'debut'=>2011,'points'=>1585],
    ];

    private $_state_key = 'guess_driver_game';

    private $_max_tries = 5;

    private $_daily_limit = 5;

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Game_model');

        // konsistenkan tipe numerik & string untuk pembanding
        foreach ($this->_drivers as &$d) {
            $d['number']  = (int) $d['number'];
            $d['wins']    = (int) $d['wins'];
            $d['champ']   = (int) $d['champ'];
            $d['birth']   = (int) $d['birth'];
            $d['debut']   = (int) $d['debut'];
            $d['points']  = (int) $d['points'];
        }
        unset($d);
    }

    // ---------- helpers ----------

    /**
     * Wajib login untuk halaman penuh; redirect ke halaman auth bila belum.
     * Kembalikan data sesi user bila sudah login, null bila belum.
     */
    private function _require_login() {
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            redirect('auth');
            return null;
        }
        return $session_data;
    }

    private function _player_identity() {
        $session_data = $this->session->userdata('user_logged_in');
        $user_id = !empty($session_data) ? $session_data['user_id'] : null;
        return $this->Game_model->build_identity($user_id);
    }

    private function _daily_used() {
        return $this->Game_model->count_today($this->_player_identity());
    }

    private function _daily_remaining() {
        return max(0, $this->_daily_limit - $this->_daily_used());
    }

    private function _daily_permitted() {
        return $this->_daily_used() < $this->_daily_limit;
    }

    private function _new_game_state() {
        $answer = $this->_drivers[array_rand($this->_drivers)];
        $keys = array_rand($this->_pools, 5);      // 5 petunjuk acak
        if (!is_array($keys)) $keys = [$keys];

        return [
            'answer' => $answer,
            'clues'  => array_values($keys),
            'tries'  => [],
            'over'   => false,
            'won'    => false,
            'started_at' => date('Y-m-d H:i:s'),
        ];
    }

    private function _save($state) {
        $this->session->set_userdata($this->_state_key, $state);
    }

    private function _load() {
        $state = $this->session->userdata($this->_state_key);
        return (is_array($state) && !empty($state['answer'])) ? $state : null;
    }

    private function _clear() {
        $this->session->unset_userdata($this->_state_key);
    }

    private function _names() {
        $names = array_column($this->_drivers, 'answer');
        sort($names);
        return $names;
    }

    private function _jsend($payload, $status = 200) {
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($status)
            ->set_output(json_encode($payload));
    }

    private function _board_vars($state) {
        return [
            'game'      => $state,
            'clues_def' => $this->_pools,
            'names'     => $this->_names(),
            'max_tries' => $this->_max_tries,
            'used_tries'=> count(!empty($state['tries']) ? $state['tries'] : []),
        ];
    }

    private function _render_board($state) {
        return $this->load->view('game/_board', $this->_board_vars($state), TRUE);
    }

    private function _render_lobby($flash = '') {
        return $this->load->view('game/_lobby', [
            'names_count'   => count($this->_drivers),
            'max_tries'     => $this->_max_tries,
            'daily_limit'   => $this->_daily_limit,
            'daily_used'    => $this->_daily_used(),
            'daily_remaining' => $this->_daily_remaining(),
            'flash'         => $flash,
        ], TRUE);
    }

    // ---------- halaman ----------

    /**
     * Halaman utama: lobby bila belum ada game berjalan, board bila ada.
     */
    public function index() {
        $session_data = $this->_require_login();
        if (!$session_data) return;
        $state = $this->_load();
        $mode = $state ? 'play' : 'lobby';

        $data = [
            'title' => 'Guess the Driver | PaddockID',
            'meta_description' => 'Game tebak pembalap F1 dari petunjuk: nomor, kemenangan, gelar, tim, dan lainnya.',
            'current_user_id' => $session_data ? $session_data['user_id'] : 0,
            'is_logged_in'    => (bool) $session_data,
            'mode'      => $mode,
            'game'      => $state,
            'stage_html'=> $state ? $this->_render_board($state) : $this->_render_lobby(),
        ];

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar-left', $data);
        $this->load->view('game', $data);
        $this->load->view('layout/sidebar-right', $data);
        $this->load->view('layout/footer');
    }

    // ---------- AJAX ----------

    /**
     * Mulai game baru (POST + CSRF via fetch, wajib login).
     * Dibatasi 5x per hari per akun.
     */
    public function start() {
        if ($this->input->method() !== 'post') {
            return $this->_jsend(['ok' => false, 'error' => 'Metode tidak diizinkan.']);
        }
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->_jsend(['ok' => false, 'error' => 'Sesi berakhir. Silakan masuk dulu.'], 401);
        }

        if (!$this->_daily_permitted()) {
            return $this->_jsend([
                'ok'    => false,
                'error' => 'Kamu sudah memakai ' . $this->_daily_limit . 'x batas main hari ini. Coba lagi besok!',
            ]);
        }

        $this->Game_model->increment($this->_player_identity());
        $this->_save($this->_new_game_state());
        return $this->_jsend(['ok' => true, 'stage' => $this->_render_board($this->_load())]);
    }

    /**
     * Akhiri/keluar ke lobby (POST + CSRF via fetch, wajib login).
     */
    public function quit() {
        if ($this->input->method() !== 'post') {
            return $this->_jsend(['ok' => false, 'error' => 'Metode tidak diizinkan.']);
        }
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->_jsend(['ok' => false, 'error' => 'Sesi berakhir. Silakan masuk dulu.'], 401);
        }
        $this->_clear();
        return $this->_jsend(['ok' => true, 'stage' => $this->_render_lobby()]);
    }

    /**
     * Proses tebakan (POST + CSRF via fetch, wajib login).
     * Mengembalikan board HTML baru.
     */
    public function guess() {
        if ($this->input->method() !== 'post') {
            return $this->_jsend(['ok' => false, 'error' => 'Metode tidak diizinkan.']);
        }
        $session_data = $this->session->userdata('user_logged_in');
        if (!$session_data) {
            return $this->_jsend(['ok' => false, 'error' => 'Sesi berakhir. Silakan masuk dulu.'], 401);
        }
        $state = $this->_load();
        if (!$state) {
            return $this->_jsend(['ok' => false, 'error' => 'Belum ada game. Klik Play untuk mulai.']);
        }
        if ($state['over']) {
            return $this->_jsend(['ok' => false, 'error' => 'Game sudah selesai. Mulai game baru untuk bermain lagi.']);
        }

        $name = trim((string) $this->input->post('driver'));
        $found = null;
        foreach ($this->_drivers as $d) {
            if (strcasecmp($d['answer'], $name) === 0) { $found = $d; break; }
        }
        if (!$found || $name === '') {
            return $this->_jsend(['ok' => false, 'error' => 'Driver tidak ditemukan di database. Ketik nama sesuai daftar.']);
        }

        $fb = [];
        $name_value = [];
        $allMatch = true;
        foreach ($state['clues'] as $k) {
            $av = $state['answer'][$k] ?? null;
            $gv = $found[$k] ?? null;
            $name_value[$k] = $gv;
            $type = !empty($this->_clue_meta[$k]) ? $this->_clue_meta[$k] : 'num';
            if ($type === 'text') {
                $fb[$k] = (strcasecmp((string) $gv, (string) $av) === 0) ? 'same' : 'diff';
                if ($fb[$k] !== 'same') $allMatch = false;
            } else {
                if ((int) $gv === (int) $av) { $fb[$k] = 'same'; }
                elseif ((int) $gv > (int) $av) { $fb[$k] = 'lower'; $allMatch = false; }
                else { $fb[$k] = 'higher'; $allMatch = false; }
            }
        }

        $state['tries'][] = ['name' => $found['answer'], 'fb' => $fb, 'name_value' => $name_value];
        if ($allMatch) {
            $state['over'] = true;
            $state['won'] = true;
            // Calculate reward based on which try the user won
            $tries_used = count($state['tries']);
            $reward_map = [
                1 => 100, // Clue 1 (try 1): 100 koin
                2 => 75,  // Clue 2 (try 2): 75 koin
                3 => 50,  // Clue 3 (try 3): 50 koin
                4 => 30,  // Clue 4 (try 4): 30 koin
                5 => 15,  // Clue 5 (try 5): 15 koin
            ];
            $reward = $reward_map[$tries_used] ?? 0;
            $state['reward'] = $reward;
            // Award coins to user
            if ($reward > 0) {
                $this->Game_model->award_coins($session_data['user_id'], $reward);
                // Update session coins
                $session_data['coins'] = ($session_data['coins'] ?? 0) + $reward;
                $this->session->set_userdata('user_logged_in', $session_data);
            }
        }
        elseif (count($state['tries']) >= $this->_max_tries) {
            $state['over'] = true;
            $state['reward'] = 0;
        }
        $this->_save($state);

        return $this->_jsend([
            'ok'    => true,
            'stage' => $this->_render_board($state),
            'over'  => $state['over'],
            'won'   => $state['won'],
            'reward' => $state['reward'] ?? 0,
        ]);
    }
}