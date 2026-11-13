# Mobile Resource Hub — Hostinger Shared Hosting Deployment Guide

## 1. Hosting Environment & Baseline
- **Provider:** Hostinger
- **Plan Tier:** Shared Web Hosting — Unlimited / Business-equivalent tier (2 CPU cores, 3 GB RAM, 60 PHP workers).
- **Target PHP Version:** PHP 8.5 (fallback PHP 8.4 / 8.3 supported by Laravel 13).

## 2. Directory Layout Architecture
To prevent exposure of `.env`, `vendor`, and core application logic, the application is deployed into two directories:

```
/home/uXXXXXXX/
├── mobile-resource-hub-app/          <-- Private Application Root (Non-Web Accessible)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   └── artisan
│
└── domains/example.com/public_html/   <-- Public Document Root (Web Accessible)
    ├── build/                         <-- Compiled Vite assets (CSS, JS)
    ├── images/
    ├── .htaccess
    ├── favicon.ico
    ├── robots.txt
    └── index.php                      <-- Modified to bootstrap from ../../mobile-resource-hub-app
```

## 3. Public Entry Point (`public_html/index.php`) Configuration
In `public_html/index.php`:
```php
<?php
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../../mobile-resource-hub-app/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../../mobile-resource-hub-app/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../../mobile-resource-hub-app/bootstrap/app.php')
    ->handleRequest(Request::capture());
```

## 4. Deployment & Build Steps
1. **Local Asset Compilation:**
   ```bash
   npm run build
   ```
2. **Production Dependency Optimization:**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
3. **Database Migration:**
   ```bash
   php artisan migrate --force
   ```
4. **Cache Optimization:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
