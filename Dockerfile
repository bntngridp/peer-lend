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
    freetype-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    shadow

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    bcmath \
    pdo \
    pdo_pgsql \
    pgsql \
    zip \
    intl \
    opcache \
    mbstring \
    gd

# Install build tools required for PECL extensions, install phpredis, then clean up
RUN apk add --no-cache --virtual .build-deps \
    build-base \
    autoconf \
    linux-headers \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy dependency files first (layer caching optimization)
COPY composer.json composer.lock ./

# Install PHP dependencies (include dev dependencies so local Docker builds
# can discover Laravel packages such as laravel/pail and keep the stack runnable).
RUN composer install \
    --no-interaction \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

# Copy application source
COPY . .

# Remove stale cached service/provider manifests from the host checkout.
# They can reference dev-only providers that are not installed in the production image.
RUN rm -f bootstrap/cache/*.php

# Copy compiled frontend assets from frontend-builder stage
COPY --from=frontend-builder /app/public/build ./public/build

# Generate optimized autoloader
RUN composer dump-autoload --optimize

# Set correct file permissions for Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Copy custom PHP configuration
COPY docker/php/php.ini $PHP_INI_DIR/conf.d/custom.ini

# Expose PHP-FPM port
EXPOSE 9000

CMD ["php-fpm"]
