-- Email verification (Fase E3): token link verifikasi & kedaluwarsa.
ALTER TABLE users
    ADD COLUMN email_token         CHAR(64)    NULL DEFAULT NULL,
    ADD COLUMN email_token_expires DATETIME    NULL DEFAULT NULL;