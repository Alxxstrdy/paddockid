<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Race_model extends CI_Model {

    private $api_base = 'https://api.jolpi.ca/ergast/f1/current/';
    private $cache_ttl = 3600;

    public function __construct() {
        parent::__construct();
        $this->load->helper('waktu_helper');
    }

    private function fetch_or_cache($endpoint, $cache_key) {
        $cache_file = APPPATH . 'cache/' . $cache_key . '.json';
        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $this->cache_ttl) {
            return json_decode(file_get_contents($cache_file), true);
        }
        $ctx = stream_context_create(['http' => ['timeout' => 10, 'user_agent' => 'PaddockID/1.0']]);
        $json = @file_get_contents($this->api_base . $endpoint, false, $ctx);
        if ($json) {
            file_put_contents($cache_file, $json);
            return json_decode($json, true);
        }
        if (file_exists($cache_file)) {
            return json_decode(file_get_contents($cache_file), true);
        }
        return null;
    }

    public function get_schedule() {
        $data = $this->fetch_or_cache('races.json', 'ergast_schedule');
        if (!$data || !isset($data['MRData']['RaceTable']['Races'])) return [];
        return $data['MRData']['RaceTable']['Races'];
    }

    private $chat_session_names = [
        'FP1' => 'Practice 1',
        'FP2' => 'Practice 2',
        'FP3' => 'Practice 3',
        'Sprint Qualifying' => 'Sprint Qualifying',
        'Sprint' => 'Sprint',
        'Qualifying' => 'Qualifying',
        'Race' => 'Race',
    ];

    private function make_chat_slug($race_name, $session_name) {
        $full_name = $this->chat_session_names[$session_name] ?? $session_name;
        $race_slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($race_name)));
        $session_slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($full_name)));
        return trim($race_slug . '-' . $session_slug, '-');
    }

    public function format_schedule() {
        $races = $this->get_schedule();
        $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $formatted = [];

        foreach ($races as $race) {
            $round = (int) $race['round'];
            $race_date = $race['date'];
            $race_time = str_replace('Z', '', $race['time'] ?? '00:00:00');
            $race_datetime = new DateTime($race_date . ' ' . $race_time, new DateTimeZone('UTC'));
            $race_datetime->setTimezone(new DateTimeZone('Asia/Jakarta'));

            $race_name = $race['raceName'];
            $sessions = [];
            $session_map = [
                'FirstPractice'    => 'FP1',
                'SecondPractice'   => 'FP2',
                'ThirdPractice'    => 'FP3',
                'SprintQualifying' => 'Sprint Qualifying',
                'Sprint'           => 'Sprint',
                'Qualifying'       => 'Qualifying',
            ];

            foreach ($session_map as $key => $label) {
                if (isset($race[$key]['date'], $race[$key]['time'])) {
                    $sess_dt = new DateTime($race[$key]['date'] . ' ' . str_replace('Z', '', $race[$key]['time']), new DateTimeZone('UTC'));
                    $sess_dt->setTimezone(new DateTimeZone('Asia/Jakarta'));
                    $sessions[] = [
                        'name'      => $label,
                        'date'      => $sess_dt->format('Y-m-d'),
                        'time'      => $sess_dt->format('H:i'),
                        'timestamp' => $sess_dt->getTimestamp(),
                        'status'    => $this->session_status($sess_dt, $now),
                        'chat_slug' => $this->make_chat_slug($race_name, $label),
                    ];
                }
            }

            $sessions[] = [
                'name'      => 'Race',
                'date'      => $race_datetime->format('Y-m-d'),
                'time'      => $race_datetime->format('H:i'),
                'timestamp' => $race_datetime->getTimestamp(),
                'status'    => $this->session_status($race_datetime, $now),
                'chat_slug' => $this->make_chat_slug($race_name, 'Race'),
            ];

            $status = $this->race_status($race_datetime, $now);

            $formatted[] = [
                'round'     => $round,
                'name'      => $race['raceName'],
                'circuit'   => $race['Circuit']['circuitName'],
                'locality'  => $race['Circuit']['Location']['locality'],
                'country'   => $race['Circuit']['Location']['country'],
                'date'      => $race_datetime->format('d M Y'),
                'timestamp' => $race_datetime->getTimestamp(),
                'sessions'  => $sessions,
                'status'    => $status,
                'country_code' => strtolower($race['Circuit']['Location']['country'] ?? ''),
            ];
        }

        return $formatted;
    }

    private function session_status($session_dt, $now) {
        $diff = $now->getTimestamp() - $session_dt->getTimestamp();
        $four_hours = 4 * 3600;
        if ($diff < 0) return 'upcoming';
        if ($diff < $four_hours) return 'live';
        return 'completed';
    }

    private function race_status($race_dt, $now) {
        $diff = $now->getTimestamp() - $race_dt->getTimestamp();
        $day_after = 24 * 3600;
        if ($diff < -$day_after) return 'upcoming';
        if ($diff < $day_after) return 'live';
        return 'completed';
    }

    public function get_driver_standings() {
        $data = $this->fetch_or_cache('driverStandings.json', 'ergast_driver_standings');
        if (!$data || !isset($data['MRData']['StandingsTable']['StandingsLists'][0]['DriverStandings'])) return [];
        return $data['MRData']['StandingsTable']['StandingsLists'][0]['DriverStandings'];
    }

    public function format_driver_standings() {
        $standings = $this->get_driver_standings();
        $formatted = [];

        foreach ($standings as $s) {
            $formatted[] = [
                'position'     => (int) $s['position'],
                'driver'       => $s['Driver']['givenName'] . ' ' . $s['Driver']['familyName'],
                'code'         => $s['Driver']['code'] ?? '',
                'nationality'  => $s['Driver']['nationality'] ?? '',
                'constructor'  => $s['Constructors'][0]['name'] ?? '',
                'constructorId'=> $s['Constructors'][0]['constructorId'] ?? '',
                'points'       => (int) $s['points'],
                'wins'         => (int) $s['wins'],
            ];
        }

        return $formatted;
    }

    public function get_constructor_standings() {
        $data = $this->fetch_or_cache('constructorStandings.json', 'ergast_constructor_standings');
        if (!$data || !isset($data['MRData']['StandingsTable']['StandingsLists'][0]['ConstructorStandings'])) return [];
        return $data['MRData']['StandingsTable']['StandingsLists'][0]['ConstructorStandings'];
    }

    public function format_constructor_standings() {
        $standings = $this->get_constructor_standings();
        $formatted = [];

        foreach ($standings as $s) {
            $formatted[] = [
                'position'      => (int) $s['position'],
                'constructor'   => $s['Constructor']['name'] ?? '',
                'constructorId' => $s['Constructor']['constructorId'] ?? '',
                'nationality'   => $s['Constructor']['nationality'] ?? '',
                'points'        => (int) $s['points'],
                'wins'          => (int) $s['wins'],
            ];
        }

        return $formatted;
    }

    public function get_race_results($round) {
        if (!is_numeric($round) || (int) $round < 1) return null;
        $round = (int) $round;

        $data = $this->fetch_or_cache($round . '/results.json', 'ergast_results_' . $round);
        if (!$data || !isset($data['MRData']['RaceTable']['Races'][0])) return null;
        return $data['MRData']['RaceTable']['Races'][0];
    }

    public function format_race_results($round) {
        $race = $this->get_race_results($round);
        if (!$race) return null;

        $results = [];
        foreach ($race['Results'] ?? [] as $r) {
            $results[] = [
                'position'      => (int) $r['position'],
                'driver'        => $r['Driver']['givenName'] . ' ' . $r['Driver']['familyName'],
                'code'          => $r['Driver']['code'] ?? '',
                'constructor'   => $r['Constructor']['name'] ?? '',
                'constructorId' => $r['Constructor']['constructorId'] ?? '',
                'laps'          => (int) ($r['laps'] ?? 0),
                'time'          => $r['Time']['time'] ?? ($r['status'] ?? ''),
                'points'        => (int) ($r['points'] ?? 0),
                'grid'          => (int) ($r['grid'] ?? 0),
                'positionText'  => $r['positionText'] ?? $r['position'],
            ];
        }

        $fastest = null;
        if (!empty($race['Results'][0]['FastestLap']['Time']['time'])) {
            $fastest = [
                'driver' => $race['Results'][0]['FastestLap']['Driver']['driverId'] ?? '',
                'time'   => $race['Results'][0]['FastestLap']['Time']['time'] ?? '',
            ];
        }

        return [
            'name'     => $race['raceName'] ?? '',
            'circuit'  => $race['Circuit']['circuitName'] ?? '',
            'date'     => $race['date'] ?? '',
            'results'  => $results,
            'fastest'  => $fastest,
        ];
    }

    public function get_next_race() {
        $schedule = $this->format_schedule();
        $now = time();

        foreach ($schedule as $race) {
            if ($race['status'] === 'upcoming' || $race['status'] === 'live') {
                return $race;
            }
        }
        return null;
    }

    public function get_live_flags() {
        $data = $this->fetch_or_cache('races.json', 'ergast_schedule');
        if (!$data || !isset($data['MRData']['RaceTable']['Races'])) return [];

        $races = $data['MRData']['RaceTable']['Races'];
        $now = time();
        $flags = [];

        foreach ($races as $race) {
            $session_map = [
                'FirstPractice' => 'Practice 1', 'SecondPractice' => 'Practice 2',
                'ThirdPractice' => 'Practice 3', 'SprintQualifying' => 'Sprint Qualifying',
                'Sprint' => 'Sprint', 'Qualifying' => 'Qualifying',
            ];
            $all_sessions = [];
            foreach ($session_map as $key => $db_name) {
                if (isset($race[$key]['date'], $race[$key]['time'])) {
                    $t = strtotime($race[$key]['date'] . ' ' . str_replace('Z', '', $race[$key]['time']) . ' UTC');
                    $all_sessions[] = ['name' => $db_name, 'time' => $t];
                }
            }
            $race_time = strtotime($race['date'] . ' ' . str_replace('Z', '', $race['time'] ?? '00:00:00') . ' UTC');
            $all_sessions[] = ['name' => 'Race', 'time' => $race_time];

            foreach ($all_sessions as $sess) {
                if ($sess['time'] <= $now && $sess['time'] + 14400 > $now) {
                    $range_start = date('Y-m-d H:i:s', $sess['time'] - 21600);
                    $range_end = date('Y-m-d H:i:s', $sess['time'] + 21600);
                    $q = $this->db->select('Session_info')
                        ->from('race_session')
                        ->where('session_name', $sess['name'])
                        ->where('start_datetime >=', $range_start)
                        ->where('start_datetime <=', $range_end)
                        ->limit(1)->get();
                    $r = $q->row_array();
                    if ($r && !empty($r['Session_info'])) {
                        $flags[] = [
                            'race' => $race['raceName'],
                            'session' => $sess['name'],
                            'flag' => strtoupper(trim($r['Session_info'])),
                        ];
                    }
                }
            }
        }
        return $flags;
    }

    public function get_constructor_color($constructorId) {
        $colors = [
            'red_bull' => 'rgb(239 68 68)', 'mclaren' => 'rgb(249 115 22)',
            'ferrari' => 'rgb(220 38 38)', 'mercedes' => 'rgb(20 184 166)',
            'aston_martin' => 'rgb(4 120 87)', 'alpine' => 'rgb(59 130 246)',
            'haas' => 'rgb(148 163 184)', 'rb' => 'rgb(37 99 235)',
            'sauber' => 'rgb(132 204 22)', 'williams' => 'rgb(14 165 233)',
        ];
        return $colors[$constructorId] ?? 'rgb(100 116 139)';
    }

    public function get_constructor_image_url($constructorId) {
        $map = [
            'red_bull' => 'redbull',
            'aston_martin' => 'astonmartin',
        ];
        $name = $map[$constructorId] ?? $constructorId;
        return base_url('uploads/teams/' . $name . '.png');
    }
}
