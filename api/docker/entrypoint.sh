#!/bin/bash
set -e

cd /var/www/html

# Copy .env if it doesn't exist
if [ ! -f .env ]; then
    echo "[entrypoint] Copying .env.example to .env..."
    cp .env.example .env
fi

# Inject Docker environment variables into .env
sed -i "s|^#\?DB_CONNECTION=.*|DB_CONNECTION=mysql|" .env
sed -i "s|^#\?DB_HOST=.*|DB_HOST=${DB_HOST:-mysql}|" .env
sed -i "s|^#\?DB_PORT=.*|DB_PORT=${DB_PORT:-3306}|" .env
sed -i "s|^#\?DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE:-rockencatech}|" .env
sed -i "s|^#\?DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME:-rockencatech_user}|" .env
sed -i "s|^#\?DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD:-secret}|" .env

# Install PHP dependencies (volume mount masks build-time vendor/)
if [ -f composer.json ]; then
    echo "[entrypoint] Running composer install..."
    composer install --no-interaction --optimize-autoloader
fi

# Generate APP_KEY if not set
APP_KEY_VALUE=$(grep '^APP_KEY=' .env | cut -d '=' -f2)
if [ -z "$APP_KEY_VALUE" ] || [ "$APP_KEY_VALUE" = "" ]; then
    echo "[entrypoint] Generating APP_KEY..."
    php artisan key:generate --force
fi

# Set correct permissions
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Run migrations and seeders on first start
MIGRATED_FLAG="storage/.migrated"
if [ ! -f "$MIGRATED_FLAG" ]; then
    echo "[entrypoint] Running migrations and seeders..."
    php artisan migrate --seed --force
    touch "$MIGRATED_FLAG"
    echo "[entrypoint] Migrations complete."
fi

# Clear config cache
php artisan config:clear
php artisan cache:clear

echo "[entrypoint] Starting PHP-FPM and Nginx..."

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
exec nginx -g 'daemon off;'
