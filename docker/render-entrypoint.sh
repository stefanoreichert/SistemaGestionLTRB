#!/bin/sh
set -eu

PORT="${PORT:-10000}"

sed -ri "s/^Listen [0-9]+$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

rm -f /var/www/html/public/hot

php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"