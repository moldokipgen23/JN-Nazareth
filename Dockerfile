FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git unzip curl libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libzip-dev zip sqlite3 libsqlite3-dev \
    nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
       pdo pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

RUN composer dump-autoload --optimize \
    && npm run build \
    && rm -rf node_modules

RUN mkdir -p storage/framework/views storage/framework/cache/data storage/framework/sessions storage/logs storage/app/public bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD mkdir -p storage/framework/views storage/framework/cache/data storage/framework/sessions storage/logs storage/app/public bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache \
    && touch database/database.sqlite \
    && php artisan migrate --force \
    && ( [ -f storage/.seeded ] || ( php artisan db:seed --class=DatabaseSeeder --force && touch storage/.seeded ) ) \
    && php artisan storage:link --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php -S 0.0.0.0:${PORT:-8080} -t public
