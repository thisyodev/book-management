#!/usr/bin/env bash
set -e

cd /var/www/html

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

PORT_TO_USE="${PORT:-10000}"
sed -ri "s/Listen 80/Listen ${PORT_TO_USE}/g" /etc/apache2/ports.conf
sed -ri "s/:80>/:${PORT_TO_USE}>/g" /etc/apache2/sites-available/000-default.conf

php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan package:discover --ansi || true

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
  php artisan migrate --force || true
fi

php artisan config:cache || true
php artisan route:cache || true

exec apache2-foreground
