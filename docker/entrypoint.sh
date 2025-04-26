#!/bin/bash

set -e

echo "📦 Booting Laravel container..."

# Ensure .env exists
if [ ! -f .env ]; then
  echo "🔧 No .env file found. Copying default..."
  cp .env.example .env
fi

# ⏳ Wait until PostgreSQL is ready
echo "⏳ Waiting for PostgreSQL to be ready..."
until nc -z eventify-pg 5432; do
  echo "Waiting for database connection at eventify-pg:5432..."
  sleep 2
done

echo "✅ PostgreSQL is ready!"

# 🛡️ Set correct permissions for Laravel (storage + cache)
echo "🔐 Fixing permissions for storage and bootstrap/cache..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Run Laravel setup steps
echo "⚙️  Running Laravel config caching..."
php artisan config:clear || true
php artisan config:cache || true

echo "🧹 Running storage linking..."
php artisan storage:link || true

# 🛠 Run migrations automatically
echo "🛠 Running migrations..."
php artisan migrate --force || true

# 🚀 Start php-fpm
echo "🚀 Starting php-fpm..."
exec php-fpm
