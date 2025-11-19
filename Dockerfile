# Imagen base PHP 8.2
FROM php:8.2-fpm

# Dependencias del sistema
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

# Instalar Composer (por si Laravel lo usa en runtime)
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Crear directorio
WORKDIR /var/www/html

# Copiar TODO el proyecto incluido vendor y node_modules build
COPY . .

# Dar permisos
RUN chmod -R 777 storage bootstrap/cache

# Exponer puerto
EXPOSE 8080

# Servidor de Laravel
CMD php -S 0.0.0.0:8080 -t public
