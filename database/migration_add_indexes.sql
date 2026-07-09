-- Migration: Add database indexes for performance
-- Run: mysql -u root -p db_paddockid < database/migration_add_indexes.sql

ALTER TABLE post_likes ADD INDEX idx_post_likes_id_post (id_post);
ALTER TABLE post_likes ADD INDEX idx_post_likes_user_id (user_id);
ALTER TABLE post_likes ADD INDEX idx_post_likes_created_at (created_at);

ALTER TABLE post_comments ADD INDEX idx_post_comments_id_post (id_post);
ALTER TABLE post_comments ADD INDEX idx_post_comments_user_id (user_id);
ALTER TABLE post_comments ADD INDEX idx_post_comments_parent_comment_id (parent_comment_id);
ALTER TABLE post_comments ADD INDEX idx_post_comments_created_at (created_at);

ALTER TABLE post_media ADD INDEX idx_post_media_id_post (id_post);

ALTER TABLE blocked_users ADD INDEX idx_blocked_users_id_user (id_user);
ALTER TABLE blocked_users ADD INDEX idx_blocked_users_id_blocked_user (id_blocked_user);

ALTER TABLE post ADD INDEX idx_post_username (username);
ALTER TABLE post ADD INDEX idx_post_id_user (id_user);
ALTER TABLE post ADD INDEX idx_post_created_at (created_at);
ALTER TABLE post ADD INDEX idx_post_id_category (id_category);

ALTER TABLE users ADD INDEX idx_users_username (username);
ALTER TABLE users ADD INDEX idx_users_id_team (id_team);

ALTER TABLE user_followers ADD INDEX idx_user_followers_id_user (id_user);
ALTER TABLE user_followers ADD INDEX idx_user_followers_id_following (id_following);
ALTER TABLE user_followers ADD INDEX idx_user_followers_created_at (created_at);

ALTER TABLE notification ADD INDEX idx_notification_id_user (id_user);
ALTER TABLE notification ADD INDEX idx_notification_created_at (created_at);
