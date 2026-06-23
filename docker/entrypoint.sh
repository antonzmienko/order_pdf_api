#!/bin/sh
set -e

if [ ! -f vendor/autoload.php ]; then
    echo "Dependencies not installed. Run ./init.sh first."
    exit 1
fi

exec php artisan serve --host=0.0.0.0 --port=3000
