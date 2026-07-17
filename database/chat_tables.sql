CREATE TABLE IF NOT EXISTS `chat_rooms` (
  `id_room`      int(11) AUTO_INCREMENT PRIMARY KEY,
  `race_round`   int(11) NOT NULL,
  `race_name`    varchar(150) NOT NULL,
  `session_name` varchar(50) NOT NULL,
  `slug`         varchar(150) NOT NULL UNIQUE,
  `opens_at`     datetime NOT NULL,
  `closes_at`    datetime NOT NULL,
  `created_at`   timestamp DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id_message`   bigint(20) AUTO_INCREMENT PRIMARY KEY,
  `id_room`      int(11) NOT NULL,
  `user_id`      varchar(20) NOT NULL,
  `content`      text NOT NULL,
  `created_at`   timestamp DEFAULT current_timestamp(),
  `deleted`      tinyint(1) DEFAULT 0,
  FOREIGN KEY (`id_room`) REFERENCES `chat_rooms`(`id_room`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id_user`) ON DELETE CASCADE,
  INDEX `idx_room_created` (`id_room`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
