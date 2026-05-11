#!/usr/bin/env bash
set -euo pipefail

echo "🚀 Starting deployment..."

# Pull latest code
git pull origin main

# Install/update dependencies (no dev, optimised)
composer install --no-dev --optimize-autoloader --no-interaction

# Build frontend
npm ci && npm run build

# Run migrations (--force required in production)
php artisan migrate --force

# Clear and rebuild all caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Clear application cache (Redis) — avoids stale cache serving old code
php artisan cache:clear

# Restart queue workers so they pick up new code
php artisan queue:restart

# Create storage symlink if missing
php artisan storage:link --force

echo "✅ Deployment complete."