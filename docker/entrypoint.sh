#!/bin/bash

set -e

echo "📦 Booting Laravel container..."

# Ensure .env exists
if [ ! -f .env ]; then
  echo "🔧 No .env file found. Copying default..."
  cp .env.example .env
fi

# ⏳ Wait until PostgreSQL is ready using real DB connection
echo "⏳ Waiting for PostgreSQL to be ready..."
until php -r "
try {
  new PDO(
    'pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
    getenv('DB_USERNAME'),
    getenv('DB_PASSWORD')
  );
} catch (Exception \$e) {
  exit(1);
}
" > /dev/null 2>&1; do
  echo "Waiting for database connection at ${DB_HOST}:${DB_PORT}..."
  sleep 2
done

echo "✅ PostgreSQL is ready!"

# 🛡️ Set correct permissions
echo "🔐 Fixing permissions for storage and bootstrap/cache..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Laravel setup
echo "⚙️  Running Laravel config caching..."
php artisan config:clear || true
php artisan config:cache || true

echo "🧹 Running storage linking..."
php artisan storage:link || true

echo "🛠 Running migrations..."
php artisan migrate --force || true

echo "🌱 Seeding roles..."
php artisan db:seed --class=RoleSeeder || true

# 🚀 Start PHP-FPM
echo "🚀 Starting php-fpm..."
exec php-fpm
