# Backup & Restore — PaddockID

## Backup otomatis

Script: `scripts/backup.sh`

```bash
chmod +x scripts/backup.sh
BACKUP_DIR=/var/backups/paddockid RETENTION_DAYS=14 ./scripts/backup.sh
```

Isi backup per timestamp:
- `database.sql.gz` — dump MySQL/MariaDB (`--single-transaction`)
- `uploads.tar.gz` — `uploads/` dan `assets/uploads/`
- `env.backup` — salinan `.env` (sensitif, mode 600)
- `MANIFEST.txt`

Script **tidak** menghapus database dan hanya merotasi folder backup lama di
`BACKUP_DIR`.

Jadwalkan harian (contoh cron, sesuaikan user & path):

```cron
15 2 * * * cd /var/www/html/paddockid && BACKUP_DIR=/var/backups/paddockid ./scripts/backup.sh >> /var/log/paddockid-backup.log 2>&1
```

Simpan salinan ke lokasi lain (object storage/offsite). Backup di server yang
sama bukan disaster recovery.

## Restore (JANGAN dijalankan di production tanpa persetujuan)

Latih restore di lingkungan terpisah:

```bash
# 1. Siapkan database kosong
mysql -u root -p -e "CREATE DATABASE paddockid_restore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Restore dump
gunzip -c database.sql.gz | mysql -u root -p paddockid_restore

# 3. Ekstrak uploads ke direktori uji
mkdir -p /tmp/paddockid-restore
tar -xzf uploads.tar.gz -C /tmp/paddockid-restore

# 4. Arahkan .env uji ke DB dan folder upload uji
#    BASE_URL=http://localhost:8080/
#    DB_NAME=paddockid_restore
#    UPLOAD_BASE_PATH=/tmp/paddockid-restore/

# 5. Jalankan aplikasi, uji login, feed, upload, DM.
```

## Verifikasi backup

Minimal bulanan:
1. Cek ukuran dump tidak nol dan `gzip -t` lolos.
2. Restore ke DB uji.
3. Bandingkan jumlah baris tabel utama (`users`, `posts`, `post_media`,
   `dm_messages`).
4. Buka beberapa gambar dari arsip upload.

## Yang belum tersedia

- Backup otomatis / cron belum terpasang di server.
- Belum ada uji restore terjadwal.
- Belum ada replikasi/offsite.
