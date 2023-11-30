#!/bin/bash

echo "Stopping application..."

cd ./../


php artisan down

echo "Start Installing..."


echo "#1 Pulling git"
git reset --hard

git checkout develop
git pull

echo "#2 Installing composer"
composer install --no-interaction --prefer-dist --optimize-autoloader


echo "#3 Running migration"
php artisan migrate --force

echo "#4 node production build"
npm run prod

echo "#5 clearing caches"
php artisan optimize:clear


echo "#6 Optimizing caches"
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

echo "#7 Starting application..."
php artisan up
