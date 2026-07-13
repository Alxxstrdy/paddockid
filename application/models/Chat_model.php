<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat_model extends CI_Model {

    private $session_durations = [
        'Practice 1'        => 60,
        'Practice 2'        => 60,
        'Practice 3'        => 60,
        'Sprint Qualifying' => 45,
        'Sprint'            => 100,
        'Qualifying'        => 60,
        'Race'              => 180,
    ];

    private $api_base = 'https://api.jolpi.ca/ergast/f1/current/';

    public function get_rooms() {
        $rooms = $this->db
            ->select('*')
            ->from('chat_rooms')
            ->order_by('opens_at', 'asc')
            ->get()
            ->result_array();

        $now = date('Y-m-d H:i:s');
        foreach ($rooms as &$room) {
            $room['room_status'] = $this->_calc_status($room, $now);
        }
        return $rooms;
    }

    public function get_active_rooms() {
        $now = date('Y-m-d H:i:s');
        $rooms = $this->db
            ->select('*')
            ->from('chat_rooms')
            ->where('opens_at <=', $now)
            ->where('closes_at >', $now)
            ->order_by('opens_at', 'asc')
            ->get()
            ->result_array();
        foreach ($rooms as &$room) {
            $room['room_status'] = 'active';
        }
        return $rooms;
    }

    public function get_upcoming_rooms() {
        $now = date('Y-m-d H:i:s');
        $rooms = $this->db
            ->select('*')
            ->from('chat_rooms')
            ->where('opens_at >', $now)
            ->order_by('opens_at', 'asc')
            ->get()
            ->result_array();
        foreach ($rooms as &$room) {
            $room['room_status'] = 'upcoming';
        }
        return $rooms;
    }

    public function get_room_by_slug($slug) {
        $room = $this->db
            ->select('*')
            ->from('chat_rooms')
            ->where('slug', $slug)
            ->limit(1)
            ->get()
            ->row_array();
        if ($room) {
            $room['room_status'] = $this->_calc_status($room, date('Y-m-d H:i:s'));
        }
        return $room;
    }

    public function get_room_by_id($id_room) {
        $room = $this->db
            ->select('*')
            ->from('chat_rooms')
            ->where('id_room', $id_room)
            ->limit(1)
            ->get()
            ->row_array();
        if ($room) {
            $room['room_status'] = $this->_calc_status($room, date('Y-m-d H:i:s'));
        }
        return $room;
    }

    public function save_message($data) {
        $this->db->insert('chat_messages', $data);
        return $this->db->insert_id();
    }

    public function sync_rooms_from_schedule() {
        $schedule = $this->_fetch_schedule();
        if (!$schedule || !isset($schedule['MRData']['RaceTable']['Races'])) {
            return ['inserted' => 0, 'errors' => 'No schedule data'];
        }

        $inserted = 0;
        $races = $schedule['MRData']['RaceTable']['Races'];

        $session_map = [
            'FirstPractice'    => 'Practice 1',
            'SecondPractice'   => 'Practice 2',
            'ThirdPractice'    => 'Practice 3',
            'SprintQualifying' => 'Sprint Qualifying',
            'Sprint'           => 'Sprint',
            'Qualifying'       => 'Qualifying',
        ];

        foreach ($races as $race) {
            $round = (int) $race['round'];
            $race_name = $race['raceName'];

            foreach ($session_map as $key => $label) {
                if (isset($race[$key]['date'], $race[$key]['time'])) {
                    $this->_create_if_missing($round, $race_name, $label, $race[$key]['date'], $race[$key]['time'], $inserted);
                }
            }

            $this->_create_if_missing($round, $race_name, 'Race', $race['date'], $race['time'] ?? '00:00:00', $inserted);
        }

        return ['inserted' => $inserted];
    }

    private function _create_if_missing($round, $race_name, $label, $date, $time, &$inserted) {
        $slug = $this->_make_slug($race_name, $label);
        $exists = $this->db->where('slug', $slug)->count_all_results('chat_rooms');
        if ($exists) return;

        $sess_dt = new DateTime($date . ' ' . str_replace('Z', '', $time), new DateTimeZone('UTC'));
        $sess_dt->setTimezone(new DateTimeZone('Asia/Jakarta'));

        $duration = $this->session_durations[$label] ?? 60;
        $opens_dt = clone $sess_dt;
        $opens_dt->modify('-30 minutes');
        $closes_dt = clone $sess_dt;
        $closes_dt->modify('+' . ($duration + 30) . ' minutes');

        $this->db->insert('chat_rooms', [
            'race_round'   => $round,
            'race_name'    => $race_name,
            'session_name' => $label,
            'slug'         => $slug,
            'opens_at'     => $opens_dt->format('Y-m-d H:i:s'),
            'closes_at'    => $closes_dt->format('Y-m-d H:i:s'),
        ]);
        $inserted++;
    }

    private function _make_slug($race_name, $session_name) {
        $race_slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($race_name)));
        $session_slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($session_name)));
        return trim($race_slug . '-' . $session_slug, '-');
    }

    private function _calc_status($room, $now) {
        if ($room['opens_at'] > $now) return 'upcoming';
        if ($room['closes_at'] > $now) return 'active';
        return 'completed';
    }

    private function _fetch_schedule() {
        $cache_file = APPPATH . 'cache/ergast_schedule.json';
        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < 3600) {
            return json_decode(file_get_contents($cache_file), true);
        }
        $ctx = stream_context_create(['http' => ['timeout' => 10, 'user_agent' => 'PaddockID/1.0']]);
        $json = @file_get_contents($this->api_base . 'races.json', false, $ctx);
        if ($json) {
            file_put_contents($cache_file, $json);
            return json_decode($json, true);
        }
        if (file_exists($cache_file)) {
            return json_decode(file_get_contents($cache_file), true);
        }
        return null;
    }
}
