-- Perbaikan: notifikasi DM (Dm.php, Notification_model) gagal tersimpan
-- karena tipe 'dm' tidak ada di ENUM notifications.type.
-- Run: mysql -u root -p db_paddockid < database/migration_add_notif_dm_enum.sql

ALTER TABLE `notifications`
    MODIFY `type` ENUM('like','comment','follow','reply','admin','dm') NOT NULL;