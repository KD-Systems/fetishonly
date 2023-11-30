#!/bin/bash

echo "Stopping application..."

cd ./../


php artisan down

echo "Start Installing..."


echo "#1 Pulling git"
git checkout develop
git pull

echo "#2 Installing composer"
composer install --no-interaction --prefer-dist --optimize-autoloader


echo "#3 Running migration"
php artisan migrate --force

npm run prod

echo "#4 clearing caches"
php artisan optimize:clear


echo "#5 Optimizing caches"
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

echo "#6 Starting application..."
php artisan up
