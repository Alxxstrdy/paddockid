<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Konfigurasi default: gunakan PHP mail()
// Jika ingin pakai SMTP, isi smtp_host, smtp_user, smtp_pass, smtp_port, dan smtp_crypto
$config['protocol']  = 'mail';
$config['mailtype']  = 'html';
$config['charset']   = 'utf-8';
$config['newline']   = "\r\n";
$config['wordwrap']  = true;

// === SMTP (isi jika ingin ganti protocol ke 'smtp') ===
$config['smtp_host']  = '';
$config['smtp_user']  = '';
$config['smtp_pass']  = '';
$config['smtp_port']  = 587;
$config['smtp_crypto'] = 'tls';
