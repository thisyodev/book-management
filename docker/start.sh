#!/usr/bin/env sh
set -e

cd /var/www/html

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

envsubst '${PORT}' < /etc/nginx/http.d/default.conf.template > /etc/nginx/http.d/default.conf

if [ -n "$APP_KEY" ]; then
  echo "APP_KEY detected"
else
  echo "APP_KEY is not set. Set APP_KEY in Render environment variables."
fi

php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan package:discover --ansi

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  php artisan migrate --force || true
fi

php artisan config:cache || true
php artisan route:cache || true

exec /usr/bin/supervisord -c /etc/supervisord.conf
