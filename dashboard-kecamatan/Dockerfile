# ============================================================
# Laravel PHP-FPM Optimized Dockerfile
# Build: docker build -t dashboard-kecamatan:latest .
# Run: docker-compose up -d
# ============================================================

# Stage 1: Base image with PHP 8.1
FROM php:8.1-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    shadow \
    curl \
    git \
    unzip \
    libzip-dev \
    zip \
    oniguruma-dev \
    npm \
    mysql-client \
    imagemagick \
    sqlite-dev \
    icu-dev \
    freetype-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    $PHPIZE_DEPS

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    pdo_sqlite \
    mbstring \
    exif \
    pcntl \
    bcmath \
    opcache \
    zip \
    intl \
    gd

# Install Redis extension
RUN pecl install redis-6.0.2 \
    && docker-php-ext-enable redis

# Configure OPcache for production
RUN echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=32" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ============================================================
# Stage 2: Application build
# ============================================================
FROM base AS builder

WORKDIR /var/www

# Copy composer files first for better layer caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy application code
COPY . .

# ============================================================
# Stage 3: Production runtime
# ============================================================
FROM base AS production

WORKDIR /var/www

# Copy only necessary files for production
COPY --from=builder /var/www/vendor ./vendor
COPY --from=builder /var/www/bootstrap ./bootstrap
COPY --from=builder /var/www/config ./config
COPY --from=builder /var/www/app ./app
COPY --from=builder /var/www/database ./database
COPY --from=builder /var/www/public ./public
COPY --from=builder /var/www/resources ./resources
COPY --from=builder /var/www/routes ./routes
COPY --from=builder /var/www/storage ./storage

# Create symbolic links for storage
RUN mkdir -p storage/framework/{cache,sessions,views} \
    && mkdir -p storage/app/public \
    && chmod -R 775 storage \
    && chmod -R 775 bootstrap/cache \
    && chown -R www-data:www-data /var/www

# Copy custom PHP-FPM configuration
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# Expose port 9000
EXPOSE 9000

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD php-fpm -t || exit 1

# Start PHP-FPM
CMD ["php-fpm", "-d", "opcache.enable=1", "-d", "opcache.memory_consumption=256"]

# ============================================================
# Stage 4: Development (optional)
# ============================================================
FROM base AS development

WORKDIR /var/www

# Copy all files
COPY . .

# Install dependencies (including dev)
RUN composer install --no-interaction

# Set permissions
RUN chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data /var/www

EXPOSE 9000

CMD ["php-fpm"]
