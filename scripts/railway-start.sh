#!/bin/bash
set -e

echo "=== Railway Laravel Startup ==="

# Pastikan storage & bootstrap/cache bisa ditulis
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Jalankan migrasi database
echo ">>> Running migrations..."
php artisan migrate --force

# Jalankan seeder jika fresh deploy (opsional, komen jika tidak diperlukan)
# php artisan db:seed --force

# Optimize
echo ">>> Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Buat symlink storage
echo ">>> Linking storage..."
php artisan storage:link || true

# Start PHP built-in server di port $PORT (Railway set otomatis)
PORT="${PORT:-8000}"
echo ">>> Starting server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT
