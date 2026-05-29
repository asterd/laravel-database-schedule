FROM composer:2.7 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-interaction --no-progress

FROM composer:2.7 AS package-tests

WORKDIR /app
COPY --from=vendor /app/vendor ./vendor
COPY . .
CMD ["vendor/bin/phpunit", "tests"]

FROM composer:2.7 AS scheduler

WORKDIR /app
COPY . .
RUN if [ -f composer.json ]; then composer install --prefer-dist --no-interaction --no-progress --no-dev --optimize-autoloader; fi
CMD ["sh", "-lc", "test -f artisan || { echo 'artisan not found: build this target from a Laravel application root.'; exit 64; }; while true; do php artisan schedule:run --no-interaction --verbose; sleep ${SCHEDULE_INTERVAL:-60}; done"]

FROM composer:2.7 AS laravel13-demo

ENV APP_ENV=local \
    APP_DEBUG=true \
    APP_URL=http://localhost:8083 \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/demo/database/database.sqlite \
    SCHEDULE_DATABASE_CONNECTION=sqlite \
    CACHE_STORE=file \
    SESSION_DRIVER=file \
    QUEUE_CONNECTION=sync \
    SCHEDULE_RESTRICTED_ACCESS=0 \
    SCHEDULE_CACHE_ENABLE=1 \
    SCHEDULE_CACHE_DRIVER=file \
    SCHEDULE_CACHE_TTL=30 \
    SCHEDULE_WITHOUT_OVERLAPPING_EXPIRES_AT=5 \
    SCHEDULE_INTERVAL=60 \
    PORT=8083

WORKDIR /demo

RUN composer create-project laravel/laravel:^13.0 . --prefer-dist --no-interaction --no-progress
COPY . /package
COPY docker/demo/routes-web.php /demo/routes/web.php
COPY docker/demo/DemoScheduleServiceProvider.php /demo/app/Providers/DemoScheduleServiceProvider.php
COPY docker/demo/configure-laravel13-env.php /usr/local/bin/configure-laravel13-env.php
COPY docker/demo/seed-laravel13.php /usr/local/bin/seed-laravel13.php
COPY docker/demo/start-laravel13.sh /usr/local/bin/start-laravel13.sh
RUN chmod +x /usr/local/bin/start-laravel13.sh \
    && php /usr/local/bin/configure-laravel13-env.php \
    && php -r '$file = "bootstrap/providers.php"; $contents = file_get_contents($file); $contents = str_replace("];", "    App\\Providers\\DemoScheduleServiceProvider::class,\n];", $contents); file_put_contents($file, $contents);' \
    && composer config repositories.database-schedule path /package \
    && composer require robersonfaria/laravel-database-schedule:@dev --no-interaction --no-progress \
    && touch database/database.sqlite \
    && php artisan optimize:clear \
    && php artisan key:generate --force \
    && php artisan migrate --force \
    && php /usr/local/bin/seed-laravel13.php \
    && php artisan database-schedule:clear-cache

EXPOSE 8083

CMD ["/usr/local/bin/start-laravel13.sh"]
