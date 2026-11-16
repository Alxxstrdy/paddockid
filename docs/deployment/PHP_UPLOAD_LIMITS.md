# PHP Upload Limits — PaddockID

Aplikasi menerima hingga **4 gambar per post**, masing-masing maksimal **10 MB**,
avatar **5 MB**, banner **10 MB**, dan banner iklan **2 MB**. Batas PHP saat ini
(`upload_max_filesize=2M`, `post_max_size=8M`) lebih kecil daripada klaim UI,
sehingga upload yang tampak valid bisa ditolak PHP sebelum masuk aplikasi.

## Nilai yang direkomendasikan untuk PHP-FPM

```ini
file_uploads = On
upload_max_filesize = 12M
post_max_size = 52M
max_file_uploads = 6
memory_limit = 256M
max_execution_time = 60
max_input_time = 60
```

Alasan:
- 12M memberi ruang untuk satu gambar 10 MB + overhead multipart.
- 52M memberi ruang 4 × 10 MB + field form.
- `max_file_uploads=6` cukup untuk 4 gambar + avatar/banner.
- Konversi WebP dan gambar 40 MP membutuhkan memori lebih dari default 128M.

## Lokasi konfigurasi

Jangan mengubah `php.ini` CLI lalu menganggap FPM ikut berubah. Keduanya terpisah.

- Debian/Ubuntu PHP-FPM: `/etc/php/8.3/fpm/php.ini` dan pool di
  `/etc/php/8.3/fpm/pool.d/*.conf`.
- Konfigurasi per-site (disarankan): `php_admin_value[upload_max_filesize] = 12M`
  dan `php_admin_value[post_max_size] = 52M` di pool FPM.
- cPanel/shared hosting: menu **MultiPHP INI Editor**.
- CLI: `php -i | grep -E 'upload_max_filesize|post_max_size|max_file_uploads'`.

Setelah perubahan:

```bash
sudo systemctl reload php8.3-fpm
# atau
sudo systemctl reload php-fpm
```

## Verifikasi

```bash
php scripts/check-upload-limits.php
```

Jika web server memakai FPM dan CLI berbeda, buat `phpinfo()` sementara di
document root, akses lewat browser, lalu **hapus segera**. Jangan pernah
membiarkan `phpinfo()` terbuka di production.

## Yang belum diubah oleh aplikasi

File aplikasi hanya menyiapkan validasi dan pesan error. Perubahan nilai
`upload_max_filesize`/`post_max_size` **wajib dilakukan di server** dan belum
diterapkan oleh audit ini.
