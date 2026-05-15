# Velura — Production Deployment Checklist

## Pre-deploy
- [ ] All tests passing locally
- [ ] .env.production values set (see below)
- [ ] Storage symlink verified (php artisan storage:link)
- [ ] Queue worker running (supervisor)
- [ ] Scheduler registered in cron

## .env critical values
```env
APP_NAME=Velura
APP_ENV=production
APP_KEY=base64:...          # php artisan key:generate --show
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=your-rds-host
DB_DATABASE=velura
DB_USERNAME=velura_user
DB_PASSWORD=strong-password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=your-redis-host
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=Velura

FILESYSTEM_DISK=public
```

## Deploy command
```bash
chmod +x deploy.sh && ./deploy.sh
```

## Supervisor (queue worker)
```ini
[program:velura-worker]
command=php /var/www/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
directory=/var/www
user=www-data
numprocs=2
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/log/supervisor/velura-worker.log
```

## Cron

cd /var/www && php artisan schedule:run >> /dev/null 2>&1

## Smoke test after deploy
- [ ] / homepage loads < 2s
- [ ] /shop/products loads with products
- [ ] Add to cart (AJAX, no page reload)
- [ ] Cart drawer opens and loads items
- [ ] Checkout → demo payment → order confirmation
- [ ] Admin login and dashboard
- [ ] Admin can update order status
- [ ] Email sent (check queue logs)
- [ ] 404 page renders correctly
- [ ] Mobile responsive (test at 375px)