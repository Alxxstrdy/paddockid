-- Migration: Direct Message (DM) tables
-- Run: mysql -u root -p db_paddockid < database/migration_create_dm_tables.sql

CREATE TABLE IF NOT EXISTS `dm_conversations` (
  `id_dm`       int(11) NOT NULL AUTO_INCREMENT,
  `user_id_a`   varchar(20) NOT NULL,
  `user_id_b`   varchar(20) NOT NULL,
  `last_read_a` datetime DEFAULT NULL,
  `last_read_b` datetime DEFAULT NULL,
  `created_at`  datetime NOT NULL,
  PRIMARY KEY (`id_dm`),
  UNIQUE KEY `uq_dm_pair` (`user_id_a`, `user_id_b`),
  KEY `idx_dm_user_a` (`user_id_a`),
  KEY `idx_dm_user_b` (`user_id_b`),
  CONSTRAINT `fk_dm_conv_a` FOREIGN KEY (`user_id_a`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `fk_dm_conv_b` FOREIGN KEY (`user_id_b`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `dm_messages` (
  `id_message` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_dm`      int(11) NOT NULL,
  `sender_id`  varchar(20) NOT NULL,
  `content`    text DEFAULT NULL,
  `image_url`  varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `deleted`    tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_message`),
  KEY `idx_dm_msg_conv_created` (`id_dm`, `created_at`),
  KEY `idx_dm_msg_sender` (`sender_id`),
  CONSTRAINT `fk_dm_msg_conv` FOREIGN KEY (`id_dm`) REFERENCES `dm_conversations` (`id_dm`) ON DELETE CASCADE,
  CONSTRAINT `fk_dm_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Notifikasi DM perlu menautkan ke percakapan
ALTER TABLE `notifications` ADD COLUMN `id_dm` int(11) DEFAULT NULL AFTER `id_comment`;