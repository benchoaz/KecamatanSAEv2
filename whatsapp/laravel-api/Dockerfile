FROM php:8.3-fpm-alpine

# Install dependencies
RUN apk update && apk add --no-cache \
    git \
    curl \
    libzip-dev \
    zip \
    unzip \
    mariadb-connector-c-dev \
    sqlite-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . /app

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

EXPOSE 8001

CMD ["php", "-S", "0.0.0.0:8001", "-t", "public"]
