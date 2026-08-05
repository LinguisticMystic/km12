#!/bin/sh
set -e
cd /var/www/html
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs bootstrap/cache storage/app/public
# Public disk symlink for event posters and other uploads (idempotent).
php artisan storage:link --force >/dev/null 2>&1 || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
exec docker-php-entrypoint "$@"
