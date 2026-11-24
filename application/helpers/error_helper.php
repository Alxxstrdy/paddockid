<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Error Code Helper
|--------------------------------------------------------------------------
| Core helpers for PaddockID coded errors and centralized incident reporting.
|
| Existing/internal catalog codes (PDB/PGN/PPS/...) remain supported for
| diagnosis and file logging. Each captured occurrence can also create a
| public incident report code (PDK-XXXXXXXX) stored in MySQL (error_logs)
| with sanitized technical context for the Developer Error Center.
|
| Security: never expose stack traces, SQL, file paths, line numbers,
| credentials, or internal server details to end users.
*/

if (!function_exists('error_codes')) {
    function error_codes()
    {
        static $codes = null;
        if ($codes === null) {
            $codes = function_exists('config_item') ? config_item('error_codes') : null;
            if (!is_array($codes)) {
                $file = APPPATH . 'config/error_codes.php';
                $local = array();
                if (file_exists($file)) {
                    require $file;
                    $local = isset($config['error_codes']) ? $config['error_codes'] : array();
                }
                $codes = is_array($local) ? $local : array();
            }
        }
        return $codes;
    }
}

if (!function_exists('error_code_info')) {
    function error_code_info($code)
    {
        $codes = error_codes();
        return isset($codes[$code]) ? $codes[$code] : null;
    }
}

if (!function_exists('db_error_code')) {
    function db_error_code($errno)
    {
        $errno = (int) $errno;
        $map = array(
            1045 => 'PDB-1001', // Access denied
            2002 => 'PDB-1002', // Can't connect (socket)
            2003 => 'PDB-1002', // Can't connect (TCP)
            2013 => 'PDB-1002', // Lost connection
            1049 => 'PDB-1003', // Unknown database
            1146 => 'PDB-1004', // Table doesn't exist
            1054 => 'PDB-1005', // Unknown column
            1062 => 'PDB-1006', // Duplicate entry
            1064 => 'PDB-1007', // Syntax error
            1451 => 'PDB-1008', // FK constraint (delete)
            1452 => 'PDB-1008', // FK constraint (insert)
        );
        return isset($map[$errno]) ? $map[$errno] : 'PDB-1999';
    }
}

if (!function_exists('_paddock_ci')) {
    function _paddock_ci()
    {
        if (function_exists('get_instance')) {
            try {
                return get_instance();
            } catch (Throwable $e) {
                return null;
            } catch (Exception $e) {
                return null;
            }
        }
        return null;
    }
}

if (!function_exists('_request_is_ajax')) {
    function _request_is_ajax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }

        $accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
        if ($accept !== '' && stripos($accept, 'application/json') !== false) {
            return true;
        }

        $ci = _paddock_ci();
        if ($ci && isset($ci->input) && method_exists($ci->input, 'is_ajax_request')) {
            try {
                return (bool) $ci->input->is_ajax_request();
            } catch (Throwable $e) {
                // ignore
            } catch (Exception $e) {
                // ignore
            }
        }

        return false;
    }
}

if (!function_exists('_paddock_is_json_request')) {
    function _paddock_is_json_request()
    {
        if (_request_is_ajax()) {
            return true;
        }
        $content_type = isset($_SERVER['CONTENT_TYPE']) ? strtolower($_SERVER['CONTENT_TYPE']) : '';
        return ($content_type !== '' && strpos($content_type, 'application/json') !== false);
    }
}

if (!function_exists('_paddock_current_user_id')) {
    function _paddock_current_user_id()
    {
        $ci = _paddock_ci();
        if (!$ci || !isset($ci->session)) {
            return null;
        }

        try {
            $session = $ci->session->userdata('user_logged_in');
            if (is_array($session)) {
                if (!empty($session['user_id'])) return (string) $session['user_id'];
                if (!empty($session['id_user'])) return (string) $session['id_user'];
                if (!empty($session['id'])) return (string) $session['id'];
            }
            $uid = $ci->session->userdata('user_id');
            if (!empty($uid)) return (string) $uid;
        } catch (Throwable $e) {
            return null;
        } catch (Exception $e) {
            return null;
        }

        return null;
    }
}

if (!function_exists('_paddock_sanitize_request_uri')) {
    function _paddock_sanitize_request_uri($uri = null)
    {
        if ($uri === null) {
            $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        }
        $uri = (string) $uri;
        if ($uri === '') {
            return '';
        }

        $parts = parse_url($uri);
        $path = isset($parts['path']) ? $parts['path'] : $uri;
        $query = isset($parts['query']) ? $parts['query'] : '';

        if ($query !== '') {
            parse_str($query, $params);
            $redacted = array();
            $sensitive = array('token','password','pass','secret','key','auth','authorization','session','cookie','csrf','api_key','apikey','access_token','refresh_token');
            foreach ($params as $k => $v) {
                $lk = strtolower((string) $k);
                $is_sensitive = false;
                foreach ($sensitive as $needle) {
                    if (strpos($lk, $needle) !== false) {
                        $is_sensitive = true;
                        break;
                    }
                }
                $redacted[$k] = $is_sensitive ? '[REDACTED]' : (is_scalar($v) ? $v : '[REDACTED]');
            }
            $path .= '?' . http_build_query($redacted);
        }

        return substr($path, 0, 2048);
    }
}

if (!function_exists('_paddock_sanitize_user_agent')) {
    function _paddock_sanitize_user_agent($ua = null)
    {
        if ($ua === null) {
            $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        }
        $ua = preg_replace('/[\x00-\x1F\x7F]/', '', (string) $ua);
        return substr($ua, 0, 512);
    }
}

if (!function_exists('_paddock_sanitize_string')) {
    function _paddock_sanitize_string($value, $max_len = 2000)
    {
        if ($value === null) {
            return null;
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_scalar($value)) {
            $str = (string) $value;
        } else {
            $str = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($str === false) {
                $str = '[unserializable]';
            }
        }

        // Redact common secrets/tokens in free-form strings
        $patterns = array(
            '/(password|passwd|pwd|secret|token|api[_-]?key|authorization|cookie|session[_-]?id)\s*[:=]\s*[^\s,;]+/i' => '$1=[REDACTED]',
            '/Bearer\s+[A-Za-z0-9\-\._~\+\/]+=*/i' => 'Bearer [REDACTED]',
        );
        foreach ($patterns as $pattern => $replacement) {
            $str = preg_replace($pattern, $replacement, $str);
        }

        $str = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $str);
        if (strlen($str) > $max_len) {
            $str = substr($str, 0, $max_len) . '...[truncated]';
        }
        return $str;
    }
}

if (!function_exists('_paddock_sanitize_context')) {
    function _paddock_sanitize_context($context)
    {
        if ($context === null || $context === '' || $context === array()) {
            return null;
        }

        $sensitive_keys = array('password','passwd','pwd','secret','token','api_key','apikey','access_token','refresh_token','authorization','cookie','cookies','session','session_id','phpsessid','csrf','csrf_token','xsrf','credit_card','card_number','cvv');

        $sanitize = function ($value, $key = null) use (&$sanitize, $sensitive_keys) {
            if ($key !== null) {
                $lk = strtolower((string) $key);
                foreach ($sensitive_keys as $needle) {
                    if ($lk === $needle || strpos($lk, $needle) !== false) {
                        return '[REDACTED]';
                    }
                }
            }

            if (is_array($value)) {
                $out = array();
                $count = 0;
                foreach ($value as $k => $v) {
                    $out[$k] = $sanitize($v, $k);
                    $count++;
                    if ($count >= 40) { // avoid huge payloads
                        $out['__truncated'] = true;
                        break;
                    }
                }
                return $out;
            }

            if (is_object($value)) {
                return $sanitize((array) $value, $key);
            }

            if (is_string($value)) {
                return _paddock_sanitize_string($value, 1000);
            }

            if (is_scalar($value) || $value === null) {
                return $value;
            }

            return '[unserializable]';
        };

        $sanitized = $sanitize($context);
        $json = json_encode($sanitized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            $json = json_encode(array('note' => 'context could not be encoded'));
        }
        if ($json !== null && strlen($json) > 10000) {
            $json = substr($json, 0, 10000) . '...[truncated]"';
        }
        return $json;
    }
}

if (!function_exists('_paddock_get_request_context')) {
    function _paddock_get_request_context()
    {
        $ip = '';
        if (function_exists('get_real_ip')) {
            try {
                $ip = (string) get_real_ip();
            } catch (Throwable $e) {
                $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            } catch (Exception $e) {
                $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            }
        } else {
            $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        }

        return array(
            'request_uri'    => _paddock_sanitize_request_uri(),
            'request_method' => isset($_SERVER['REQUEST_METHOD']) ? substr((string) $_SERVER['REQUEST_METHOD'], 0, 16) : null,
            'user_id'        => _paddock_current_user_id(),
            'ip_address'     => substr((string) $ip, 0, 45),
            'user_agent'     => _paddock_sanitize_user_agent(),
        );
    }
}

if (!function_exists('_paddock_incident_severity')) {
    function _paddock_incident_severity($internal_code = '', $detail = '')
    {
        $code = strtoupper((string) $internal_code);
        if ($code === 'PGN-9002' && stripos((string) $detail, 'Severity: Warning') !== false) {
            return 'warning';
        }
        if (strpos($code, 'PDB-') === 0 || strpos($code, 'PGN-') === 0 || strpos($code, 'PPS-5') === 0) {
            return 'error';
        }
        if (strpos($code, 'PUP-') === 0 || strpos($code, 'PAU-') === 0 || strpos($code, 'PRC-') === 0) {
            return 'warning';
        }
        return 'error';
    }
}

if (!function_exists('_paddock_incident_type')) {
    function _paddock_incident_type($internal_code = '', $context = '')
    {
        $ctx = strtolower((string) $context);
        if ($ctx !== '') {
            return substr(preg_replace('/[^a-z0-9_\-\.]/', '_', $ctx), 0, 100);
        }
        $code = strtoupper((string) $internal_code);
        $prefix = substr($code, 0, 3);
        $map = array(
            'PDB' => 'database',
            'PUP' => 'upload',
            'PPS' => 'api',
            'PFT' => 'integration',
            'PAU' => 'auth',
            'PRC' => 'integration',
            'PCG' => 'config',
            'PGN' => 'application',
        );
        if (isset($map[$prefix])) {
            return $map[$prefix];
        }
        return 'application';
    }
}

if (!function_exists('generate_incident_code')) {
    function generate_incident_code($prefix = 'PDK', $length = 8)
    {
        $bytes = 4;
        try {
            if (function_exists('random_bytes')) {
                $raw = random_bytes($bytes);
            } elseif (function_exists('openssl_random_pseudo_bytes')) {
                $raw = openssl_random_pseudo_bytes($bytes);
            } else {
                $raw = md5(uniqid((string) mt_rand(), true) . microtime(true), true);
            }
        } catch (Throwable $e) {
            $raw = md5(uniqid((string) mt_rand(), true) . microtime(true), true);
        } catch (Exception $e) {
            $raw = md5(uniqid((string) mt_rand(), true) . microtime(true), true);
        }

        $hex = strtoupper(substr(bin2hex($raw), 0, $length));
        if (strlen($hex) < $length) {
            $hex = str_pad($hex, $length, '0', STR_PAD_RIGHT);
        }
        return $prefix . '-' . $hex;
    }
}

if (!function_exists('create_error_incident')) {
    /**
     * Create a DB-backed incident (PDK-*) with safe fallback logging.
     * Returns incident code string or null if incident creation was skipped.
     */
    function create_error_incident($internal_code, $detail = '', $context = '', $meta = array())
    {
        static $in_progress = false;

        if ($in_progress) {
            return null;
        }

        $in_progress = true;
        $incident_code = null;

        try {
            $ci = _paddock_ci();

            // Try to generate a unique code when DB model is available.
            $candidate = null;
            $model = null;
            if ($ci) {
                try {
                    if (!isset($ci->error_log_model)) {
                        $ci->load->model('Error_log_model', 'error_log_model');
                    }
                    $model = isset($ci->error_log_model) ? $ci->error_log_model : null;
                } catch (Throwable $e) {
                    $model = null;
                } catch (Exception $e) {
                    $model = null;
                }
            }

            for ($i = 0; $i < 5; $i++) {
                $candidate = generate_incident_code('PDK');
                if (!$model || !method_exists($model, 'code_exists') || !$model->code_exists($candidate)) {
                    break;
                }
                $candidate = null;
            }
            if ($candidate === null) {
                $candidate = generate_incident_code('PDK') . substr(strtoupper(dechex(mt_rand(0, 255))), 0, 2);
            }

            $req = _paddock_get_request_context();
            $exception = isset($meta['exception']) ? $meta['exception'] : null;
            $exception_class = isset($meta['exception_class']) ? $meta['exception_class'] : null;
            $file = isset($meta['file']) ? $meta['file'] : null;
            $line = isset($meta['line']) ? $meta['line'] : null;
            $severity = isset($meta['severity']) ? $meta['severity'] : _paddock_incident_severity($internal_code, $detail);
            $error_type = isset($meta['error_type']) ? $meta['error_type'] : _paddock_incident_type($internal_code, $context);

            if ($exception instanceof Throwable || $exception instanceof Exception) {
                $exception_class = $exception_class ?: get_class($exception);
                $file = $file ?: $exception->getFile();
                $line = $line ?: $exception->getLine();
            }

            $summary_parts = array();
            if (!empty($internal_code)) {
                $summary_parts[] = 'internal_code=' . $internal_code;
            }
            $safe_detail = _paddock_sanitize_string($detail, 2000);
            if ($safe_detail !== null && $safe_detail !== '') {
                $summary_parts[] = $safe_detail;
            }
            $summary = implode(' | ', $summary_parts);
            if ($summary === '') {
                $summary = 'Unhandled application error';
            }

            $ctx = array();
            if ($context !== '' && $context !== null) {
                $ctx['context'] = $context;
            }
            if (!empty($internal_code)) {
                $ctx['internal_code'] = $internal_code;
            }
            if (isset($meta['extra']) && is_array($meta['extra'])) {
                $ctx['extra'] = $meta['extra'];
            }

            $trace = null;
            if (isset($meta['trace']) && is_string($meta['trace'])) {
                $trace = $meta['trace'];
            } elseif ($exception instanceof Throwable || $exception instanceof Exception) {
                try {
                    $trace = $exception->getTraceAsString();
                } catch (Throwable $te) {
                    $trace = null;
                } catch (Exception $te) {
                    $trace = null;
                }
            }
            if ($trace) {
                $ctx['stack_trace'] = _paddock_sanitize_string($trace, 8000);
            }

            $data = array(
                'error_code'      => $candidate,
                'error_type'      => $error_type,
                'severity'        => $severity,
                'message'         => $summary,
                'exception_class' => $exception_class ? _paddock_sanitize_string($exception_class, 191) : null,
                'file'            => $file ? _paddock_sanitize_string($file, 500) : null,
                'line'            => $line,
                'request_uri'     => $req['request_uri'],
                'request_method'  => $req['request_method'],
                'user_id'         => $req['user_id'],
                'ip_address'      => $req['ip_address'],
                'user_agent'      => $req['user_agent'],
                'context'         => _paddock_sanitize_context($ctx),
                'status'          => 'open',
            );

            $saved = false;
            if ($model && method_exists($model, 'create_incident')) {
                try {
                    $saved = (bool) $model->create_incident($data);
                } catch (Throwable $e) {
                    $saved = false;
                } catch (Exception $e) {
                    $saved = false;
                }
            }

            $incident_code = $candidate;

            // Always keep a file-log fallback trail (sanitized) so incidents are traceable
            // even if DB logging is unavailable.
            $file_line = '[INCIDENT ' . $incident_code . ']';
            if (!empty($internal_code)) {
                $file_line .= ' [' . $internal_code . ']';
            }
            $file_line .= ' type=' . $error_type . ' severity=' . $severity;
            $file_line .= ' ' . _paddock_sanitize_string($summary, 1000);
            if (!$saved) {
                $file_line .= ' | db_log=failed_or_unavailable';
            }
            log_message($severity === 'warning' ? 'error' : 'error', $file_line);
        } catch (Throwable $e) {
            try {
                log_message('error', '[INCIDENT-FALLBACK] Failed to create error incident: ' . _paddock_sanitize_string($e->getMessage(), 300));
            } catch (Throwable $e2) {
                // give up silently
            } catch (Exception $e2) {
                // give up silently
            }
            $incident_code = $incident_code ?: null;
        } catch (Exception $e) {
            try {
                log_message('error', '[INCIDENT-FALLBACK] Failed to create error incident');
            } catch (Exception $e2) {
                // give up silently
            }
            $incident_code = $incident_code ?: null;
        }

        $in_progress = false;
        return $incident_code;
    }
}

if (!function_exists('log_coded_error')) {
    /**
     * Backward-compatible coded error logging.
     * Creates a per-occurrence incident (PDK-*) and writes a sanitized file log.
     * Returns the incident code when available.
     */
    function log_coded_error($code, $detail = '', $context = '', $meta = array())
    {
        $safe_detail = is_array($detail) ? implode(' | ', $detail) : (string) $detail;
        $line = '[' . $code . '] ' . _paddock_sanitize_string($safe_detail, 2000);
        if ($context !== '') {
            $line .= ' | context: ' . _paddock_sanitize_string($context, 200);
        }
        log_message('error', $line);

        $incident_code = null;
        try {
            $incident_code = create_error_incident($code, $safe_detail, $context, is_array($meta) ? $meta : array());
        } catch (Throwable $e) {
            $incident_code = null;
        } catch (Exception $e) {
            $incident_code = null;
        }

        return $incident_code;
    }
}

if (!function_exists('error_friendly_message')) {
    function error_friendly_message($internal_code = '')
    {
        $info = error_code_info($internal_code);
        if ($info && !empty($info['title'])) {
            // keep user-facing message generic/safe; title is used for context but avoid internals
        }
        return 'Maaf, terjadi kendala saat memproses permintaan Anda. Silakan coba lagi beberapa saat.';
    }
}

if (!function_exists('render_coded_error')) {
    /**
     * Render a safe, user-friendly coded error page.
     * $code is treated as the public incident/report code when available.
     */
    function render_coded_error($code, $heading = '', $detail = '', $extra = array())
    {
        $environment = defined('ENVIRONMENT') ? ENVIRONMENT : 'production';
        $incident_code = is_array($extra) && !empty($extra['incident_code']) ? $extra['incident_code'] : $code;
        $user_message = is_array($extra) && !empty($extra['user_message'])
            ? $extra['user_message']
            : 'Kami tidak dapat memproses permintaan Anda saat ini. Silakan coba kembali beberapa saat.';

        $data = array(
            'code'          => $incident_code,
            'incident_code' => $incident_code,
            'internal_code' => is_array($extra) && !empty($extra['internal_code']) ? $extra['internal_code'] : null,
            'heading'       => $heading !== '' ? $heading : 'Terjadi Kendala',
            'user_message'  => $user_message,
            'detail'        => '', // never expose internal details to end users
            'info'          => null,
            'environment'   => $environment,
            'show_detail'   => false,
            'support_hint'  => 'Jika masalah berlanjut, berikan kode laporan tersebut kepada tim PaddockID.',
        );

        $view = VIEWPATH . 'errors' . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'error_coded.php';
        if (!file_exists($view)) {
            $view = VIEWPATH . 'errors' . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'error_general.php';
        }

        extract($data);
        ob_start();
        include $view;
        return ob_get_clean();
    }
}

if (!function_exists('exit_coded_error')) {
    function exit_coded_error($code, $detail = '', $heading = '', $meta = array())
    {
        $safe_detail = is_array($detail) ? implode(' | ', $detail) : (string) $detail;
        $incident_code = null;

        try {
            $incident_code = log_coded_error($code, $safe_detail, isset($meta['context']) ? $meta['context'] : '', $meta);
        } catch (Throwable $e) {
            $incident_code = null;
        } catch (Exception $e) {
            $incident_code = null;
        }

        if (!$incident_code) {
            $incident_code = generate_incident_code('PDK');
        }

        if (function_exists('is_cli') && is_cli()) {
            fwrite(STDERR, $incident_code . ': Terjadi kendala teknis.' . PHP_EOL);
            exit(1);
        }

        $status = isset($meta['http_status']) ? (int) $meta['http_status'] : 500;
        if ($status < 400 || $status > 599) {
            $status = 500;
        }
        if (function_exists('set_status_header')) {
            set_status_header($status);
        } else {
            http_response_code($status);
        }

        if (_paddock_is_json_request()) {
            $ci = _paddock_ci();
            $payload = array(
                'status'     => 'error',
                'success'    => false,
                'message'    => 'Terjadi kendala saat memproses permintaan.',
                'error_code' => $incident_code,
                'code'       => $incident_code,
            );
            $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($ci && isset($ci->output)) {
                $ci->output
                    ->set_status_header($status)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output($json);
                $ci->output->_display();
            } else {
                if (!headers_sent()) {
                    header('Content-Type: application/json; charset=utf-8');
                }
                echo $json;
            }
            exit(1);
        }

        echo render_coded_error($incident_code, $heading, $safe_detail, array(
            'incident_code' => $incident_code,
            'internal_code' => $code,
        ));
        exit(1);
    }
}

if (!function_exists('safe_error_msg')) {
    /**
     * Return generic message to user and log sanitized exception detail.
     */
    function safe_error_msg($e)
    {
        $msg = ($e instanceof Throwable || $e instanceof Exception) ? $e->getMessage() : (string) $e;
        $meta = array(
            'exception'  => ($e instanceof Throwable || $e instanceof Exception) ? $e : null,
            'error_type' => 'application',
            'severity'   => 'error',
        );
        $incident = null;
        try {
            $incident = create_error_incident('PGN-9001', $msg, 'safe_error_msg', $meta);
        } catch (Throwable $ex) {
            $incident = null;
        } catch (Exception $ex) {
            $incident = null;
        }
        log_message('error', 'Exception captured' . ($incident ? ' [' . $incident . ']' : '') . ': ' . _paddock_sanitize_string($msg, 500));
        return 'Terjadi kesalahan. Silakan coba lagi.';
    }
}

if (!function_exists('throttle')) {
    /**
     * Rate-limit aksi konten (anti-spam & anti-coin-farm): IP + identity (id_user/email).
     * Mencatat setiap percobaan; mengembalikan true bila masih diizinkan.
     */
    function throttle($action, $max_attempts, $window_minutes, $identity = null)
    {
        $ci =& get_instance();
        $ci->load->model('Auth_model');

        if (!$ci->Auth_model->check_rate_limit(get_real_ip(), $action, $max_attempts, $window_minutes, $identity)) {
            return false;
        }

        $ci->Auth_model->log_rate_limit_action(get_real_ip(), $action, $identity);
        return true;
    }
}
