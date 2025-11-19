# Imagen base PHP 8.2
FROM php:8.2-fpm

# Instalar dependencias del sistema
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
    nodejs \
    npm \
    && docker-php-ext-install mbstring zip exif pcntl bcmath gd

# Instalar extensión MongoDB
RUN pecl install mongodb \
    && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini

# Instalar Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Directorio de la app
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Instalar dependencias NPM
RUN npm install

# Construir Vite
RUN npm run build

# ➕ Generar assets de Flux
RUN php artisan flux:install --force
RUN php artisan flux:generate --force
RUN php artisan vendor:publish --tag=flux-assets --force

# Dar permisos
RUN chmod -R 777 storage bootstrap/cache public/flux

# Exponer puerto Cloud Run
EXPOSE 8080

# Servidor PHP embebido
CMD php -S 0.0.0.0:8080 -t public
