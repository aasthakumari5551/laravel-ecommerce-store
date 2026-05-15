# Velura — Production Deployment Checklist

## Environment (.env)
- [ ] APP_ENV=production
- [ ] APP_DEBUG=false
- [ ] APP_KEY generated (php artisan key:generate)
- [ ] APP_URL=https://yourdomain.com
- [ ] DB_* credentials set (MySQL 8.0)
- [ ] CACHE_DRIVER=redis
- [ ] QUEUE_CONNECTION=redis
- [ ] SESSION_DRIVER=redis
- [ ] REDIS_HOST / REDIS_PORT set
- [ ] MAIL_MAILER=ses (or smtp)
- [ ] MAIL_FROM_ADDRESS=noreply@yourdomain.com
- [ ] FILESYSTEM_DISK=public (or s3)
- [ ] BRAND_* config values verified

## Build
- [ ] composer install --no-dev --optimize-autoloader
- [ ] npm run build
- [ ] php artisan config:cache
- [ ] php artisan route:cache
- [ ] php artisan view:cache
- [ ] php artisan event:cache
- [ ] php artisan storage:link

## Database
- [ ] php artisan migrate --force
- [ ] php artisan app:setup-roles
- [ ] php artisan db:seed (CategorySeeder, ProductSeeder)

## Queue / Scheduler
- [ ] queue:work running (supervisor recommended)
- [ ] php artisan schedule:run in cron (every minute)

## Supervisor config (queue worker)
```ini
[program:velura-queue]
command=php /var/www/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/velura-queue.log
```

## Nginx
- [ ] Gzip enabled
- [ ] Static asset cache headers (1y for css/js/images)
- [ ] SSL certificate active
- [ ] www → non-www redirect (or reverse)
- [ ] PHP-FPM socket configured

## Smoke Tests
- [ ] Homepage loads
- [ ] Product listing works
- [ ] Product detail + gallery works
- [ ] Add to cart (AJAX)
- [ ] Cart drawer opens
- [ ] Wishlist toggle
- [ ] Checkout flow
- [ ] Demo payment success
- [ ] Demo payment failure + stock restore
- [ ] Order confirmation email sent (check queue)
- [ ] Admin login + dashboard
- [ ] Admin order status update
- [ ] Notification bell shows unread
- [ ] Compare bar works
- [ ] Quick view opens
- [ ] 404 page renders correctly
- [ ] Mobile responsive