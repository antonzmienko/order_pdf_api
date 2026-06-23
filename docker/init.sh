#!/bin/sh
set -e

if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
fi

php bin/composer.phar install --no-interaction --prefer-dist

php artisan storage:link --force 2>/dev/null || true

echo "API initialized."
