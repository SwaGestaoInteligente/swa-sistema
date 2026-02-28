#!/bin/sh
set -eu

mkdir -p /run/nginx

# Garante estrutura e permissões no volume persistente (storage/app)
mkdir -p /var/www/html/storage/app/public /var/www/html/storage/app/private
chown -R www-data:www-data /var/www/html/storage/app
chmod -R 775 /var/www/html/storage/app

if [ "${RUN_QUEUE_WORKER:-true}" = "true" ]; then
    php /var/www/html/artisan queue:work \
        --queue="${QUEUE_NAME:-default}" \
        --sleep="${QUEUE_SLEEP:-1}" \
        --tries="${QUEUE_TRIES:-3}" \
        --max-time="${QUEUE_MAX_TIME:-3600}" &
fi

php-fpm -D
exec nginx -g "daemon off;"
