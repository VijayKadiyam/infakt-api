#!/bin/bash
cd /var/www/vhosts/infakt/infakt-api

# php /usr/local/bin/composer install

sudo chmod -R 755 storage public vendor bootstrap database

php /usr/local/bin/composer dump-autoload
php artisan cache:clear

php artisan migrate

php artisan config:clear