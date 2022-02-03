#!/bin/bash
cd /var/www/vhosts/mamaearth/me-api

# php /usr/local/bin/composer install

sudo chmod -R 777 storage public vendor bootstrap database

php /usr/local/bin/composer dump-autoload
php artisan cache:clear

php artisan migrate

php artisan config:clear