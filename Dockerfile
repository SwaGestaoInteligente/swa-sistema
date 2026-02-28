# syntax=docker/dockerfile:1.7

FROM composer:2 AS vendor
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts

COPY . .
RUN composer dump-autoload --no-dev --optimize --no-scripts

FROM node:22-alpine AS frontend
WORKDIR /app

COPY package.json package-lock.json* ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi

COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

FROM php:8.3-fpm-alpine AS app
WORKDIR /var/www/html

RUN apk add --no-cache \
    bash \
    curl \
    git \
    icu-dev \
    libzip-dev \
    nginx \
    oniguruma-dev \
    postgresql-dev \
    unzip \
    zip \
 && docker-php-ext-install \
    bcmath \
    intl \
    mbstring \
    pdo_pgsql \
    zip

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/start.sh /usr/local/bin/start-container

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod +x /usr/local/bin/start-container

EXPOSE 8080

CMD ["/usr/local/bin/start-container"]
