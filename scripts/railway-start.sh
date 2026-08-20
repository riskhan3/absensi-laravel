#!/bin/bash

echo "=== Railway Laravel Startup ==="
cd /var/www

# Set storage permissions
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Buat file .env dari environment variables Railway
echo ">>> Membuat .env dari environment variables..."
cat > /var/www/.env << EOF
APP_NAME="${APP_NAME:-Smart Absensi}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"
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
WHATSAPP_TOKEN=${WHATSAPP_TOKEN}

SCHOOL_LATITUDE=${SCHOOL_LATITUDE:--0.8175879}
SCHOOL_LONGITUDE=${SCHOOL_LONGITUDE:-100.6331262}
GEOFENCE_RADIUS=${GEOFENCE_RADIUS:-500}
GEOFENCING_ENABLED=${GEOFENCING_ENABLED:-true}
EOF

echo ">>> .env berhasil dibuat ✓"
cat /var/www/.env | grep -v PASSWORD | grep -v KEY

# Generate APP_KEY jika masih kosong
if [ -z "$APP_KEY" ]; then
    echo ">>> Generating APP_KEY..."
    php artisan key:generate --force
fi

# Clear cache lama
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

# Jalankan migrasi
echo ">>> Running migrations..."
php artisan migrate --force || echo ">>> WARNING: Migration gagal, lanjut..."

# Storage symlink
php artisan storage:link 2>/dev/null || true

# Cache untuk production
echo ">>> Caching config & routes..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start server
PORT="${PORT:-8080}"
echo ">>> Starting Laravel on 0.0.0.0:$PORT"
exec php artisan serve --host=0.0.0.0 --port=$PORT
