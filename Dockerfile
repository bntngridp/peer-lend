# ─── Stage 1: Build Frontend Assets ─────────────────────────────────────────
FROM node:20-alpine AS frontend-builder

WORKDIR /app

COPY package*.json ./
RUN npm ci --ignore-scripts

COPY . .
RUN npm run build

# ─── Stage 2: PHP Application ─────────────────────────────────────────────────
FROM php:8.3-fpm-alpine AS app

# Install system dependencies
RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    shadow

# Install PHP extensions
RUN docker-php-ext-install \
    bcmath \
    pdo \
    pdo_pgsql \
    pgsql \
    zip \
    intl \
    opcache \
    mbstring

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy dependency files first (layer caching optimization)
COPY composer.json composer.lock ./

# Install PHP dependencies (production only)
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

# Copy application source
COPY . .

# Copy compiled frontend assets from frontend-builder stage
COPY --from=frontend-builder /app/public/build ./public/build

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev

# Set correct file permissions for Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Copy custom PHP configuration
COPY docker/php/php.ini $PHP_INI_DIR/conf.d/custom.ini

# Expose PHP-FPM port
EXPOSE 9000

CMD ["php-fpm"]
