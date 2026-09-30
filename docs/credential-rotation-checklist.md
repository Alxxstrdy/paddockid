# Checklist Rotasi Kredensial — PaddockID

Status: **belum dikerjakan.** Dokumen ini hasil audit 30 Sep 2026.

## Kenapa ini penting

File `.env` pernah terbaca publik di `https://paddockid.web.id/.env` (HTTP 200,
16 kunci terbaca). Nginx dan PHP-FPM sama-sama berjalan sebagai `www-data`,
jadi `chmod` **tidak** mencegah file itu dilayani — satu-satunya penahan adalah
`deny` di nginx.

Setelah `deny` terpasang, isi `.env` **tetap harus diasumsikan bocor**:
sudah bisa di-cache proxy, di-index mesin pencari, atau di-salin siapa pun.
Menutup file tidak membuat kredensial lama kembali aman.

> Tidak ada nilai rahasia di dokumen ini. Semua nilai diambil dari `.env` saat
> eksekusi.

---

## Identitas (dari `.env`, bukan rahasia)

| Kunci | Nilai |
|---|---|
| `DB_HOST` | `localhost` |
| `DB_USER` | `paddockid_admin` |
| `DB_NAME` | `db_paddockid` |
| Versi DB | MariaDB 10.11.14 |

`DB_USER` bukan root — kebocoran `.env` tidak langsung memberi akses root MySQL.

---

## Urutan eksekusi

Rotasi tidak bisa atomik; satu salah langkah = situs tumbang. Urut dari yang
paling murah dan paling berbahaya kalau dibiarkan, ke yang paling mengganggu user.

| # | Rahasia | Downtime | Dampak ke user |
|---|---|---|---|
| 1 | `DB_PASS` | nol | tidak ada |
| 2 | `FONNTE_API_TOKEN` | nol | OTP WhatsApp mati sementara |
| 3 | `SMTP_PASS` | nol | email notifikasi mati sementara |
| 4 | `PUSHER_SECRET` | nol | notifikasi real-time delay |
| 5 | `ENCRYPTION_KEY` | ~2 menit | **semua user logout** |

`ENCRYPTION_KEY` di akhir karena paling merepotkan. Kalau justru curiga ada sesi
admin yang sudah dipalsukan, balik urutannya — invalidate forging lebih penting
daripada kenyamanan.

---

## 1. Password database

```bash
openssl rand -base64 24 | tr -d '/+=' | head -c 28
```

```sql
-- sebagai root MariaDB
SELECT user, host FROM mysql.user WHERE user = 'paddockid_admin';
-- CATAT semua host yang muncul; rotasi SEMUA, bukan hanya localhost

ALTER USER 'paddockid_admin'@'localhost' IDENTIFIED BY 'PASSWORD_BARU';
FLUSH PRIVILEGES;
```

Tulis ke `.env`: `DB_PASS=PASSWORD_BARU`, lalu `sudo systemctl reload php8.3-fpm`.

**Verifikasi**
```bash
mysql -u paddockid_admin -p db_paddockid -e "SELECT 1;"
grep "PGN-9002\|PGN-9003" application/logs/log-$(date +%F).php   # harus kosong
```

**Wajib dicek — risiko eskalasi**
```sql
SHOW GRANTS FOR 'paddockid_admin'@'localhost';
```
Kalau muncul `ALL PRIVILEGES ON *.*`, `FILE`, atau `GRANT OPTION`, risiko jauh
lebih besar dari asumsi audit ini.

## 2–4. Token pihak ketiga

| Rahasia | Ambil dari | Config aplikasi |
|---|---|---|
| `FONNTE_API_TOKEN` | dashboard Fonnte → token device | `application/helpers/fonnte_helper.php` |
| `SMTP_PASS` | panel VPS / provider email | `application/config/email.php` |
| `PUSHER_SECRET` | dashboard Pusher → Settings → Keys | `application/config/pusher.php` |

Untuk semuanya: generate baru → tulis `.env` → `chmod 640 .env` →
`sudo systemctl reload php8.3-fpm`.

**Verifikasi** — tes fitur yang memakai masing-masing:
OTP (Fonnte), email notifikasi (SMTP), notifikasi real-time (Pusher).

## 5. ENCRYPTION_KEY

```bash
openssl rand -hex 32
```

Tulis ke `.env` → reload FPM.

Harapan: sesi dan cookie lama langsung invalid (signature tidak cocok), semua
user diminta login ulang.

**Verifikasi:** login ulang di satu browser, pastikan post / comment / DM normal.

---

## Setelah semua selesai

`.env` **harus tetap bisa dibaca `www-data`**, karena `index.php` membacanya
sebagai user FPM. Jangan pernah `chown root:root` dengan mode 640 — itu membuat
`www-data` kehilangan akses dan seluruh situs langsung tumbang.

```bash
sudo chown root:www-data /var/www/html/paddockid/.env
sudo chmod 640 /var/www/html/paddockid/.env

# cek ulang: www-data harus masih bisa baca
sudo -u www-data test -r /var/www/html/paddockid/.env \
  && echo "OK: www-data bisa baca" \
  || echo "BAHAYA: situs akan tumbang"
```

Baris `test -r` itu wajib dijalankan. Kalau gagal, kembalikan ke
`chown eyb:www-data` + `chmod 640`.

Lalu catat password baru di password manager — jangan pernah di `.env` yang
sempat bocor tanpa dirotasi.

## Ringkasan order minus

Tidak ada nilai rahasia yang tercatat di dokumen ini. Semua nilai diambil dari
`.env` pada saat eksekusi.
