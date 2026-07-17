CREATE TABLE `custom_ads` (
  `id_ad` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) NOT NULL,
  `target_url` varchar(500) NOT NULL,
  `position` enum('sidebar','feed','both') NOT NULL DEFAULT 'sidebar',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `start_date` datetime NOT NULL DEFAULT current_timestamp(),
  `end_date` datetime DEFAULT NULL,
  `click_count` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_ad`),
  KEY `idx_position_active` (`position`, `is_active`),
  KEY `idx_date_range` (`start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
