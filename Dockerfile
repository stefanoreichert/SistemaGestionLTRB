# ── Stage 1: Vite build (imagen oficial Node 20 LTS) ──────────────────────────
FROM node:20-slim AS node-builder

WORKDIR /app

# Layer cache: instalar dependencias primero
COPY package.json package-lock.json ./
RUN npm ci

# Copiar solo fuentes necesarios para Vite
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources/ ./resources/

# Compilar assets en produccion
ENV NODE_ENV=production
RUN npm run build

# Verificar que manifest.json fue generado
RUN test -f public/build/manifest.json \
    && echo "manifest.json OK" \
    || (echo "ERROR: manifest.json no generado por Vite" && exit 1)


# ── Stage 2: PHP runtime ───────────────────────────────────────────────────────
FROM php:8.2-cli

WORKDIR /var/www

# Sistema (sin Node; ya no hace falta)
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP extensiones
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar proyecto (node_modules/vendor excluidos por .dockerignore)
COPY . .

# PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copiar assets Vite desde stage 1
COPY --from=node-builder /app/public/build ./public/build

# Permisos Laravel
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 10000

# Migrar BD, cachear config y arrancar
CMD php artisan migrate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan serve --host=0.0.0.0 --port=$PORT
