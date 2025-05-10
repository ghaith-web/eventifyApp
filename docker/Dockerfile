# Use PHP-FPM base image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN echo "🔧 Updating apt and installing dependencies..." && \
    apt-get update && apt-get install -y \
        git curl zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev libpq-dev libpq5 npm netcat-openbsd \
        pkg-config libssl-dev autoconf make && \
    echo "📦 Installing PHP extensions..." && \
    docker-php-ext-install pdo pdo_pgsql pgsql pdo_mysql zip mbstring exif pcntl bcmath gd && \
    echo "📡 Installing and enabling Redis extension..." && \
    pecl install redis && docker-php-ext-enable redis && \
    echo "🧹 Cleaning up build dependencies..." && \
    apt-get purge -y libpq-dev autoconf make pkg-config libssl-dev && \
    apt-get autoremove -y && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    echo "✅ System setup complete!"



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
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
