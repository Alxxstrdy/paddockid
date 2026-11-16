# Web Server Hardening — PaddockID

## 1. Rewrite & base URL

`BASE_URL` **wajib** diisi di `.env` production:

```env
CI_ENV=production
BASE_URL=https://paddockid.web.id/
ALLOWED_HOSTS=paddockid.web.id
```

Aplikasi tidak lagi memakai `HTTP_HOST` untuk membentuk `base_url`. Host header
palsu tidak akan mengubah link verifikasi email/reset password.

Jika aplikasi dipasang di subdirektori, `BASE_URL` harus memuat subdirektori:
`https://example.com/paddockid/`.

## 2. Apache

`.htaccess` di root dan setiap folder upload sudah diperbarui. Yang perlu
dipastikan di server:

```apache
AllowOverride All
```

Modul yang disarankan: `mod_rewrite`, `mod_headers`, `mod_expires`,
`mod_authz_core`. `php_flag engine off` dibungkus `<IfModule mod_php*.c>` agar
tidak menyebabkan 500 pada PHP-FPM.

Jika memakai Apache 2.2, blok `Require all denied` perlu diganti `Deny from all`
(sudah disediakan fallback di `.htaccess` upload).

## 3. Nginx (WAJIB jika bukan Apache)

`.htaccess` **tidak dibaca Nginx**. Tambahkan aturan berikut, lalu sesuaikan
root/path:

```nginx
# Jangan pernah mengeksekusi script dari folder upload
location ~* ^/(uploads|assets/uploads)/.*\.(php|php[0-9]?|phtml|phar|pl|py|cgi|asp|aspx|jsp|sh|bash)$ {
    deny all;
    return 403;
}

# File sensitif & folder internal
location ~* ^/(application|system|vendor|database|docs|tools|soketi)(/|$) {
    deny all;
    return 404;
}
location ~ /\.(env|git) {
    deny all;
    return 404;
}

# Header untuk upload/SVG
location ~* ^/(uploads|assets/uploads)/ {
    add_header X-Content-Type-Options "nosniff" always;
    location ~* \.svg$ {
        add_header Content-Security-Policy "default-src 'none'; style-src 'unsafe-inline'; img-src data:" always;
    }
}

# PHP hanya untuk index.php
location ~ \.php$ {
    include fastcgi_params;
    fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}
```

Pastikan `client_max_body_size` minimal `52m` agar sesuai batas aplikasi.

## 4. Permission folder

- Folder upload: dimiliki user web server, **tanpa** bit eksekusi script.
- Contoh aman: directory `755`, file `644`, owner `www-data` (sesuaikan).
- `application/cache` dan `application/logs` harus writable oleh PHP, tetapi
  tidak boleh dapat diakses langsung dari web (sudah diblokir `.htaccess`).

Perintah pemeriksaan (read-only):

```bash
namei -l uploads/posts
find uploads assets/uploads -type f -perm -111 -print
```

## 5. CDN

`UPLOAD_BASE_PATH` menentukan lokasi fisik penyimpanan. Folder origin CDN harus:
- writable oleh PHP,
- **tidak** mengeksekusi PHP,
- disajikan dengan `X-Content-Type-Options: nosniff`.

Konfigurasi CDN final belum diterapkan dan harus diverifikasi di server baru.

## 6. Yang belum bisa diverifikasi dari repo

- Web server yang benar-benar dipakai (Apache/Nginx/LiteSpeed).
- `AllowOverride`, modul, dan pool PHP-FPM.
- Permission/owner folder produksi.
- Konfigurasi CDN dan cache.
