# =========================
#  Etapa 1: Build de assets
# =========================
FROM node:18 AS build

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .

# Construir assets con Vite (incluye Flux)
RUN npm run build


# =========================
#  Etapa 2: PHP + Composer
# =========================
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
RUN pecl install mongodb \
    && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini

# Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar proyecto completo
COPY . .

# Copiar assets compilados desde la etapa 1
COPY --from=build /app/public ./public

# Instalar dependencias PHP optimizadas
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Permisos
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8080

# Servidor embebido para Cloud Run
CMD php -S 0.0.0.0:8080 -t public
