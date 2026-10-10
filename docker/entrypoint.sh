#!/bin/sh

set -e

# Wait for MySQL to accept connections (use host from env or db)
DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"

echo "Waiting for database at ${DB_HOST}:${DB_PORT}..."
until nc -z -v -w30 "${DB_HOST}" "${DB_PORT}" >/dev/null 2>&1; do
  echo "Still waiting..."
  sleep 2
done
echo "Database is reachable."

# Run migrations and seed (idempotent)
php /var/www/html/scripts/migrate.php || true
php /var/www/html/scripts/seed.php || true

echo "Booting Apache..."
exec "$@"
