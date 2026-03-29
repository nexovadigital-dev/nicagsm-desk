#!/bin/bash
# NexovaDesk - Deploy script for Hostinger shared hosting
# Run this on the server after cloning the repo

set -e

APP_DIR="/home/u164741808/domains/nexovadesk.com/public_html/nexova-app"
cd "$APP_DIR"

echo "==> Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Installing Node dependencies & building assets..."
npm ci --omit=dev
npm run build

echo "==> Setting up environment..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "!! .env created from .env.example — fill in DB and other values"
fi

echo "==> Generating app key..."
php artisan key:generate --no-interaction

echo "==> Running migrations..."
php artisan migrate --force --no-interaction

echo "==> Linking storage..."
php artisan storage:link --no-interaction 2>/dev/null || true

echo "==> Caching config/routes/views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Setting permissions..."
chmod -R 755 storage bootstrap/cache

echo "==> Done! NexovaDesk is deployed."
