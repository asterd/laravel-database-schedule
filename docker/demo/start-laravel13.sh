#!/bin/sh
set -eu

touch /demo/database/database.sqlite

php artisan optimize:clear
php artisan migrate --force
php /usr/local/bin/seed-laravel13.php
php artisan database-schedule:clear-cache

(
    while true; do
        php artisan schedule:run --no-interaction --verbose
        sleep "${SCHEDULE_INTERVAL:-60}"
    done
) &

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8083}"
