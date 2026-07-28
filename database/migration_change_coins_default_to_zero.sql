-- Change default coins for new users from 5000 to 0
ALTER TABLE `users` ALTER COLUMN `coins` SET DEFAULT 0;