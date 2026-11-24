<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Error_log_model
 *
 * DB-backed incident logging for Developer Error Center.
 * Uses CI Query Builder and keeps operations minimal/safe so logging failures
 * do not cascade into application failures.
 */
class Error_log_model extends CI_Model
{
    protected $table = 'error_logs';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check whether the error_logs table is available.
     */
    public function table_exists()
    {
        static $exists = null;
        if ($exists !== null) {
            return $exists;
        }

        try {
            if (!isset($this->db) || !$this->db || !$this->db->conn_id) {
                $exists = false;
                return $exists;
            }
            $exists = $this->db->table_exists($this->table);
        } catch (Throwable $e) {
            $exists = false;
        } catch (Exception $e) {
            $exists = false;
        }

        return $exists;
    }

    public function code_exists($code)
    {
        if (!$this->table_exists()) {
            return false;
        }

        try {
            return $this->db->where('error_code', $code)->limit(1)->count_all_results($this->table) > 0;
        } catch (Throwable $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function create_incident(array $data)
    {
        if (!$this->table_exists()) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $row = [
            'error_code'      => isset($data['error_code']) ? substr((string) $data['error_code'], 0, 32) : null,
            'error_type'      => isset($data['error_type']) ? substr((string) $data['error_type'], 0, 100) : null,
            'severity'        => isset($data['severity']) ? substr((string) $data['severity'], 0, 20) : 'error',
            'message'         => isset($data['message']) ? $data['message'] : null,
            'exception_class' => isset($data['exception_class']) ? substr((string) $data['exception_class'], 0, 191) : null,
            'file'            => isset($data['file']) ? substr((string) $data['file'], 0, 500) : null,
            'line'            => isset($data['line']) && $data['line'] !== '' ? (int) $data['line'] : null,
            'request_uri'     => isset($data['request_uri']) ? substr((string) $data['request_uri'], 0, 2048) : null,
            'request_method'  => isset($data['request_method']) ? substr((string) $data['request_method'], 0, 16) : null,
            'user_id'         => isset($data['user_id']) && $data['user_id'] !== '' ? substr((string) $data['user_id'], 0, 64) : null,
            'ip_address'      => isset($data['ip_address']) ? substr((string) $data['ip_address'], 0, 45) : null,
            'user_agent'      => isset($data['user_agent']) ? substr((string) $data['user_agent'], 0, 512) : null,
            'context'         => isset($data['context']) ? $data['context'] : null,
            'status'          => isset($data['status']) ? $data['status'] : 'open',
            'created_at'      => isset($data['created_at']) ? $data['created_at'] : $now,
            'updated_at'      => isset($data['updated_at']) ? $data['updated_at'] : $now,
        ];

        if (empty($row['error_code'])) {
            return false;
        }

        try {
            $ok = $this->db->insert($this->table, $row);
            return $ok ? $this->db->insert_id() : false;
        } catch (Throwable $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function find_by_code($code)
    {
        if (!$this->table_exists() || $code === '') {
            return null;
        }

        try {
            $query = $this->db->where('error_code', $code)->limit(1)->get($this->table);
            return $query ? $query->row_array() : null;
        } catch (Throwable $e) {
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function count_all($filters = [])
    {
        if (!$this->table_exists()) {
            return 0;
        }

        try {
            $this->_apply_filters($filters);
            return (int) $this->db->count_all_results($this->table);
        } catch (Throwable $e) {
            return 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function get_list($filters = [], $limit = 30, $offset = 0)
    {
        if (!$this->table_exists()) {
            return [];
        }

        try {
            $this->_apply_filters($filters);
            $this->db->order_by('created_at', 'DESC')->order_by('id', 'DESC');
            if ($limit > 0) {
                $this->db->limit((int) $limit, (int) $offset);
            }
            $query = $this->db->get($this->table);
            return $query ? $query->result_array() : [];
        } catch (Throwable $e) {
            return [];
        } catch (Exception $e) {
            return [];
        }
    }

    public function get_summary()
    {
        $summary = [
            'available'         => $this->table_exists(),
            'today'             => 0,
            'unresolved'        => 0,
            'resolved'          => 0,
            'investigating'     => 0,
            'ignored'           => 0,
            'by_type'           => [],
            'recent'            => [],
        ];

        if (!$summary['available']) {
            return $summary;
        }

        try {
            $today_start = date('Y-m-d 00:00:00');
            $summary['today'] = (int) $this->db->where('created_at >=', $today_start)->count_all_results($this->table);

            $summary['unresolved'] = (int) $this->db->where_in('status', ['open', 'investigating'])->count_all_results($this->table);
            $summary['resolved'] = (int) $this->db->where('status', 'resolved')->count_all_results($this->table);
            $summary['investigating'] = (int) $this->db->where('status', 'investigating')->count_all_results($this->table);
            $summary['ignored'] = (int) $this->db->where('status', 'ignored')->count_all_results($this->table);

            $types = $this->db->select('COALESCE(NULLIF(error_type, ""), "unknown") AS error_type, COUNT(*) AS total', false)
                ->from($this->table)
                ->group_by('error_type')
                ->order_by('total', 'DESC')
                ->limit(8)
                ->get();
            $summary['by_type'] = $types ? $types->result_array() : [];

            $recent = $this->db->select('id, error_code, error_type, severity, message, status, created_at')
                ->from($this->table)
                ->order_by('created_at', 'DESC')
                ->order_by('id', 'DESC')
                ->limit(10)
                ->get();
            $summary['recent'] = $recent ? $recent->result_array() : [];
        } catch (Throwable $e) {
            // keep partial summary
        } catch (Exception $e) {
            // keep partial summary
        }

        return $summary;
    }

    public function update_status($error_code, $status, $resolution_notes = null, $resolved_by = null)
    {
        if (!$this->table_exists()) {
            return false;
        }

        $allowed = ['open', 'investigating', 'resolved', 'ignored'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $data = [
            'status'           => $status,
            'updated_at'       => $now,
            'resolution_notes' => $resolution_notes !== null && $resolution_notes !== '' ? $resolution_notes : null,
        ];

        if ($status === 'resolved') {
            $data['resolved_at'] = $now;
            $data['resolved_by'] = $resolved_by !== null && $resolved_by !== '' ? substr((string) $resolved_by, 0, 64) : null;
        } else {
            $data['resolved_at'] = null;
            $data['resolved_by'] = null;
        }

        try {
            $this->db->where('error_code', $error_code);
            return $this->db->update($this->table, $data);
        } catch (Throwable $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function _apply_filters($filters)
    {
        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $this->db->group_start()
                ->like('error_code', $search)
                ->or_like('message', $search)
                ->or_like('error_type', $search)
                ->or_like('request_uri', $search)
                ->or_like('exception_class', $search)
                ->group_end();
        }

        if (!empty($filters['error_code'])) {
            $this->db->where('error_code', trim((string) $filters['error_code']));
        }

        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }

        if (!empty($filters['error_type'])) {
            $this->db->where('error_type', $filters['error_type']);
        }

        if (!empty($filters['severity'])) {
            $this->db->where('severity', $filters['severity']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }
    }
}
