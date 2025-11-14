# Imagen base con PHP 8.2 + extensiones necesarias
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
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Crear carpeta de la app
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Dar permisos a storage y cache
RUN chmod -R 777 storage bootstrap/cache

# Exponer puerto para Cloud Run
EXPOSE 8080

# Servidor PHP embebido
CMD php -S 0.0.0.0:8080 -t public
