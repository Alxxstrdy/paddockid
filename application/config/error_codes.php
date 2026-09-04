<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Registri Kode Error PaddockID
|--------------------------------------------------------------------------
| Sumber kebenaran untuk kode error. Setiap kode memetakan: kategori,
| judul, penyebab, dan langkah perbaikan.
|
| Format kode: KATEGORI-NNNN
|   PDB  Database   PUP Upload    PPS Pusher/Chat  PFT Fonnte
|   PAU  Auth       PRC Race API  PCG Config/Secret  PGN General
|
| Dipakai oleh:
|   - application/helpers/error_helper.php (pemetaan + logging + render)
|   - halaman admin /admin/error_codes (katalog perbaikan)
|   - application/views/errors/html/error_coded.php (tampilan dev)
*/

$config['error_codes'] = array(

    // ======================= DATABASE =======================
    'PDB-1001' => array(
        'category' => 'Database',
        'title'    => 'Akses database ditolak (Access denied)',
        'cause'    => 'Username atau password database salah, atau akun DB tidak memiliki hak akses.',
        'fix'      => 'Periksa nilai DB_USER dan DB_PASS pada file .env dan pastikan akun MySQL paddockid_admin aktif serta punya privilege ke db_paddockid.',
    ),
    'PDB-1002' => array(
        'category' => 'Database',
        'title'    => 'Server database tidak dapat dijangkau',
        'cause'    => 'Service MySQL mati, host salah, port tidak terbuka, atau koneksi terputus di tengah operasi.',
        'fix'      => 'Pastikan service MySQL berjalan (systemctl status mysql), cek nilai DB_HOST/DB_PORT di .env, dan periksa konektivitas jaringan ke server DB.',
    ),
    'PDB-1003' => array(
        'category' => 'Database',
        'title'    => 'Database tidak ditemukan (Unknown database)',
        'cause'    => 'Nama database pada .env tidak sesuai dengan database yang benar-benar ada di server MySQL.',
        'fix'      => 'Periksa nilai DB_NAME di .env, pastikan database db_paddockid sudah dibuat, dan user punya akses ke database tersebut.',
    ),
    'PDB-1004' => array(
        'category' => 'Database',
        'title'    => 'Tabel tidak ditemukan (Table does not exist)',
        'cause'    => 'Tabel yang dipakai belum dibuat di database, atau nama tabel berubah.',
        'fix'      => 'Jalankan file SQL/migration terkait (database/search_history.sql, dll) atau sinkronkan skema tabel dengan kode terbaru.',
    ),
    'PDB-1005' => array(
        'category' => 'Database',
        'title'    => 'Kolom tidak dikenal (Unknown column)',
        'cause'    => 'Skema database tidak sinkron dengan kode: kolom yang dipakai query belum ada di tabel.',
        'fix'      => 'Bandingkan struktur tabel di database dengan model/query terbaru, lalu tambahkan kolom atau sesuaikan query.',
    ),
    'PDB-1006' => array(
        'category' => 'Database',
        'title'    => 'Data duplikat (Duplicate entry)',
        'cause'    => 'Pelanggaran unique/primary key saat insert atau update.',
        'fix'      => 'Tangani di sisi aplikasi agar mendeteksi duplikat lebih dulu (mis. cek is_username_exists / is_email_exists) atau terima bahwa operasi ditolak database.',
    ),
    'PDB-1007' => array(
        'category' => 'Database',
        'title'    => 'Syntax error pada query',
        'cause'    => 'Query SQL yang dihasilkan kode tidak valid (salah sintaks, nama tabel/kolom salah).',
        'fix'      => 'Buka query yang dicatat di log, uji manual di klien MySQL, lalu perbaiki query/model yang bersangkutan.',
    ),
    'PDB-1008' => array(
        'category' => 'Database',
        'title'    => 'Pelanggaran foreign key constraint',
        'cause'    => 'Data yang direferensikan (parent) tidak ada, atau ada data anak yang menghalangi hapus/update parent.',
        'fix'      => 'Periksa data referensi pada tabel terkait, pastikan relasi konsisten sebelum insert/update/delete.',
    ),
    'PDB-1999' => array(
        'category' => 'Database',
        'title'    => 'Error database lainnya',
        'cause'    => 'Error database yang tidak masuk kategori di atas (kode errno lain).',
        'fix'      => 'Baca detail errno dan pesan lengkap di log (halaman /admin/errors), lalu telusuri query yang menyebabkannya.',
    ),

    // ======================= AUTH =======================
    'PAU-2001' => array(
        'category' => 'Auth',
        'title'    => 'Kegagalan proses password (hash/verify)',
        'cause'    => 'Operasi password_hash/password_verify gagal, biasanya karena parameter password tidak valid.',
        'fix'      => 'Pastikan format password mengikuti aturan aplikasi (min 8 karakter, huruf besar/kecil, angka, simbol) dan data hash di DB tidak rusak.',
    ),
    'PAU-2002' => array(
        'category' => 'Auth',
        'title'    => 'Google OAuth gagal',
        'cause'    => 'Callback Google menolak token, atau GOOGLE_CLIENT_ID/GOOGLE_CLIENT_SECRET tidak terkonfigurasi di .env.',
        'fix'      => 'Periksa nilai GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET di .env serta Redirect URI di Google Cloud Console.',
    ),
    'PAU-2003' => array(
        'category' => 'Auth',
        'title'    => 'Email reset password gagal terkirim',
        'cause'    => 'Konfigurasi SMTP salah, atau service email menolak pengiriman.',
        'fix'      => 'Periksa konfigurasi email (smtp_host/user/pass) di file application/config/email.php dan pastikan port SMTP terbuka.',
    ),

    // ======================= UPLOAD =======================
    'PUP-3001' => array(
        'category' => 'Upload',
        'title'    => 'File yang diunggah bukan gambar',
        'cause'    => 'Isi file tidak terdeteksi sebagai gambar valid (jpg/png/gif/webp) saat validasi MIME.',
        'fix'      => 'Minta pengguna mengunggah file gambar yang valid. Jika berkas legitimate ditolak, periksa deteksi getimagesize/finfo pada server.',
    ),
    'PUP-3002' => array(
        'category' => 'Upload',
        'title'    => 'Ukuran file melebihi batas',
        'cause'    => 'File melebihi batas ukuran yang ditentukan (mis. 10 MB untuk post, 2 MB untuk banner).',
        'fix'      => 'Tambah batas upload di kode (Post.php / Profile.php / Admin.php) dan/atau naikkan upload_max_filesize/post_max_size di php.ini.',
    ),
    'PUP-3003' => array(
        'category' => 'Upload',
        'title'    => 'Gagal menyimpan file upload',
        'cause'    => 'Folder tujuan tidak bisa ditulis, atau move_uploaded_file gagal.',
        'fix'      => 'Pastikan folder uploads/posts, assets/uploads/profile, dan uploads/banners dapat ditulis (chmod/permission owner www-data).',
    ),

    // ======================= PUSHER / CHAT =======================
    'PPS-4001' => array(
        'category' => 'Pusher/Chat',
        'title'    => 'Pusher gagal memicu event (trigger)',
        'cause'    => 'Kredensial Pusher salah, kuota/channel bermasalah, atau koneksi ke Pusher gagal.',
        'fix'      => 'Periksa PUSHER_APP_ID/KEY/SECRET/CLUSTER di .env dan pastikan sesuai dashboard Pusher. Verifikasi layanan berjalan dan kuota mencukupi.',
    ),
    'PPS-4002' => array(
        'category' => 'Pusher/Chat',
        'title'    => 'Pusher auth gagal',
        'cause'    => 'Signature auth Pusher tidak valid (biasanya PUSHER_SECRET salah atau channel tidak diizinkan).',
        'fix'      => 'Cocokkan PUSHER_SECRET di .env dengan dashboard Pusher, dan pastikan endpoint auth memvalidasi pengguna dengan benar.',
    ),

    // ======================= FONNTE =======================
    'PFT-5001' => array(
        'category' => 'Fonnte',
        'title'    => 'Token API FONNTE tidak terkonfigurasi',
        'cause'    => 'Nilai FONNTE_API_TOKEN kosong/tidak ada di file .env.',
        'fix'      => 'Isi FONNTE_API_TOKEN di .env dengan token dari dashboard FONNTE.',
    ),

    // ======================= RACE API =======================
    'PRC-6001' => array(
        'category' => 'Race API',
        'title'    => 'Gagal mengambil data dari API F1 (jolpi.ca)',
        'cause'    => 'API tidak terjangkau (jaringan/timeout) atau endpoint berubah.',
        'fix'      => 'Periksa koneksi internet server dan status layanan api.jolpi.ca. Jika cache lama masih ada, aplikasi memakai cache tersebut.',
    ),
    'PRC-6002' => array(
        'category' => 'Race API',
        'title'    => 'Cache race tidak dapat ditulis',
        'cause'    => 'Folder application/cache tidak dapat ditulis oleh proses PHP.',
        'fix'      => 'Pastikan folder application/cache dapat ditulis (owner www-data) untuk file cache JSON race.',
    ),

    // ======================= CONFIG / SECRET =======================
    'PCG-7001' => array(
        'category' => 'Config/Secret',
        'title'    => 'ENCRYPTION_KEY tidak terkonfigurasi',
        'cause'    => 'Nilai ENCRYPTION_KEY kosong di .env sehingga session/cookie tidak bisa dienkripsi dengan benar.',
        'fix'      => 'Isi ENCRYPTION_KEY di .env dengan kunci 32 karakter (hex 64 karakter). Jangan gunakan kunci bawaan dari git.',
    ),
    'PCG-7002' => array(
        'category' => 'Config/Secret',
        'title'    => 'Kredensial Google tidak terkonfigurasi',
        'cause'    => 'GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET kosong di .env.',
        'fix'      => 'Isi kedua nilai tersebut di .env sesuai Google Cloud Console, atau nonaktifkan tombol login Google.',
    ),

    // ======================= GENERAL =======================
    'PGN-9001' => array(
        'category' => 'General',
        'title'    => 'Exception tidak tertangkap',
        'cause'    => 'Terjadi exception yang tidak ditangani try/catch di alur aplikasi.',
        'fix'      => 'Baca detail exception (file, baris, stack trace) di log, lalu perbaiki atau tambahkan penanganan di kode terkait.',
    ),
    'PGN-9002' => array(
        'category' => 'General',
        'title'    => 'Error PHP (warning/notice/fatal)',
        'cause'    => 'Kode PHP menghasilkan error pada tingkat tertentu.',
        'fix'      => 'Baca pesan error di log untuk menemukan file dan baris penyebab, lalu perbaiki kode.',
    ),
    'PGN-9003' => array(
        'category' => 'General',
        'title'    => 'Error aplikasi umum',
        'cause'    => 'Aplikasi memanggil show_error() secara manual karena kondisi tak terduga.',
        'fix'      => 'Periksa pesan lengkap di log untuk menemukan titik pemicu, lalu sesuaikan alur kode.',
    ),
    'PGN-9999' => array(
        'category' => 'General',
        'title'    => 'Error tak dikenal',
        'cause'    => 'Error yang tidak dapat dipetakan ke kategori yang ada.',
        'fix'      => 'Baca detail lengkap di log (halaman /admin/errors), lalu laporkan/mapping kode baru bila perlu.',
    ),
);
