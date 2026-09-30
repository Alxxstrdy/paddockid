<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Simrace_model
 * — Fitur eksperimen: simulasi "what-if" dari race yang sudah selesai.
 * Data diambil dari mirror Ergast (api.jolpi.ca), lalu kita hitung sendiri
 * waktu kumulatif & posisi berbasis lap time per driver.
 *
 * File ini 100% terpisah dari model lain (Race_model dll) supaya bisa
 * dihapus kapan saja tanpa mempengaruhi fitur lain.
 * Yang perlu dihapus: Simrace_model.php, Simrace.php (controller),
 * views/simrace.php, uploads/css/simrace.css.
 *
 * CATATAN SIMULASI:
 * - Posisi simulasi dihitung dari waktu kumulatif per lap.
 * - Gap = selisih waktu kumulatif terhadap leader di lap tersebut.
 * - Ini ESTIMASI (mobil yang tertinggal lap/blue flag dll tidak persis sama
 *   dengan timing resmi live), cocok untuk eksperimen/hiburan.
 */

class Simrace_model extends CI_Model {

    private $api_base = 'https://api.jolpi.ca/ergast/f1/';
    private $cache_ttl = 86400; // 24 jam

    public function __construct() {
        parent::__construct();
    }

    /**
     * Whitelist internal: cache filename dan URL dibangun dari $season/$path,
     * jadi keduanya harus dibatasi di sini juga — bukan hanya di controller.
     * Tanpa ini, pemanggil lain bisa menyisipkan "../" ke dalam nama file cache.
     */
    private function safe_season($value)
    {
        $value = (string) $value;
        if ($value === 'current') return $value;
        if (!preg_match('/^\d{4}$/', $value)) return null;
        $year = (int) $value;
        if ($year < 1950 || $year > (int) date('Y') + 1) return null;
        return (string) $year;
    }

    private function safe_path($value)
    {
        $value = (string) $value;
        if (strpos($value, '..') !== false) return null;
        return preg_match('#^[A-Za-z0-9_-]+(?:/[A-Za-z0-9_.-]+)*\.json$#', $value) ? $value : null;
    }

    private function fetch($season, $path) {
        $season = $this->safe_season($season);
        $path   = $this->safe_path($path);
        if ($season === null || $path === null) {
            return null;
        }

        $cache_file = APPPATH . 'cache/simrace_' . $season . '_' . str_replace('/', '_', $path) . '.json';
        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $this->cache_ttl) {
            return json_decode(file_get_contents($cache_file), true);
        }
        $url = $this->api_base . $season . '/' . $path;
        $ctx = stream_context_create(['http' => ['timeout' => 15, 'user_agent' => 'PaddockID/1.0']]);
        $json = @file_get_contents($url, false, $ctx);
        if ($json) {
            @file_put_contents($cache_file, $json);
            return json_decode($json, true);
        }
        if (file_exists($cache_file)) {
            return json_decode(file_get_contents($cache_file), true);
        }
        return null;
    }

    /* ---------- DATA SUMBER ---------- */

    public function get_schedule_list() {
        $data = $this->fetch('current', 'races.json');
        if (!$data) return [];
        $out = [];
        foreach (($data['MRData']['RaceTable']['Races'] ?? []) as $r) {
            $out[] = [
                'season' => $data['MRData']['RaceTable']['season'] ?? 'current',
                'round'  => (int) $r['round'],
                'name'   => $r['raceName'] . ' — ' . $r['Circuit']['circuitName'],
            ];
        }
        return $out;
    }

    public function get_race_title($season, $round) {
        $data = $this->fetch($season, 'races.json');
        foreach (($data['MRData']['RaceTable']['Races'] ?? []) as $r) {
            if ((int) $r['round'] === (int) $round) {
                return $r['raceName'] . ' — ' . $r['Circuit']['circuitName'];
            }
        }
        return $season . ' Round ' . $round;
    }

    public function get_drivers($season, $round) {
        $data = $this->fetch($season, $round . '/results.json');
        $race = $data['MRData']['RaceTable']['Races'][0] ?? null;
        if (!$race) return [];
        $out = [];
        foreach (($race['Results'] ?? []) as $r) {
            $out[] = [
                'driverId'    => $r['Driver']['driverId'] ?? '',
                'code'        => $r['Driver']['code'] ?? '',
                'name'        => ($r['Driver']['givenName'] ?? '') . ' ' . ($r['Driver']['familyName'] ?? ''),
                'constructor' => $r['Constructor']['name'] ?? '',
                'position'    => $r['position'] ?? '',
                'laps'        => (int) ($r['laps'] ?? 0),
            ];
        }
        return $out;
    }

    public function get_laps($season, $round) {
        $data = $this->fetch_laps_paged($season, $round);
        if (!$data) return [];
        return $data['MRData']['RaceTable']['Races'][0]['Laps'] ?? [];
    }

    /**
     * laps.json isi banyak baris timing (20 driver x ~58 lap).
     * Mirror membatasi limit=100 per request → harus pindah halaman (offset)
     * dan gabungkan hasilnya. Hasil gabungan di-cache 24 jam.
     */
    private function fetch_laps_paged($season, $round) {
        $cache_file = APPPATH . 'cache/simrace_' . $season . '_' . $round . '_laps_all.json';
        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $this->cache_ttl) {
            return json_decode(file_get_contents($cache_file), true);
        }

        $by_lap = [];
        $empty_unit  = null;
        $saved_race  = null;
        $offset = 0;
        $limit  = 100;
        $ctx = stream_context_create(['http' => ['timeout' => 25, 'user_agent' => 'PaddockID/1.0']]);

        while (true) {
            $url = $this->api_base . $season . '/' . $round . '/laps.json?limit=' . $limit . '&offset=' . $offset;
            $json = @file_get_contents($url, false, $ctx);
            if (!$json) return null;

            $data = json_decode($json, true);
            $race = $data['MRData']['RaceTable']['Races'][0] ?? null;
            if (!$race || empty($race['Laps'])) break;

            $saved_race = $race;
            foreach ($race['Laps'] as $lap) {
                $n = (int) $lap['number'];
                if (!isset($by_lap[$n])) {
                    $by_lap[$n] = ['number' => (string) $n, 'Timings' => []];
                }
                $by_lap[$n]['Timings'] = array_merge($by_lap[$n]['Timings'], $lap['Timings']);
            }

            $offset += (int) ($data['MRData']['limit'] ?? $limit);
            $total   = (int) ($data['MRData']['total'] ?? 0);
            if ($offset >= $total) break;
            if ($offset > 200000) break; // pengaman
        }

        if (!$by_lap) return null;

        ksort($by_lap);
        $saved_race['Laps'] = array_values($by_lap);
        $response = ['MRData' => ['RaceTable' => ['Races' => [$saved_race]]]];

        @file_put_contents($cache_file, json_encode($response));
        return $response;
    }

    public function get_pitstops($season, $round) {
        $data = $this->fetch($season, $round . '/pitstops.json');
        if (!$data) return [];
        return $data['MRData']['RaceTable']['Races'][0]['PitStops'] ?? [];
    }

    /* ---------- SIMULASI ---------- */

    /** Ubah "1:31.929" (m:ss.mmm) atau "1:02:12.345" (h:mm:ss.mmm) jadi detik */
    private function parse_lap_time($time) {
        $parts = explode(':', trim($time));
        if (count($parts) === 3) {
            return (int) $parts[0] * 3600 + (int) $parts[1] * 60 + (float) $parts[2];
        }
        if (count($parts) === 2) {
            return (int) $parts[0] * 60 + (float) $parts[1];
        }
        return (float) $parts[0];
    }

    private function fmt_time($seconds) {
        $seconds = max(0, (float) $seconds);
        $ms = (int) round(($seconds - floor($seconds)) * 1000);
        if ($ms >= 1000) { $ms = 0; $seconds += 1; }
        $total = (int) floor($seconds);
        $h  = intdiv($total, 3600);
        $m  = intdiv($total % 3600, 60);
        $s  = $total % 60;
        if ($h > 0) return sprintf('%d:%02d:%02d.%03d', $h, $m, $s, $ms);
        return sprintf('%d:%02d.%03d', $m, $s, $ms);
    }

    public function simulate($season, $round, $driver, $pit_lap, $duration = null) {
        $laps = $this->get_laps($season, $round);
        if (!$laps) return null;

        $max_lap = 0;
        $lap_map = [];    // lap => driverId => [sec, pos]
        $all_drivers = [];
        foreach ($laps as $lap) {
            $n = (int) $lap['number'];
            $max_lap = max($max_lap, $n);
            $lap_map[$n] = [];
            foreach ($lap['Timings'] as $t) {
                $lap_map[$n][$t['driverId']] = [
                    'sec' => $this->parse_lap_time($t['time']),
                    'pos' => (int) ($t['position'] ?? 0),
                ];
                $all_drivers[$t['driverId']] = true;
            }
        }

        if (!isset($all_drivers[$driver])) {
            return ['error' => 'Driver tidak ikut/tercatat di race ini.'];
        }
        if ($pit_lap < 1 || $pit_lap > $max_lap) {
            return ['error' => 'Lap pit harus antara 1 — ' . $max_lap . '.'];
        }

        // pitstop asli driver ini (sebagai referensi duration default)
        $actual_pit = null;
        foreach ($this->get_pitstops($season, $round) as $ps) {
            if ($ps['driverId'] === $driver) {
                $actual_pit = [
                    'lap'      => (int) $ps['lap'],
                    'stop'     => (int) ($ps['stop'] ?? 1),
                    'duration' => (float) $ps['duration'],
                ];
                break;
            }
        }

        if ($duration === null || $duration === '') {
            $duration = $actual_pit ? $actual_pit['duration'] : 20;
        }
        $duration = (float) $duration;

        // waktu kumulatif per driver per lap (base)
        $cum = [];
        foreach ($all_drivers as $did => $_x) $cum[$did] = [];
        $prev = [];
        foreach ($lap_map as $lap => $timings) {
            foreach ($timings as $did => $row) {
                $prev[$did] = isset($prev[$did]) ? $prev[$did] + $row['sec'] : $row['sec'];
                $cum[$did][$lap] = $prev[$did];
            }
        }

        // sim: tambah duration sebagai penalti di lap pit untuk 1 driver
        $sim_cum = $cum;
        if (isset($sim_cum[$driver])) {
            foreach ($sim_cum[$driver] as $lap => $val) {
                if ($lap >= $pit_lap) $sim_cum[$driver][$lap] += $duration;
            }
        }

        $lap_numbers = array_keys($lap_map);

        // fungsi hitung ranking per lap
        $rank = function ($source, $lap) use ($lap_map) {
            $rows = [];
            foreach ($lap_map[$lap] as $did => $_r) {
                $rows[] = ['d' => $did, 'c' => $source[$did][$lap]];
            }
            usort($rows, function ($a, $b) { return $a['c'] <=> $b['c']; });
            return $rows;
        };

        // info driver (code/name) dari results endpoint
        $drivers_list = $this->get_drivers($season, $round);
        $code_map = [];
        $dinfo = ['id' => $driver, 'code' => '', 'name' => '', 'constructor' => ''];
        foreach ($drivers_list as $dl) {
            $code_map[$dl['driverId']] = $dl['code'];
            if ($dl['driverId'] === $driver) {
                $dinfo['code'] = $dl['code'];
                $dinfo['name'] = $dl['name'];
                $dinfo['constructor'] = $dl['constructor'];
            }
        }

        $rows = [];
        $changes = [];
        $final_compare = [];

        foreach ($lap_numbers as $lap) {
            $base_rows = $rank($cum, $lap);
            $sim_rows  = $rank($sim_cum, $lap);

            $base_pos = 0;
            foreach ($base_rows as $i => $r) { if ($r['d'] === $driver) { $base_pos = $i + 1; break; } }
            $sim_pos = 0;
            foreach ($sim_rows as $i => $r) { if ($r['d'] === $driver) { $sim_pos = $i + 1; break; } }

            $leader_base = $base_rows[0]['c'];
            $leader_sim  = $sim_rows[0]['c'];

            $rows[] = [
                'lap'      => $lap,
                'base_pos' => $base_pos,
                'sim_pos'  => $sim_pos,
                'base_gap' => $cum[$driver][$lap] - $leader_base,
                'sim_gap'  => $sim_cum[$driver][$lap] - $leader_sim,
            ];

            if ($lap >= $pit_lap) {
                for ($i = 0; $i < count($base_rows); $i++) {
                    $who = $base_rows[$i]['d'];
                    $sp = 0;
                    foreach ($sim_rows as $j => $r) { if ($r['d'] === $who) { $sp = $j + 1; break; } }
                    if (($i + 1) !== $sp) {
                        $changes[] = ['lap' => $lap, 'driver' => $who, 'base' => $i + 1, 'sim' => $sp];
                    }
                }
            }
        }

        // ---- FINAL: klasemen akhir berbasis klasifikasi resmi ----
        // finish[] = lap terakhir yang tercatat (jarak tempuh driver).
        // Dipakai supaya mobil lapped / DNF tidak salah urut oleh kumulatif
        // waktu (lebih sedikit lap = kumulatif lebih kecil, tapi justru di belakang).
        $finish = [];
        foreach ($all_drivers as $did => $_x) {
            $finish[$did] = max(array_keys($cum[$did]));
        }

        // klasemen base & sim: sort (jarak tempuh DESC, kumulatif ASC)
        $final_rank = function ($use_sim) use ($all_drivers, $finish, $cum, $sim_cum) {
            $rows = [];
            foreach ($all_drivers as $did => $_x) {
                $arr = $use_sim ? $sim_cum[$did] : $cum[$did];
                $rows[] = ['d' => $did, 'laps' => $finish[$did], 't' => $arr[$finish[$did]]];
            }
            usort($rows, function ($a, $b) {
                if ($a['laps'] !== $b['laps']) return $b['laps'] <=> $a['laps'];
                return $a['t'] <=> $b['t'];
            });
            return $rows;
        };

        $base_rank = $final_rank(false);
        $sim_rank  = $final_rank(true);

        $base_pos_all = [];
        foreach ($base_rank as $i => $r) $base_pos_all[$r['d']] = $i + 1;
        $sim_pos_all  = [];
        foreach ($sim_rank as $i => $r) $sim_pos_all[$r['d']] = $i + 1;

        $final_compare = [];
        foreach ($base_rank as $i => $r) {
            $final_compare[] = [
                'driver' => $r['d'],
                'code'   => $code_map[$r['d']] ?? '',
                'base'   => $i + 1,
                'sim'    => $sim_pos_all[$r['d']] ?? 0,
                'laps'   => $r['laps'],
            ];
        }

        $base_final = $base_pos_all[$driver] ?? 0;

        return [
            'meta' => [
                'season' => $season,
                'round'  => (int) $round,
                'title'  => $this->get_race_title($season, $round),
                'max_lap'=> $max_lap,
            ],
            'driver'      => $dinfo,
            'pit_lap'     => $pit_lap,
            'duration'    => $duration,
            'actual_pit'  => $actual_pit,
            'base_final'  => $base_final,
            'sim_final'   => $sim_pos_all[$driver] ?? 0,
            'driver_rows' => $rows,
            'changes'     => array_slice($changes, 0, 80),
            'final'       => $final_compare,
        ];
    }
}