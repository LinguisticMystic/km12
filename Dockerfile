# syntax=docker/dockerfile:1

# Vite/Tailwind import Filament and Laravel CSS from vendor/ (excluded from build context).
FROM php:8.3-cli-alpine AS vendor
# Match app image extensions so composer platform checks pass (Filament needs intl, zip, etc.).
RUN apk add --no-cache \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
        $PHPIZE_DEPS \
    && docker-php-ext-install intl opcache pdo_mysql mbstring zip \
    && apk del $PHPIZE_DEPS
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --no-autoloader

FROM node:22-alpine AS frontend
WORKDIR /app
COPY --from=vendor /app/vendor ./vendor
COPY package.json ./
RUN npm install
COPY . .
RUN npm run build

FROM php:8.3-fpm-alpine AS app

RUN apk add --no-cache \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
        $PHPIZE_DEPS \
    && docker-php-ext-install intl opcache pdo_mysql mbstring zip \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --no-autoloader \
    && composer clear-cache

COPY . .
COPY --from=frontend /app/public/build ./public/build

RUN composer dump-autoload --optimize --classmap-authoritative

# Fresh APP_KEY + package manifest for --no-dev (avoid host bootstrap/cache with dev-only packages)
RUN cp .env.example .env \
    && php artisan key:generate --force \
    && php artisan package:discover --ansi \
    && php artisan filament:assets \
    && rm -f .env

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint-app.sh
RUN chmod +x /usr/local/bin/docker-entrypoint-app.sh

ENTRYPOINT ["docker-entrypoint-app.sh"]
CMD ["php-fpm"]

FROM nginx:alpine AS web
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public /var/www/html/public
