# Use official PHP-FPM image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev libpq-dev libpq5 npm netcat-openbsd \
    pkg-config libssl-dev autoconf make dos2unix && \
    docker-php-ext-install pdo pdo_pgsql pgsql pdo_mysql zip mbstring exif pcntl bcmath gd && \
    pecl install redis && docker-php-ext-enable redis && \
    apt-get purge -y libpq-dev autoconf make pkg-config libssl-dev && \
    apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app source code
COPY . .

# Ensure Laravel folders are writable
RUN mkdir -p storage/logs bootstrap/cache && \
    chown -R www-data:www-data /var/www && \
    chmod -R 775 storage bootstrap/cache

# Copy entrypoint script and fix CRLF issues on Windows
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN dos2unix /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev || true

# Optionally install JS dependencies
RUN npm install && npm run build || true

# Expose PHP-FPM port
EXPOSE 9000

ENTRYPOINT ["sh", "/usr/local/bin/entrypoint.sh"]
