#!/usr/bin/env bash
set -e

PORT="${PORT:-10000}"

# Configure Apache to listen on Render's dynamic PORT
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Create .env if not present
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Ensure SQLite database file exists when using sqlite
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    mkdir -p database
    touch database/database.sqlite
fi

# Generate APP_KEY if not provided via environment variables
if [ -z "${APP_KEY}" ]; then
    php artisan key:generate --force
fi

# Run migrations and seed demo data
php artisan migrate --force --seed

# Cache config and routes for production
php artisan config:cache
php artisan route:cache

# Ensure writable permissions
chown -R www-data:www-data storage bootstrap/cache database
chmod -R 775 storage bootstrap/cache database

exec apache2-foreground
