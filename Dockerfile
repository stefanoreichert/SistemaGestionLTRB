# ---------- FRONTEND BUILD ----------
FROM node:20 AS node_builder

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN rm -f public/hot && npm run build


# ---------- BACKEND (PHP + APACHE) ----------
FROM php:8.2-apache

WORKDIR /var/www/html

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip \
    && a2enmod rewrite headers \
    && sed -ri -e 's!/var/www/html!/var/www/html/public!g' \
        /etc/apache2/sites-available/*.conf \
        /etc/apache2/apache2.conf \
        /etc/apache2/conf-available/*.conf \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar proyecto
COPY . .

ENV COMPOSER_MEMORY_LIMIT=-1

# Instalar Laravel
RUN rm -f public/hot \
    && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist \
    && rm -rf public/build \
    && mkdir -p storage/framework/views \
        storage/framework/cache \
        storage/framework/sessions \
        bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R ug+rwx storage bootstrap/cache

# Copiar build frontend
COPY --from=node_builder /app/public/build ./public/build

# ---------- 🔥 FIX CLAVE PARA RAILWAY ----------
ENV PORT=8080
EXPOSE 8080

# Cambiar Apache a puerto dinámico
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

CMD ["apache2-foreground"]