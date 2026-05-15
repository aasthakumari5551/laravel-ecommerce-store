#!/usr/bin/env bash
set -euo pipefail

echo ""
echo "═══════════════════════════════════════"
echo "  Velura Production Deploy"
echo "═══════════════════════════════════════"
echo ""

# 1. Pull
echo "→ Pulling latest code..."
git pull origin main

# 2. PHP dependencies
echo "→ Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --quiet

# 3. Frontend
echo "→ Building frontend..."
npm ci --silent
npm run build

# 4. Cache clear (before migrate — avoids stale config issues)
echo "→ Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 5. Migrate
echo "→ Running migrations..."
php artisan migrate --force

# 6. Rebuild caches
echo "→ Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Storage
echo "→ Linking storage..."
php artisan storage:link --force 2>/dev/null || true

# 8. Queue restart
echo "→ Restarting queue workers..."
php artisan queue:restart

# 9. Permissions
echo "→ Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# 10. Warmup
echo "→ Warming up..."
php artisan app:setup-roles 2>/dev/null || true

echo ""
echo "✅ Deploy complete!"
echo ""