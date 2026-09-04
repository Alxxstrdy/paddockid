<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Error Code Helper
|--------------------------------------------------------------------------
| Fungsi inti untuk sistem kode error PaddockID:
|   - error_codes()        : mengambil registry kode (config/error_codes.php)
|   - error_code_info()    : detail satu kode
|   - db_error_code()      : memetakan errno MySQL -> kode PDB-xxxx
|   - log_coded_error()    : menulis log dengan prefiks kode
|   - exit_coded_error()   : log + render + exit (halaman/JSON/CLI)
|   - render_coded_error() : render buffer halaman error (dipakai MY_Exceptions)
*/

if (!function_exists('error_codes')) {
    function error_codes()
    {
        static $codes = null;
        if ($codes === null) {
            $codes = config_item('error_codes');
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

if (!function_exists('log_coded_error')) {
    function log_coded_error($code, $detail, $context = '')
    {
        $line = '[' . $code . '] ' . $detail;
        if ($context !== '') {
            $line .= ' | context: ' . $context;
        }
        log_message('error', $line);
    }
}

if (!function_exists('_request_is_ajax')) {
    function _request_is_ajax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }
}

if (!function_exists('render_coded_error')) {
    function render_coded_error($code, $heading = '', $detail = '')
    {
        $info = error_code_info($code);
        if ($heading === '') {
            $heading = $info ? $info['title'] : 'Terjadi Kesalahan';
        }
        $environment = defined('ENVIRONMENT') ? ENVIRONMENT : 'development';
        $data = array(
            'code'        => $code,
            'heading'     => $heading,
            'detail'      => $detail,
            'info'        => $info,
            'environment' => $environment,
            'show_detail' => ($environment === 'development'),
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
    function exit_coded_error($code, $detail = '', $heading = '')
    {
        log_coded_error($code, $detail);

        if (is_cli()) {
            fwrite(STDERR, $code . ': ' . $detail . PHP_EOL);
            exit(1);
        }

        set_status_header(500);

        if (_request_is_ajax()) {
            $info = error_code_info($code);
            $environment = defined('ENVIRONMENT') ? ENVIRONMENT : 'development';
            $ci =& get_instance();
            $payload = array(
                'status'  => 'error',
                'code'    => $code,
                'message' => ($environment === 'development')
                    ? $heading . ($detail !== '' ? ' | ' . $detail : '')
                    : 'Terjadi kendala teknis. Kode: ' . $code,
            );
            if ($environment === 'development') {
                $payload['fix'] = $info ? ($info['cause'] . ' | ' . $info['fix']) : '';
            }
            $ci->output
                ->set_content_type('application/json')
                ->set_output(json_encode($payload));
            $ci->output->_display();
            exit(1);
        }

        echo render_coded_error($code, $heading, $detail);
        exit(1);
    }
}
