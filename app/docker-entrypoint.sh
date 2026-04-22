#!/bin/sh
set -e

# ============================================================
# Laravel Robust Production Entrypoint
# ============================================================

echo "🚀 Initializing SILAP Application Environment..."

# 1. Wait for Database
echo "⏳ Waiting for Database (Postgres)..."
until nc -z db 5432; do
  echo "Still waiting for postgres at db:5432..."
  sleep 2
done
echo "✅ Database is reachable."

# 2. Wait for Redis
echo "⏳ Waiting for Redis..."
until nc -z redis 6379; do
  echo "Still waiting for redis at redis:6379..."
  sleep 2
done
echo "✅ Redis is reachable."

# 3. Ensure storage directories exist and have correct permissions
echo "📁 Checking storage permissions..."
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/app/public
mkdir -p bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 4. Handle APP_KEY if missing
if [ -z "$APP_KEY" ]; then
    echo "🔑 APP_KEY is missing, generating one..."
    php artisan key:generate --force --no-interaction
fi

# 5. Laravel Optimization
echo "📦 Discovering packages..."
php artisan package:discover --ansi

echo "⚙️ Optimizing configuration & routes..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Database Migrations (Safe Mode)
echo "🗄️ Running database migrations..."
php artisan migrate --force --no-interaction || echo "⚠️ Migration failed, check logs."

# 7. Start the application
echo "🏁 SILAP Application is READY and PROTECTED!"
exec "$@"
