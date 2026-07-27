# Multi-stage build for frontend assets
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package*.json vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
RUN npm ci && npm run build

# Main PHP-FPM + Nginx container
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Install system dependencies: Ghostscript, ImageMagick, Poppler-utils, Libzip
RUN apk add --no-cache \
    nginx \
    supervisor \
    ghostscript \
    imagemagick \
    poppler-utils \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    bash \
    curl \
    git \
    unzip \
    icu-data-full

# Install PHP extensions required by Laravel & PDF operations
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip pdo pdo_mysql bcmath intl opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Modify ImageMagick policy to ALLOW PDF processing (Crucial for PDF to JPG conversion)
RUN if [ -f /etc/ImageMagick-7/policy.xml ]; then \
        sed -i 's/rights="none" pattern="PDF"/rights="read|write" pattern="PDF"/g' /etc/ImageMagick-7/policy.xml; \
    elif [ -f /etc/ImageMagick-6/policy.xml ]; then \
        sed -i 's/rights="none" pattern="PDF"/rights="read|write" pattern="PDF"/g' /etc/ImageMagick-6/policy.xml; \
    fi

# Configure Custom PHP ini settings for large PDF uploads & processing
RUN echo "upload_max_filesize = 128M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 128M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_input_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

# Copy project files
COPY . .

# Copy built frontend assets from node-builder stage
COPY --from=node-builder /app/public/build ./public/build

# Install PHP Composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Setup environment file if not exists & generate app key if needed
RUN if [ ! -f .env ] && [ -f .env.example ]; then cp .env.example .env; fi \
    && php artisan key:generate --force

# Set directory permissions for Laravel storage
RUN mkdir -p storage/app/temp storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Nginx & Supervisor configuration
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
