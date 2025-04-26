# Use PHP-FPM base image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev libpq-dev npm netcat-openbsd \
    && docker-php-ext-install pdo pdo_pgsql pgsql pdo_mysql zip mbstring exif pcntl bcmath gd


# Install Composer (from official Composer image)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application source code
COPY . .

# Prepare Laravel directories (important!)
RUN mkdir -p storage/logs bootstrap/cache && \
    chown -R www-data:www-data /var/www && \
    chmod -R 775 storage bootstrap/cache
    
# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev || true

# Optionally install frontend assets (only if you're using Vite or Mix)
RUN npm install && npm run build || true

# Set permissions
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Expose PHP-FPM port
EXPOSE 9000

# Start php-fpm through entrypoint
ENTRYPOINT ["/var/www/docker/entrypoint.sh"]
