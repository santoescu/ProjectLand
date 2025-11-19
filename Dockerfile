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

# Extensión MongoDB
RUN pecl install mongodb \
    && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini

# Instalar Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Directorio de la app
WORKDIR /var/www/html

# Copiar proyecto
COPY . .

# Instalar dependencias PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Instalar dependencias JS y generar build
RUN npm install
RUN npm run build

# Publicar assets de Flux
RUN php artisan flux:publish --force

# Permisos
RUN chmod -R 777 storage bootstrap/cache public/flux

# Exponer puerto Cloud Run
EXPOSE 8080

# Ejecutar servidor embebido de PHP
CMD php -S 0.0.0.0:8080 -t public
