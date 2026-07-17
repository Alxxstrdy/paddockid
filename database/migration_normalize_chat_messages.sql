-- Migration: Remove username & avatar from chat_messages (normalize with users table)
-- Run: mysql -u root -p db_paddockid < database/migration_normalize_chat_messages.sql

ALTER TABLE `chat_messages`
    DROP COLUMN `username`,
    DROP COLUMN `avatar`;
