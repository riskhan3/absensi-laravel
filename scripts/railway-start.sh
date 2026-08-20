#!/bin/bash

echo "=== Railway Laravel Startup ==="

# Set storage permissions
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Auto-generate APP_KEY jika belum ada
if [ -z "$APP_KEY" ]; then
    echo ">>> WARNING: APP_KEY kosong, generating..."
    php artisan key:generate --force
else
    echo ">>> APP_KEY sudah ada ✓"
fi

# Clear semua cache lama
php artisan config:clear
php artisan cache:clear

# Jalankan migrasi (lanjut meski gagal)
echo ">>> Running migrations..."
php artisan migrate --force || echo ">>> WARNING: Migration gagal, lanjut..."

# Storage symlink
php artisan storage:link 2>/dev/null || true

# Optimize untuk production
echo ">>> Caching config & routes..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start server
PORT="${PORT:-8000}"
echo ">>> PORT=$PORT"
echo ">>> APP_URL=$APP_URL"
echo ">>> DB_HOST=$DB_HOST"
echo ">>> Starting Laravel on 0.0.0.0:$PORT"
exec php artisan serve --host=0.0.0.0 --port=$PORT
