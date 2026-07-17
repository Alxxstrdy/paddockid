<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2026-07-12 14:56:07 --> $config['composer_autoload'] is set to TRUE but /var/www/html/paddockid/application/vendor/autoload.php was not found.
DEBUG - 2026-07-12 14:56:07 --> UTF-8 Support Enabled
DEBUG - 2026-07-12 14:56:07 --> No URI present. Default controller set.
DEBUG - 2026-07-12 14:56:07 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2026-07-12 14:56:08 --> Session: Initialization under CLI aborted.
DEBUG - 2026-07-12 21:56:08 --> Config file loaded: /var/www/html/paddockid/application/config/ads.php
ERROR - 2026-07-12 21:56:08 --> Query error: Unknown column 'u.last_activity' in 'SELECT' - Invalid query: SELECT p.id_post, p.user_id, p.post_category, u.username, u.display_name, u.avatar, u.verified, u.team_id, u.last_activity, t.team_name, t.team_logo, t.team_color, b.image_url as border, p.content, p.created_at, pc.category_name as category, (SELECT COUNT(*) FROM post_likes WHERE id_post = p.id_post) as likes_count, (SELECT COUNT(*) FROM post_comments WHERE id_post = p.id_post) as comments_count, (SELECT COUNT(*) FROM post_likes WHERE id_post = p.id_post AND user_id = 0) > 0 as is_liked, (SELECT GROUP_CONCAT(file_url ORDER BY id ASC SEPARATOR ', ') FROM post_media WHERE id_post = p.id_post) as file_url
FROM `posts` `p`
JOIN `users` `u` ON `p`.`user_id` = `u`.`id_user`
LEFT JOIN `borders` `b` ON `u`.`border_active` = `b`.`id_border`
LEFT JOIN `team` `t` ON `u`.`team_id` = `t`.`team_id`
LEFT JOIN `post_category` `pc` ON `p`.`post_category` = `pc`.`id_category`
WHERE (`p`.`deleted` IS NULL OR `p`.`deleted` =0)
ORDER BY 
            (CASE WHEN p.user_id IN ('')
                THEN 10000000000 + UNIX_TIMESTAMP(p.created_at)
                ELSE likes_count * 86400 + comments_count * 172800 + UNIX_TIMESTAMP(p.created_at)
            END) DESC
        
 LIMIT 5
ERROR - 2026-07-12 21:56:08 --> Severity: error --> Exception: Call to a member function result_array() on false /var/www/html/paddockid/application/models/Post_model.php 236
ERROR - 2026-07-12 14:57:34 --> $config['composer_autoload'] is set to TRUE but /var/www/html/paddockid/application/vendor/autoload.php was not found.
DEBUG - 2026-07-12 14:57:34 --> UTF-8 Support Enabled
DEBUG - 2026-07-12 14:57:34 --> No URI present. Default controller set.
DEBUG - 2026-07-12 14:57:34 --> Global POST, GET and COOKIE data sanitized
ERROR - 2026-07-12 14:57:34 --> Session: Configured save path '/var/www/html/paddockid/application/cache/sessions' is not writable by the PHP process.
ERROR - 2026-07-12 14:57:34 --> Severity: Warning --> session_start(): Failed to initialize storage module: user (path: /var/www/html/paddockid/application/cache/sessions) /var/www/html/paddockid/system/libraries/Session/Session.php 137
DEBUG - 2026-07-12 21:57:34 --> Config file loaded: /var/www/html/paddockid/application/config/ads.php
ERROR - 2026-07-12 21:57:34 --> Query error: Unknown column 'u.last_activity' in 'SELECT' - Invalid query: SELECT p.id_post, p.user_id, p.post_category, u.username, u.display_name, u.avatar, u.verified, u.team_id, u.last_activity, t.team_name, t.team_logo, t.team_color, b.image_url as border, p.content, p.created_at, pc.category_name as category, (SELECT COUNT(*) FROM post_likes WHERE id_post = p.id_post) as likes_count, (SELECT COUNT(*) FROM post_comments WHERE id_post = p.id_post) as comments_count, (SELECT COUNT(*) FROM post_likes WHERE id_post = p.id_post AND user_id = 0) > 0 as is_liked, (SELECT GROUP_CONCAT(file_url ORDER BY id ASC SEPARATOR ', ') FROM post_media WHERE id_post = p.id_post) as file_url
FROM `posts` `p`
JOIN `users` `u` ON `p`.`user_id` = `u`.`id_user`
LEFT JOIN `borders` `b` ON `u`.`border_active` = `b`.`id_border`
LEFT JOIN `team` `t` ON `u`.`team_id` = `t`.`team_id`
LEFT JOIN `post_category` `pc` ON `p`.`post_category` = `pc`.`id_category`
WHERE (`p`.`deleted` IS NULL OR `p`.`deleted` =0)
ORDER BY 
            (CASE WHEN p.user_id IN ('')
                THEN 10000000000 + UNIX_TIMESTAMP(p.created_at)
                ELSE likes_count * 86400 + comments_count * 172800 + UNIX_TIMESTAMP(p.created_at)
            END) DESC
        
 LIMIT 5
ERROR - 2026-07-12 21:57:34 --> Severity: error --> Exception: Call to a member function result_array() on false /var/www/html/paddockid/application/models/Post_model.php 236
ERROR - 2026-07-12 14:58:47 --> $config['composer_autoload'] is set to TRUE but /var/www/html/paddockid/application/vendor/autoload.php was not found.
DEBUG - 2026-07-12 14:58:47 --> UTF-8 Support Enabled
DEBUG - 2026-07-12 14:58:47 --> No URI present. Default controller set.
DEBUG - 2026-07-12 14:58:47 --> Global POST, GET and COOKIE data sanitized
ERROR - 2026-07-12 14:58:47 --> Session: Configured save path '/var/www/html/paddockid/application/cache/sessions' is not writable by the PHP process.
ERROR - 2026-07-12 14:58:47 --> Severity: Warning --> session_start(): Failed to initialize storage module: user (path: /var/www/html/paddockid/application/cache/sessions) /var/www/html/paddockid/system/libraries/Session/Session.php 137
DEBUG - 2026-07-12 21:58:47 --> Config file loaded: /var/www/html/paddockid/application/config/ads.php
ERROR - 2026-07-12 21:58:47 --> Query error: Unknown column 'u.last_activity' in 'SELECT' - Invalid query: SELECT p.id_post, p.user_id, p.post_category, u.username, u.display_name, u.avatar, u.verified, u.team_id, u.last_activity, t.team_name, t.team_logo, t.team_color, b.image_url as border, p.content, p.created_at, pc.category_name as category, (SELECT COUNT(*) FROM post_likes WHERE id_post = p.id_post) as likes_count, (SELECT COUNT(*) FROM post_comments WHERE id_post = p.id_post) as comments_count, (SELECT COUNT(*) FROM post_likes WHERE id_post = p.id_post AND user_id = 0) > 0 as is_liked, (SELECT GROUP_CONCAT(file_url ORDER BY id ASC SEPARATOR ', ') FROM post_media WHERE id_post = p.id_post) as file_url
FROM `posts` `p`
JOIN `users` `u` ON `p`.`user_id` = `u`.`id_user`
LEFT JOIN `borders` `b` ON `u`.`border_active` = `b`.`id_border`
LEFT JOIN `team` `t` ON `u`.`team_id` = `t`.`team_id`
LEFT JOIN `post_category` `pc` ON `p`.`post_category` = `pc`.`id_category`
WHERE (`p`.`deleted` IS NULL OR `p`.`deleted` =0)
ORDER BY 
            (CASE WHEN p.user_id IN ('')
                THEN 10000000000 + UNIX_TIMESTAMP(p.created_at)
                ELSE likes_count * 86400 + comments_count * 172800 + UNIX_TIMESTAMP(p.created_at)
            END) DESC
        
 LIMIT 5
ERROR - 2026-07-12 21:58:47 --> Severity: error --> Exception: Call to a member function result_array() on false /var/www/html/paddockid/application/models/Post_model.php 236
ERROR - 2026-07-12 14:59:49 --> $config['composer_autoload'] is set to TRUE but /var/www/html/paddockid/application/vendor/autoload.php was not found.
DEBUG - 2026-07-12 14:59:49 --> UTF-8 Support Enabled
DEBUG - 2026-07-12 14:59:49 --> No URI present. Default controller set.
DEBUG - 2026-07-12 14:59:49 --> Global POST, GET and COOKIE data sanitized
ERROR - 2026-07-12 14:59:49 --> Session: Configured save path '/var/www/html/paddockid/application/cache/sessions' is not writable by the PHP process.
ERROR - 2026-07-12 14:59:49 --> Severity: Warning --> session_start(): Failed to initialize storage module: user (path: /var/www/html/paddockid/application/cache/sessions) /var/www/html/paddockid/system/libraries/Session/Session.php 137
DEBUG - 2026-07-12 21:59:49 --> Config file loaded: /var/www/html/paddockid/application/config/ads.php
ERROR - 2026-07-12 21:59:49 --> Query error: Unknown column 'u.last_activity' in 'SELECT' - Invalid query: SELECT p.id_post, p.user_id, p.post_category, u.username, u.display_name, u.avatar, u.verified, u.team_id, u.last_activity, t.team_name, t.team_logo, t.team_color, b.image_url as border, p.content, p.created_at, pc.category_name as category, (SELECT COUNT(*) FROM post_likes WHERE id_post = p.id_post) as likes_count, (SELECT COUNT(*) FROM post_comments WHERE id_post = p.id_post) as comments_count, (SELECT COUNT(*) FROM post_likes WHERE id_post = p.id_post AND user_id = 0) > 0 as is_liked, (SELECT GROUP_CONCAT(file_url ORDER BY id ASC SEPARATOR ', ') FROM post_media WHERE id_post = p.id_post) as file_url
FROM `posts` `p`
JOIN `users` `u` ON `p`.`user_id` = `u`.`id_user`
LEFT JOIN `borders` `b` ON `u`.`border_active` = `b`.`id_border`
LEFT JOIN `team` `t` ON `u`.`team_id` = `t`.`team_id`
LEFT JOIN `post_category` `pc` ON `p`.`post_category` = `pc`.`id_category`
WHERE (`p`.`deleted` IS NULL OR `p`.`deleted` =0)
ORDER BY 
            (CASE WHEN p.user_id IN ('')
                THEN 10000000000 + UNIX_TIMESTAMP(p.created_at)
                ELSE likes_count * 86400 + comments_count * 172800 + UNIX_TIMESTAMP(p.created_at)
            END) DESC
        
 LIMIT 5
ERROR - 2026-07-12 15:01:20 --> $config['composer_autoload'] is set to TRUE but /var/www/html/paddockid/application/vendor/autoload.php was not found.
DEBUG - 2026-07-12 15:01:20 --> UTF-8 Support Enabled
DEBUG - 2026-07-12 15:01:20 --> No URI present. Default controller set.
DEBUG - 2026-07-12 15:01:20 --> Global POST, GET and COOKIE data sanitized
ERROR - 2026-07-12 15:01:20 --> Session: Configured save path '/var/www/html/paddockid/application/cache/sessions' is not writable by the PHP process.
ERROR - 2026-07-12 15:01:20 --> Severity: Warning --> session_start(): Failed to initialize storage module: user (path: /var/www/html/paddockid/application/cache/sessions) /var/www/html/paddockid/system/libraries/Session/Session.php 137
DEBUG - 2026-07-12 22:01:20 --> Config file loaded: /var/www/html/paddockid/application/config/ads.php
ERROR - 2026-07-12 22:01:20 --> Query error: Unknown column 'u.last_activity' in 'SELECT' - Invalid query: SELECT p.id_post, p.user_id, p.post_category, u.username, u.display_name, u.avatar, u.verified, u.team_id, u.last_activity, t.team_name, t.team_logo, t.team_color, b.image_url as border, p.content, p.created_at, pc.category_name as category, (SELECT COUNT(*) FROM post_likes WHERE id_post = p.id_post) as likes_count, (SELECT COUNT(*) FROM post_comments WHERE id_post = p.id_post) as comments_count, (SELECT COUNT(*) FROM post_likes WHERE id_post = p.id_post AND user_id = 0) > 0 as is_liked, (SELECT GROUP_CONCAT(file_url ORDER BY id ASC SEPARATOR ', ') FROM post_media WHERE id_post = p.id_post) as file_url
FROM `posts` `p`
JOIN `users` `u` ON `p`.`user_id` = `u`.`id_user`
LEFT JOIN `borders` `b` ON `u`.`border_active` = `b`.`id_border`
LEFT JOIN `team` `t` ON `u`.`team_id` = `t`.`team_id`
LEFT JOIN `post_category` `pc` ON `p`.`post_category` = `pc`.`id_category`
WHERE (`p`.`deleted` IS NULL OR `p`.`deleted` =0)
ORDER BY 
            (CASE WHEN p.user_id IN ('')
                THEN 10000000000 + UNIX_TIMESTAMP(p.created_at)
                ELSE likes_count * 86400 + comments_count * 172800 + UNIX_TIMESTAMP(p.created_at)
            END) DESC
        
 LIMIT 5
