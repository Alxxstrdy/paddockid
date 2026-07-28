-- Add coins column to users table
ALTER TABLE `users` ADD COLUMN `coins` INT(11) NOT NULL DEFAULT 5000 AFTER `last_activity`;

-- Add description column to borders table (if not exists)
SET @col_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'db_paddockid'
      AND TABLE_NAME = 'borders'
      AND COLUMN_NAME = 'description'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `borders` ADD COLUMN `description` TEXT AFTER `price`',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
