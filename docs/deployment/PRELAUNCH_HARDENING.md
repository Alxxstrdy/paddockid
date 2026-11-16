# Pre-Launch Hardening — PaddockID (16 Nov 2026)

Dokumen ini merangkum perubahan kode dan hal yang **masih harus dilakukan di
server**. Jangan menganggap item server selesai hanya karena file repo berubah.

## 1. Environment

- `.env` kini dibaca **sebelum** `ENVIRONMENT` ditentukan, sehingga `CI_ENV`
  benar-benar dipakai.
- Nilai `CI_ENV` divalidasi; nilai asing jatuh ke `production`.
- Production memaksa `display_errors=0`, `display_startup_errors=0`,
  `html_errors=0`, dan `log_errors=1`.
- **Manual:** isi `.env` production dengan `CI_ENV=production`.

## 2. Base URL

- `BASE_URL` dari `.env` dipakai jika valid (http/https + host).
- `ALLOWED_HOSTS` opsional untuk membatasi host.
- Tidak ada lagi `HTTP_HOST`/`X-Forwarded-Proto` untuk membentuk base URL.
- **Manual:** isi `BASE_URL=https://domain-resmi/` dan `ALLOWED_HOSTS`.

## 3. Upload

- Validasi MIME asli (`getimagesize` + `finfo`), ekstensi diturunkan dari MIME.
- Nama file acak (`encrypt_name` / `uniqid`).
- Batas **6000 px per sisi** dan **40 MP total** sebelum decode.
- GIF animasi dibatasi 300 frame.
- WebP conversion tetap berjalan.
- `.htaccess` baru/terbarui di: `uploads`, `posts`, `avatars`, `banners`, `ads`,
  `dm_images`, `teams`, `borders`, `css`, `donate`, `profile`, `Flag`,
  `teams_backup`, `teams_backup_p1`, `assets/uploads`, `assets/uploads/profile`.
- SVG yang disajikan publik diberi CSP `default-src 'none'`.
- **Manual:** jika server Nginx, terapkan aturan di `WEB_SERVER_HARDENING.md`.

## 4. Bug fungsional

- `/shop` sekarang route ke `borders/shop` (bukan 404).
- DM delete = "hapus untuk saya" (`deleted_by_a`/`deleted_by_b`).
- Guest `load_more_posts` dibatasi 5 post di server.
- Search: perbandingan owner memakai integer, Edit/Delete muncul untuk pemilik.
- `Profile::edit_profile` dibatasi 20 perubahan/jam per user.

## 5. Admin & moderasi

- Moderasi otomatis default **nonaktif**; `monitor_only` tetap `TRUE`.
  Dapat diaktifkan lewat `MODERATION_ENABLED=true` setelah service siap.
- Hasil moderasi masih berupa log file, belum ada UI review admin.
- 2FA admin belum diterapkan; lihat `ADMIN_SECURITY.md`.

## 6. Backup

- `scripts/backup.sh` dan `BACKUP_RESTORE.md` tersedia.
- **Manual:** pasang cron, simpan offsite, dan uji restore di lingkungan
  terpisah.

## 7. Database

- Migration: `database/migrations/2026_11_16_hardening.sql`
  - `dm_conversations.deleted_by_a`, `deleted_by_b`
  - index `login_attempts(ip_address, attempted_at)` dan
    `login_attempts(identity, attempted_at)`
- Backup DB dibuat sebelum migration di `/tmp/opencode/`.

## 8. Yang belum diselesaikan

- Konfigurasi PHP-FPM (`upload_max_filesize`, `post_max_size`) — server.
- Konfigurasi Nginx/Apache produksi dan permission folder — server.
- 2FA admin — butuh keputusan produk.
- UI review moderasi — belum ada data/service.
- FULLTEXT search & refactor N+1 — perubahan lebih besar, ditunda.
- Uji beban dan uji restore produksi — belum dijalankan.
