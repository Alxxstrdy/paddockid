#!/usr/bin/env bash
#
# PaddockID backup (database + uploads + .env).
# Aman dijalankan saat aplikasi hidup (mysqldump --single-transaction).
# TIDAK menghapus database. Hanya merotasi file backup lama.
#
# Pemakaian:
#   BACKUP_DIR=/var/backups/paddockid ./scripts/backup.sh
#   RETENTION_DAYS=14 ./scripts/backup.sh
#
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="${ENV_FILE:-$APP_DIR/.env}"
BACKUP_DIR="${BACKUP_DIR:-/var/backups/paddockid}"
RETENTION_DAYS="${RETENTION_DAYS:-14}"
STAMP="$(date +%Y%m%d-%H%M%S)"
TARGET="$BACKUP_DIR/$STAMP"

if [[ ! -f "$ENV_FILE" ]]; then
  echo "ERROR: env file tidak ditemukan: $ENV_FILE" >&2
  exit 1
fi

read_env() {
  php -r '
    $e = parse_ini_file($argv[1], false, INI_SCANNER_RAW);
    echo $e[$argv[2]] ?? "";
  ' "$ENV_FILE" "$1"
}

DB_HOST="$(read_env DB_HOST)"
DB_USER="$(read_env DB_USER)"
DB_PASS="$(read_env DB_PASS)"
DB_NAME="$(read_env DB_NAME)"

if [[ -z "$DB_NAME" || -z "$DB_USER" ]]; then
  echo "ERROR: DB_NAME/DB_USER kosong di $ENV_FILE" >&2
  exit 1
fi

mkdir -p "$TARGET"
chmod 700 "$TARGET"

CNF="$(mktemp /tmp/paddockid-mysql.XXXXXX.cnf)"
trap 'rm -f "$CNF"' EXIT
chmod 600 "$CNF"
{
  echo "[client]"
  echo "host=${DB_HOST:-localhost}"
  echo "user=${DB_USER}"
  echo "password=\"${DB_PASS//\"/\\\"}\""
} > "$CNF"

echo "[1/3] Dump database $DB_NAME"
mysqldump --defaults-extra-file="$CNF" \
  --single-transaction --quick --routines --triggers \
  "$DB_NAME" | gzip -9 > "$TARGET/database.sql.gz"

echo "[2/3] Arsip uploads/ dan assets/uploads/"
tar -czf "$TARGET/uploads.tar.gz" -C "$APP_DIR" uploads assets/uploads 2>/dev/null || \
  tar -czf "$TARGET/uploads.tar.gz" -C "$APP_DIR" uploads

if [[ -f "$ENV_FILE" ]]; then
  echo "[3/3] Salin .env (sensitif)"
  install -m 600 "$ENV_FILE" "$TARGET/env.backup"
else
  echo "[3/3] .env tidak ada, dilewati"
fi

chmod 600 "$TARGET"/* 2>/dev/null || true
echo "$STAMP" > "$TARGET/MANIFEST.txt"
{
  echo "created_at=$(date -Is)"
  echo "db_name=$DB_NAME"
  echo "app_dir=$APP_DIR"
  echo "retention_days=$RETENTION_DAYS"
} >> "$TARGET/MANIFEST.txt"

# Rotasi backup lama (hanya file backup di BACKUP_DIR).
find "$BACKUP_DIR" -mindepth 1 -maxdepth 1 -type d -mtime +"$RETENTION_DAYS" -print -exec rm -rf {} + 2>/dev/null || true

echo "Selesai: $TARGET"
