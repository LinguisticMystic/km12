#!/bin/sh
set -e
cd /var/www/html
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
exec docker-php-entrypoint "$@"
