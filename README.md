<div align="center">

# Velura — Premium E-Commerce Platform

**A production-grade Laravel e-commerce platform built for modern online retail.**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?logo=tailwind-css)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?logo=alpine.js)](https://alpinejs.dev)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql)](https://mysql.com)

[Live Demo](#) · [Admin Demo](#) · [Documentation](#)

</div>

---

## 📸 Screenshots

> Homepage · Product Listing · Product Detail · Cart · Checkout · Admin Dashboard

*(Add screenshots to `/docs/screenshots/` and reference here)*

---

## ✨ Feature Showcase

### Storefront
| Feature | Description |
|---|---|
| 🔍 Smart Search | Fuzzy search with typo correction, autocomplete, trending keywords |
| 🛍️ Cart Drawer | Slide-in AJAX cart with free shipping progress bar |
| ❤️ Wishlist | Add/remove products with animated feedback |
| 🔄 Quick View | Modal product preview without page navigation |
| ⚖️ Compare | Side-by-side product comparison (up to 3) |
| 📦 Order Tracking | Visual timeline with real-time status updates |
| 🎁 Coupons | Discount engine with usage limits, expiry, and type variants |
| 🔔 Notifications | In-app notification center with unread badges |
| 📱 Responsive | Fully optimised for mobile, tablet, and desktop |

### Product Discovery
| Feature | Description |
|---|---|
| 🤖 Personalisation | Browsing-history based recommendations |
| 🔥 Trending | Real-time trending products and keywords |
| ⚡ Flash Sales | Countdown timer with stock urgency bars |
| 🏷️ Brands | Popular brand discovery grid |
| 🗂️ Categories | Hierarchical category tree with emoji icons |
| 📊 Reviews | Verified purchase reviews with star ratings |

### Admin Panel
| Feature | Description |
|---|---|
| 📈 Analytics | Revenue charts, KPI cards, period comparison |
| 📦 Products | Full CRUD with image uploads (Spatie Media Library) |
| 🛒 Orders | Order management with state machine transitions |
| 🏷️ Coupons | Create and manage discount campaigns |
| ⭐ Reviews | Moderation queue with approve/reject |
| 🔔 Low Stock | Real-time inventory alerts |

---

## 🏗️ Architecture

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | Laravel 12, PHP 8.2 |
| **Frontend** | Blade, Tailwind CSS 3, Alpine.js 3 |
| **Database** | MySQL 8.0 |
| **Cache / Queue** | Redis (database driver for local) |
| **Media** | Spatie Laravel Media Library |
| **Auth / Roles** | Laravel Breeze + Spatie Permission |
| **Payment** | Simulated gateway (Razorpay-compatible interface) |
| **Email** | Laravel Mail (Markdown) + Queue |
| **Containerisation** | Docker + Docker Compose |
| **Assets** | Vite + Laravel Mix |

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+, Composer, Node 18+, MySQL 8.0

### Installation

```bash
# 1. Clone
git clone https://github.com/yourusername/velura.git
cd velura

# 2. Install
composer install
npm install

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Configure .env
#    DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 5. Migrate + seed
php artisan migrate
php artisan db:seed

# 6. Storage
php artisan storage:link

# 7. Build + serve
npm run dev
php artisan serve
```

Visit `http://localhost:8000`

### Demo Accounts

| Role | Email | Password |
|---|---|---|
| Admin | admin@velura.in | password |
| Customer | priya@demo.in | password |
| Customer | rahul@demo.in | password |

---

## 🐳 Docker Setup

```bash
cp .env.example .env
# Set DB_HOST=mysql, REDIS_HOST=redis

docker-compose up -d
docker-compose exec app php artisan migrate --seed
docker-compose exec app php artisan storage:link
```

---

## 🌐 Production Deployment

```bash
# Deploy to any server
./deploy.sh

# Required .env values for production
APP_ENV=production
APP_DEBUG=false
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
MAIL_MAILER=ses          # or smtp
FILESYSTEM_DISK=s3       # or public
```

See `DEPLOYMENT.md` for the full production checklist.

---

## 📁 Key Design Decisions

- **Service layer pattern** — all business logic lives in `app/Services/`, controllers stay thin
- **PaymentGateway interface** — swap `SimulatedPaymentService` for real Razorpay in one line
- **UUID public identifiers** — database IDs never exposed in URLs or API responses
- **Event-driven emails** — order events fire jobs, jobs send mail — clean, retryable, queueable
- **Cache-first reads** — categories, featured products, related products all cache-first
- **State machine orders** — `OrderStatus::canTransitionTo()` enforces legal status transitions
- **Race-condition-safe stock** — `lockForUpdate()` + `where stock >= qty` prevents overselling

---

## 📄 Licence

MIT — built for portfolio demonstration purposes.

---

<div align="center">
Built with ❤️ using Laravel + Tailwind CSS
</div>