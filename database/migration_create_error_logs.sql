-- Migration: Centralized error incident logs (Developer Error Center)
-- Safe additive migration for PaddockID (CodeIgniter 3 / MySQL).
-- Run manually when ready:
--   mysql -u <user> -p <db_name> < database/migration_create_error_logs.sql

CREATE TABLE IF NOT EXISTS `error_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `error_code` varchar(32) NOT NULL COMMENT 'Public incident code, e.g. PDK-7F3A91C2',
  `error_type` varchar(100) DEFAULT NULL COMMENT 'Category/type, e.g. database, api, upload, exception, php_error',
  `severity` varchar(20) NOT NULL DEFAULT 'error' COMMENT 'debug, info, warning, error, critical',
  `message` text DEFAULT NULL COMMENT 'Sanitized internal error summary (never shown to end users)',
  `exception_class` varchar(191) DEFAULT NULL,
  `file` varchar(500) DEFAULT NULL COMMENT 'Internal file path for developer investigation only',
  `line` int(11) DEFAULT NULL,
  `request_uri` varchar(2048) DEFAULT NULL,
  `request_method` varchar(16) DEFAULT NULL,
  `user_id` varchar(64) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(512) DEFAULT NULL,
  `context` longtext DEFAULT NULL COMMENT 'Sanitized JSON context; no secrets/sensitive request bodies',
  `status` enum('open','investigating','resolved','ignored') NOT NULL DEFAULT 'open',
  `resolution_notes` text DEFAULT NULL,
  `resolved_by` varchar(64) DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_error_logs_error_code` (`error_code`),
  KEY `idx_error_logs_created_at` (`created_at`),
  KEY `idx_error_logs_status` (`status`),
  KEY `idx_error_logs_error_type` (`error_type`),
  KEY `idx_error_logs_user_id` (`user_id`),
  KEY `idx_error_logs_severity` (`severity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
