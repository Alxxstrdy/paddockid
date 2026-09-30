<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Konfigurasi email env-driven (dibaca dari .env per request → tanpa restart PHP).
// Isi SMTP_HOST di .env untuk mengaktifkan SMTP; jika kosong, fallback ke PHP mail().

$config['mailtype']  = 'html';
$config['charset']   = 'utf-8';
$config['newline']   = "\r\n";
$config['wordwrap']  = true;
$config['smtp_timeout'] = 20;

$smtp_host  = getenv('SMTP_HOST');
$smtp_user  = getenv('SMTP_USER');
$smtp_pass  = getenv('SMTP_PASS');
$smtp_port  = getenv('SMTP_PORT');
$smtp_crypto = getenv('SMTP_CRYPTO');

$config['protocol']    = !empty($smtp_host) ? 'smtp' : 'mail';
$config['smtp_host']   = (string) $smtp_host;
$config['smtp_user']   = (string) $smtp_user;
$config['smtp_pass']   = (string) $smtp_pass;
$config['smtp_port']   = (int) (!empty($smtp_port) ? $smtp_port : 587);
$config['smtp_crypto'] = !empty($smtp_crypto) ? $smtp_crypto : 'tls';