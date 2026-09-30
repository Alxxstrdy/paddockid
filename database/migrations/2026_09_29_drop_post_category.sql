-- Hapus fitur kategori post.
-- Drops: tabel post_category + kolom posts.post_category (beserta FK & index-nya).
-- Backup: /tmp/opencode/paddockid-backup/posts_post_category_20260929_132218.sql

ALTER TABLE `posts` DROP FOREIGN KEY `posts_ibfk_2`;
ALTER TABLE `posts` DROP COLUMN `post_category`;
DROP TABLE IF EXISTS `post_category`;
