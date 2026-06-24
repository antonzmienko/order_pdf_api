#!/bin/sh
set -e

if [ ! -f vendor/autoload.php ]; then
    echo "Dependencies not installed. Run ./init.sh first."
    exit 1
fi

export PHP_IDE_CONFIG="${PHP_IDE_CONFIG:-serverName=api}"

exec php artisan serve --host=0.0.0.0 --port=3000
