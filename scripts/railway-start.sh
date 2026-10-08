#!/bin/bash

echo "=== Railway Laravel Startup ==="
cd /var/www/html

# Set permissions
mkdir -p storage/framework/sessions storage/framework/views \
         storage/framework/cache/data storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Buat .env dari environment variables Railway
echo ">>> Membuat .env..."
cat > /var/www/html/.env << ENVEOF
APP_NAME="${APP_NAME:-Smart Absensi}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"
APP_TIMEZONE="${APP_TIMEZONE:-Asia/Jakarta}"
TRUSTED_PROXIES="${TRUSTED_PROXIES:-*}"

LOG_CHANNEL=stack
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

SESSION_DRIVER=${SESSION_DRIVER:-file}
SESSION_LIFETIME=120
CACHE_STORE=${CACHE_STORE:-file}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}
FILESYSTEM_DISK=local

WHATSAPP_ENABLED=${WHATSAPP_ENABLED:-false}
SCHOOL_LATITUDE=${SCHOOL_LATITUDE:--0.8175879}
SCHOOL_LONGITUDE=${SCHOOL_LONGITUDE:-100.6331262}
GEOFENCE_RADIUS=${GEOFENCE_RADIUS:-500}
GEOFENCING_ENABLED=${GEOFENCING_ENABLED:-true}
ENVEOF

echo ">>> .env dibuat ✓"
echo ">>> APP_KEY ada: $([ -n '$APP_KEY' ] && echo YES || echo NO)"

# Clear cache
php artisan config:clear 2>/dev/null || true

# Migrasi
echo ">>> Running migrations..."
php artisan migrate --force || echo ">>> Migration warning, continuing..."

# Jalankan seeder
echo ">>> Running seeder..."
php artisan db:seed --force || echo ">>> Seeder warning, continuing..."

# Storage symlink
php artisan storage:link 2>/dev/null || true

# Cache production
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start server
PORT="${PORT:-8080}"
echo ">>> Starting on 0.0.0.0:$PORT"
exec php artisan serve --host=0.0.0.0 --port=$PORT
