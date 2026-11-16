<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Konfigurasi Service Moderasi Konten (XLM-RoBERTa + YOLOv8)
| -------------------------------------------------------------------------
| Service moderasi berjalan sebagai microservice Python (FastAPI) di
| localhost. PaddockID memanggilnya untuk menghitung keputusan moderasi.
|
| FASE SAAT INI: uji kelayakan teknis.
|   - monitor_only = TRUE  -> konten TIDAK pernah diblokir; hasil hanya dicatat.
|   - Model masih base/pretrained, jadi labelnya BELUM akurat.
|
| Ubah monitor_only jadi FALSE hanya setelah model dilatih & threshold final.
|
| enabled  : matikan total integrasi tanpa menghapus kode.
| base_url : alamat microservice.
| timeout  : batas tunggu (detik). Kalau service lambat/mati -> fail-open
|            (konten tetap lolos), tidak pernah menggagalkan posting.
| log_file : berkas catatan hasil moderasi (JSON per baris).
*/

$config['moderation'] = [
    // Default aman: nonaktif. Aktifkan lewat .env hanya setelah service
    // moderasi berjalan dan hasil deteksi bisa ditinjau admin.
    'enabled'      => filter_var(getenv('MODERATION_ENABLED') ?: 'false', FILTER_VALIDATE_BOOLEAN),
    // Jangan ubah ke FALSE sebelum false-positive dievaluasi.
    'monitor_only' => TRUE,
    'base_url'     => 'http://127.0.0.1:8000',
    'timeout'      => 5,
    'log_file'     => APPPATH . 'logs/moderation.log',
];
