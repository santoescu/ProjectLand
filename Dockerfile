###############################################
# STAGE 1: Construcción de assets con Node
###############################################
FROM node:18 AS build-assets

WORKDIR /app

COPY package.json package-lock.json vite.config.js ./
COPY resources ./resources

RUN npm install


###############################################
# STAGE 2: Imagen PHP + Laravel
###############################################
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libssl-dev \
    pkg-config \
    && docker-php-ext-install mbstring zip exif pcntl bcmath gd

# Extensión MongoDB
RUN pecl install mongodb && \
    echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Copiar assets del build
COPY --from=build-assets /app/public/build ./public/build

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN php artisan view:clear \
    && php artisan cache:clear \
    && php artisan config:clear \
    && php artisan route:clear

RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8080

CMD php -S 0.0.0.0:8080 -t public
