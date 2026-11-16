# Admin Security Review — PaddockID

## Kondisi saat ini

- Panel admin memakai session yang sama dengan user biasa
  (`user_logged_in`) dan memeriksa `role === 'admin'` di constructor
  `Admin::_check_admin()`.
- Semua endpoint admin melewati pemeriksaan role tersebut.
- CSRF global aktif (`csrf_protection = TRUE`), cookie `HttpOnly` + `SameSite=Lax`.
- Login admin memakai `password_verify()` dan rate-limit login yang sama.
- Ada `admin_activity_logs` untuk audit aksi admin.

## Risiko

1. **Tidak ada 2FA.** Satu password admin yang bocor = akses penuh.
2. **Session admin = session user.** Jika cookie session dicuri, penyerang
   langsung mendapat role admin.
3. **Tidak ada idle timeout khusus admin.**
4. **Tidak ada pembatasan IP / perangkat admin.**
5. **Tidak ada notifikasi login admin baru** ke email/owner.
6. **Tidak ada halaman review hasil moderasi otomatis** (moderasi masih off).

## Rekomendasi 2FA (urutan implementasi aman)

1. Tambah kolom `users.totp_secret` (varchar 64, nullable) dan
   `users.totp_enabled` (tinyint default 0). Jangan menaruh secret di session.
2. Gunakan library TOTP yang sudah teruji (mis. `spomky-labs/otphp`) lewat
   Composer; jangan menulis algoritma HMAC sendiri.
3. Alur enrollment:
   - admin login dengan password,
   - jika `role=admin` dan `totp_enabled=0`, paksa halaman setup,
   - tampilkan QR + secret sekali, verifikasi kode 6 digit,
   - simpan secret terenkripsi (gunakan `ENCRYPTION_KEY`) dan aktifkan.
4. Alur login:
   - setelah password benar, jika `totp_enabled=1`, jangan langsung set
     `user_logged_in`; simpan status sementara `pending_2fa` (5 menit),
   - verifikasi kode, baru regenerasi session dan set `user_logged_in`.
5. Sediakan recovery code sekali pakai (hash di DB).
6. Wajibkan 2FA hanya untuk `role=admin`, jangan mengunci user biasa.

## Hardening tambahan yang disarankan

- Idle timeout admin 30–60 menit (session key terpisah).
- Regenerasi session setelah perubahan role.
- Batasi `/admin` dengan IP allowlist atau Cloudflare Access.
- Kirim email peringatan saat login admin dari IP/perangkat baru.
- Simpan `admin_activity_logs` minimal 90 hari.
- Pisahkan akun admin dari akun komunitas.

## Yang belum diterapkan

2FA **belum diimplementasikan** pada audit ini karena membutuhkan keputusan
produk (library, alur enrollment, recovery code) dan pengujian login admin
secara menyeluruh. Jangan mengklaim admin sudah 2FA.
