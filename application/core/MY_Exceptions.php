<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions
{
	/**
	 * Central application error capture point:
	 *   - template 'error_db'  -> database errors (map errno to internal PDB code)
	 *   - template 'error_404' -> default 404 handling
	 *   - others               -> general application errors
	 *
	 * Creates a per-occurrence incident (PDK-*) for developer investigation and
	 * renders a safe, user-friendly response without internal details.
	 */
	public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
	{
		if ($template === 'error_404') {
			return parent::show_error($heading, $message, $template, $status_code);
		}

		if (!function_exists('create_error_incident')) {
			require_once APPPATH . 'helpers/error_helper.php';
		}

		$detail = is_array($message) ? implode(' | ', $message) : (string) $message;
		$internal_code = 'PGN-9003';
		$error_type = 'application';
		$severity = 'error';
		$http_status = (int) $status_code;
		if ($http_status < 400 || $http_status > 599) {
			$http_status = 500;
		}

		if ($template === 'error_db') {
			$internal_code = $this->_db_code_from_message($message);
			$error_type = 'database';
			// DB errors should be treated as server-side failures.
			if ($http_status < 500) {
				$http_status = 500;
			}
		}

		$meta = array(
			'context'     => $template === 'error_db' ? 'database' : 'show_error',
			'error_type'  => $error_type,
			'severity'    => $severity,
			'http_status' => $http_status,
			'extra'       => array(
				'template' => $template,
				'heading'  => is_scalar($heading) ? (string) $heading : '',
			),
		);

		// Keep sanitized file log entry for existing log viewers/tools.
		if (function_exists('log_message')) {
			$log_detail = function_exists('_paddock_sanitize_string')
				? _paddock_sanitize_string($detail, 2000)
				: substr($detail, 0, 2000);
			log_message('error', '[' . $internal_code . '] ' . $log_detail . ' | context: ' . $meta['context']);
		}

		$incident_code = null;
		try {
			$incident_code = create_error_incident($internal_code, $detail, $meta['context'], $meta);
		} catch (Throwable $e) {
			$incident_code = null;
		} catch (Exception $e) {
			$incident_code = null;
		}
		if (!$incident_code && function_exists('generate_incident_code')) {
			$incident_code = generate_incident_code('PDK');
		}

		if (function_exists('set_status_header')) {
			set_status_header($http_status);
		} else {
			http_response_code($http_status);
		}

		// JSON/AJAX requests get a safe JSON payload with incident code.
		$is_json = false;
		if (function_exists('_paddock_is_json_request')) {
			$is_json = _paddock_is_json_request();
		} elseif (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
			$is_json = true;
		}
		if ($is_json) {
			$payload = json_encode(array(
				'status'     => 'error',
				'success'    => false,
				'message'    => 'Terjadi kendala saat memproses permintaan.',
				'error_code' => $incident_code,
				'code'       => $incident_code,
			), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			if (!headers_sent()) {
				header('Content-Type: application/json; charset=utf-8');
			}
			echo $payload;
			exit(1);
		}

		if (function_exists('render_coded_error')) {
			echo render_coded_error($incident_code, $heading, $detail, array(
				'incident_code' => $incident_code,
				'internal_code' => $internal_code,
			));
			exit(1);
		}

		// Last-resort fallback without exposing internal details.
		echo 'Terjadi kendala. Kode laporan: ' . htmlspecialchars((string) $incident_code, ENT_QUOTES, 'UTF-8');
		exit(1);
	}

	private function _db_code_from_message($message)
	{
		$text = is_array($message) ? implode(' ', $message) : (string) $message;
		if (preg_match('/Error Number:\s*(\d+)/i', $text, $m) && function_exists('db_error_code')) {
			return db_error_code($m[1]);
		}
		return 'PDB-1999';
	}
}
