FROM php:8.2-fpm

WORKDIR /var/www

# Dependencias sistema + Node
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP extensiones
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar proyecto
COPY . .

# Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Node production build
ENV NODE_ENV=production
RUN npm ci
RUN npm run build

# Permisos Laravel
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 10000

# Migrar BD y arrancar servidor
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT