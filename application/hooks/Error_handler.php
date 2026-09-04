<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Re-register PHP error & exception handlers setelah bootstrap CI.
 *
 * CI3 default (system/core/Common.php) hanya menampilkan error bila
 * display_errors aktif, sehingga di produksi pengguna dapat halaman kosong.
 * Handler ini menggantikan keduanya agar error selalu dicatat dengan kode
 * dan (untuk fatal/exception) menampilkan halaman error yang ramah.
 */
class Error_handler
{
	public function register()
	{
		require_once APPPATH . 'helpers/error_helper.php';
		set_exception_handler(array($this, 'handle_exception'));
		set_error_handler(array($this, 'handle_php_error'));
	}

	public function handle_exception($exception)
	{
		log_coded_error('PGN-9001', $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine());
		exit_coded_error('PGN-9001', $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine());
	}

	public function handle_php_error($severity, $message, $filepath, $line)
	{
		if (($severity & error_reporting()) !== $severity) {
			return false;
		}

		$detail = $message . ' in ' . $filepath . ':' . $line;
		log_coded_error('PGN-9002', $detail);

		$is_error = (((E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);
		if ($is_error) {
			exit_coded_error('PGN-9002', $detail);
		}

		return true;
	}
}
