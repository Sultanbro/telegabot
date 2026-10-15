FROM composer:2 AS composer
FROM php:8.1-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libpq-dev libsqlite3-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_pgsql pdo_sqlite mbstring dom xml xmlwriter zip \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts --no-autoloader
COPY . .
RUN composer dump-autoload --no-scripts \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
CMD ["sh", "docker/local/test.sh"]
