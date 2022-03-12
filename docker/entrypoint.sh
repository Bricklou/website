#!/bin/sh

find /var/www/html/ -mindepth 1 \
    ! -regex "storage/app/public\(/.*\)?" \
    ! -regex "public/storage\(/.*\)?" \
    ! -regex "vendor\(/.*\)?" \
    -delete
cp -a /app/. /var/www/html/
chown -R www-data:www-data /var/www/html/

php artisan storage:link
php artisan config:cache
php artisan view:cache
php artisan route:cache
php-fpm
