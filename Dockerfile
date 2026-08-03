# Stage 1: Build Frontend Assets (Node)
FROM node:20-alpine AS node_builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Build Backend Dependencies (Composer)
FROM composer:2.7 AS composer_builder
WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
# Install dependencies only (no scripts/autoloader yet)
RUN composer install --ignore-platform-reqs --no-interaction --no-plugins --no-scripts --prefer-dist --no-dev
COPY . .
RUN composer dump-autoload --optimize --no-dev

# Stage 3: Final Production Image (PHP-FPM + Nginx on Alpine)
FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install lightweight system dependencies & PHP extensions
# libpng-dev/libjpeg-turbo-dev/freetype-dev for Intervention Image (GD)
# zip/unzip/libzip-dev for Excel
# supervisor for queue workers
RUN apk add --no-cache \
    nginx \
    supervisor \
    libpng \
    libpng-dev \
    libjpeg-turbo \
    libjpeg-turbo-dev \
    freetype \
    freetype-dev \
    libzip \
    libzip-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip bcmath opcache \
    && apk del libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev # Remove dev tools to keep image small

# Copy files from previous stages
COPY --from=composer_builder /app /var/www/html
COPY --from=node_builder /app/public/build /var/www/html/public/build

# Nginx configuration
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# PHP configuration for production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php.ini $PHP_INI_DIR/conf.d/custom.ini

# Supervisor configuration (handles Nginx + PHP-FPM + Laravel Queue)
COPY docker/supervisord.conf /etc/supervisord.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
    && chmod -R 775 /var/www/html/storage/framework

# Tambahkan entrypoint script untuk memperbaiki permission binding volumes saat container start
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Expose web server port
EXPOSE 80

# Start Supervisor (which starts Nginx and PHP-FPM)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]