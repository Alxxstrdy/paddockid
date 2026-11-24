<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Re-register PHP error & exception handlers after CI bootstrap.
 *
 * Captures uncaught exceptions and PHP runtime errors that reach this handler,
 * records sanitized incidents (PDK-*) for the Developer Error Center, and shows
 * a safe user-facing response without internal details.
 */
class Error_handler
{
	public function register()
	{
		require_once APPPATH . 'helpers/error_helper.php';
		set_exception_handler(array($this, 'handle_exception'));
		set_error_handler(array($this, 'handle_php_error'));
		register_shutdown_function(array($this, 'handle_shutdown'));
	}

	public function handle_exception($exception)
	{
		if (!function_exists('create_error_incident')) {
			require_once APPPATH . 'helpers/error_helper.php';
		}

		$message = ($exception instanceof Throwable || $exception instanceof Exception)
			? $exception->getMessage()
			: 'Unhandled exception';

		$meta = array(
			'exception'  => $exception,
			'context'    => 'exception',
			'error_type' => 'exception',
			'severity'   => 'error',
		);

		try {
			$incident_code = create_error_incident('PGN-9001', $message, 'exception', $meta);
		} catch (Throwable $e) {
			$incident_code = function_exists('generate_incident_code') ? generate_incident_code('PDK') : 'PDK-UNAVAILABLE';
		} catch (Exception $e) {
			$incident_code = function_exists('generate_incident_code') ? generate_incident_code('PDK') : 'PDK-UNAVAILABLE';
		}

		$this->_render_safe_incident($incident_code, 500);
	}

	public function handle_php_error($severity, $message, $filepath, $line)
	{
		if (($severity & error_reporting()) !== $severity) {
			return false;
		}

		$is_fatal = (((E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR) & $severity) === $severity);
		$detail = (string) $message;
		$meta = array(
			'context'    => 'php_error',
			'error_type' => 'php_error',
			'severity'   => $is_fatal ? 'critical' : 'warning',
			'file'       => $filepath,
			'line'       => $line,
			'extra'      => array('php_severity' => $severity),
		);

		try {
			if (!function_exists('create_error_incident')) {
				require_once APPPATH . 'helpers/error_helper.php';
			}
			$incident_code = create_error_incident('PGN-9002', $detail, 'php_error', $meta);
		} catch (Throwable $e) {
			$incident_code = null;
		} catch (Exception $e) {
			$incident_code = null;
		}

		if ($is_fatal) {
			$this->_render_safe_incident($incident_code, 500);
		}

		// Non-fatal errors: log incident only, let PHP/CI continue normal flow.
		return true;
	}

	public function handle_shutdown()
	{
		$error = error_get_last();
		if (!$error) {
			return;
		}

		$fatal = E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR;
		if (($error['type'] & $fatal) !== $error['type']) {
			return;
		}

		// Avoid duplicate output if headers/body already sent in an unexpected state.
		if (headers_sent()) {
			try {
				if (!function_exists('create_error_incident')) {
					require_once APPPATH . 'helpers/error_helper.php';
				}
				create_error_incident('PGN-9002', isset($error['message']) ? $error['message'] : 'Fatal shutdown error', 'php_shutdown', array(
					'context'    => 'php_shutdown',
					'error_type' => 'php_error',
					'severity'   => 'critical',
					'file'       => isset($error['file']) ? $error['file'] : null,
					'line'       => isset($error['line']) ? $error['line'] : null,
				));
			} catch (Throwable $e) {
				// no-op
			} catch (Exception $e) {
				// no-op
			}
			return;
		}

		try {
			if (!function_exists('create_error_incident')) {
				require_once APPPATH . 'helpers/error_helper.php';
			}
			$incident_code = create_error_incident('PGN-9002', isset($error['message']) ? $error['message'] : 'Fatal shutdown error', 'php_shutdown', array(
				'context'    => 'php_shutdown',
				'error_type' => 'php_error',
				'severity'   => 'critical',
				'file'       => isset($error['file']) ? $error['file'] : null,
				'line'       => isset($error['line']) ? $error['line'] : null,
			));
		} catch (Throwable $e) {
			$incident_code = null;
		} catch (Exception $e) {
			$incident_code = null;
		}

		$this->_render_safe_incident($incident_code, 500);
	}

	protected function _render_safe_incident($incident_code, $status = 500)
	{
		if (!$incident_code && function_exists('generate_incident_code')) {
			$incident_code = generate_incident_code('PDK');
		}

		if (function_exists('is_cli') && is_cli()) {
			fwrite(STDERR, ($incident_code ?: 'PDK-UNAVAILABLE') . ': Terjadi kendala teknis.' . PHP_EOL);
			exit(1);
		}

		$status = (int) $status;
		if ($status < 400 || $status > 599) {
			$status = 500;
		}

		while (ob_get_level() > 0) {
			@ob_end_clean();
		}

		if (function_exists('set_status_header')) {
			set_status_header($status);
		} elseif (!headers_sent()) {
			http_response_code($status);
		}

		$is_json = function_exists('_paddock_is_json_request') ? _paddock_is_json_request() : false;
		if ($is_json) {
			if (!headers_sent()) {
				header('Content-Type: application/json; charset=utf-8');
			}
			echo json_encode(array(
				'status'     => 'error',
				'success'    => false,
				'message'    => 'Terjadi kendala saat memproses permintaan.',
				'error_code' => $incident_code,
				'code'       => $incident_code,
			), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			exit(1);
		}

		if (function_exists('render_coded_error')) {
			echo render_coded_error($incident_code, 'Terjadi Kendala', '', array('incident_code' => $incident_code));
			exit(1);
		}

		if (!headers_sent()) {
			header('Content-Type: text/html; charset=utf-8');
		}
		echo 'Terjadi kendala. Kode laporan: ' . htmlspecialchars((string) $incident_code, ENT_QUOTES, 'UTF-8');
		exit(1);
	}
}
