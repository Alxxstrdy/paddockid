<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions
{
	/**
	 * Titik tangkap semua error aplikasi:
	 *   - template 'error_db'  -> error database (kode PDB-xxxx, errno di-parse)
	 *   - template 'error_404' -> halaman not-found (biarkan default)
	 *   - lainnya              -> error umum (PGN-9003)
	 */
	public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
	{
		if ($template === 'error_404') {
			return parent::show_error($heading, $message, $template, $status_code);
		}

		if (!function_exists('render_coded_error')) {
			require_once APPPATH . 'helpers/error_helper.php';
		}

		if ($template === 'error_db') {
			$code = $this->_db_code_from_message($message);
			$detail = is_array($message) ? implode(' | ', $message) : $message;
			log_coded_error($code, $detail, 'database');
			return render_coded_error($code, '', $detail);
		}

		$code = 'PGN-9003';
		$detail = is_array($message) ? implode(' | ', $message) : $message;
		log_coded_error($code, $detail, 'show_error');

		set_status_header($status_code);
		return render_coded_error($code, $heading, $detail);
	}

	private function _db_code_from_message($message)
	{
		$text = is_array($message) ? implode(' ', $message) : (string) $message;

		if (preg_match('/Error Number:\s*(\d+)/', $text, $m)) {
			return db_error_code((int) $m[1]);
		}

		if (preg_match('/access denied/i', $text)) {
			return 'PDB-1001';
		}
		if (preg_match('/connect|unable to reach|server has gone away/i', $text)) {
			return 'PDB-1002';
		}
		if (preg_match('/unknown database/i', $text)) {
			return 'PDB-1003';
		}

		return 'PDB-1999';
	}
}
