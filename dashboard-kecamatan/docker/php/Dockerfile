# Simplified PHP FPM Dockerfile for Cloud Shell

FROM php:8.1-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng \
    libzip \
    libxml2 \
    icu-libs \
    oniguruma \
    su-exec

# Install PHP extensions
RUN apk add --no-cache --virtual .php-build-deps \
    $PHPIZE_DEPS \
    libpng-dev \
    libzip-dev \
    libxml2-dev \
    icu-dev \
    oniguruma-dev && \
    docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache && \
    apk del .php-build-deps

# Copy composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install composer dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader 2>/dev/null || true

# Create required directories
RUN mkdir -p storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap && \
    chmod -R 775 storage bootstrap

# Expose port
EXPOSE 9000

# Use php-fpm directly
CMD ["php-fpm"]
