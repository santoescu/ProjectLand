# Imagen base con PHP 8.2 + extensiones necesarias
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
    && docker-php-ext-install mbstring zip exif pcntl bcmath gd

# ➕ Instalar la extensión de MongoDB (OBLIGATORIO)
RUN pecl install mongodb \
    && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini

# Instalar Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Crear carpeta de la app
# Directorio de la app
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Dar permisos a storage y cache
# Permisos
RUN chmod -R 777 storage bootstrap/cache

# Exponer puerto para Cloud Run
# Puerto para Cloud Run
EXPOSE 8080

# Servidor PHP embebido
# Servidor embebido
CMD php -S 0.0.0.0:8080 -t public

