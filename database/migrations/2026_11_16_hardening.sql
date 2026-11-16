-- PaddockID pre-launch hardening (2026-11-16)
-- Aman dijalankan pada data yang sudah ada. Tidak menghapus baris apa pun.

-- 1) "Hapus untuk saya" pada DM: status per peserta.
ALTER TABLE `dm_conversations`
  ADD COLUMN `deleted_by_a` tinyint(1) NOT NULL DEFAULT 0 AFTER `last_read_b`,
  ADD COLUMN `deleted_by_b` tinyint(1) NOT NULL DEFAULT 0 AFTER `deleted_by_a`;

-- 2) Index untuk query rate-limit login (count_failed_attempts).
ALTER TABLE `login_attempts`
  ADD KEY `idx_login_ip_time` (`ip_address`, `attempted_at`),
  ADD KEY `idx_login_identity_time` (`identity`, `attempted_at`);
