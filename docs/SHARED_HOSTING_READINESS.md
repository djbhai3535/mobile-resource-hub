# Hostinger Shared Hosting Compatibility & Deployment Architecture

## 1. Hosting Environment Profile
- **Target Platform:** Hostinger Shared Hosting (Business / Cloud Startup / Cloud Professional tiers).
- **Web Server:** LiteSpeed Web Server with Apache `.htaccess` module compatibility.
- **PHP Version Support:** PHP 8.3 minimum (development baseline) to PHP 8.5 target (production).
- **Database Engine:** MySQL 8.0+ or MariaDB 10.4+.
- **Process Model:** Shared PHP-FPM execution pool.
- **Hard Constraints:**
  - Zero background daemon support (No Supervisor, long-running Node.js processes, or persistent queue workers).
  - No Redis, Memcached, Elasticsearch, or Meilisearch daemons.
  - No root / `sudo` access.
  - Restricted cron frequencies (standard 1-minute minimum).
  - Memory limit typically 512 MB to 1024 MB per request.
  - Script timeout typically 60 to 120 seconds.

---

## 2. Directory Topology & Isolation Architecture
To prevent public exposure of sensitive files (`.env`, `storage`, `vendor`, `.git`):

```text
/home/u123456789/
├── mrh_app/                               <-- PRIVATE APPLICATION ROOT (Outside Web Root)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── routes/
│   ├── storage/
│   │   ├── app/
│   │   │   ├── private_downloads/         <-- FIRMWARE & TOOLS (Never exposed directly)
│   │   │   └── public/                    <-- PUBLIC MEDIA ASSETS
│   │   ├── framework/
│   │   └── logs/
│   ├── vendor/
│   ├── .env                               <-- SECURE CONFIGURATION
│   └── artisan
│
└── public_html/                           <-- PUBLIC DOCUMENT ROOT
    ├── build/                             <-- Vite compiled assets (css, js, fonts)
    ├── storage/                           <-- Symlink to mrh_app/storage/app/public
    ├── robots.txt
    ├── favicon.ico
    └── index.php                          <-- Public entry point
```

### Public Entry Configuration (`public_html/index.php`)
```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../mrh_app/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../mrh_app/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../mrh_app/bootstrap/app.php')
    ->handleRequest(Request::capture());
```

---

## 3. Required PHP Extensions & Settings

### Mandatory PHP Extensions
- `BCMath` (cryptographic operations & large integer precision)
- `Ctype` (string character type verification)
- `cURL` (external mirror health checks and API calls)
- `DOM` & `XML` (Sitemap XML generation & RSS syndication)
- `Fileinfo` (secure MIME-type binary inspection for uploads)
- `Filter` (IP and URL safety verification)
- `Hash` (SHA-256 and MD5 file checksums)
- `JSON` (JSON-LD schema serialization & dynamic specifications)
- `Mbstring` (multibyte UTF-8 search tokenization)
- `OpenSSL` (HTTPS transport encryption and AES token encryption)
- `PCRE` (safe CSS variable and query sanitization regex)
- `PDO` & `pdo_mysql` (production database transactions)
- `Session` (admin portal state management)
- `Tokenizer` (Blade compilation)

### Recommended `php.ini` Settings
| Directive | Recommended Production Value | Justification |
|---|---|---|
| `memory_limit` | `512M` | Sufficient for 64 KB streamed chunks & Blade rendering. |
| `max_execution_time` | `120` | Allows search index rebuilding without timeouts. |
| `upload_max_filesize` | `256M` (or max available) | Allows uploading tools, drivers, and recovery images. |
| `post_max_size` | `260M` | Must exceed `upload_max_filesize`. |
| `session.cookie_httponly` | `1` | Prevents XSS cookie theft. |
| `session.cookie_secure` | `1` | Enforces HTTPS-only session transmission. |
| `session.cookie_samesite` | `Lax` | Mitigates CSRF token leakage. |

---

## 4. Cron Task Automation on Shared Hosting
Instead of persistent background queue workers, all background reconciliation tasks are automated through a single standard shared hosting cron job configured in Hostinger hPanel:

```bash
* * * * * cd /home/u123456789/mrh_app && php artisan schedule:run >> /dev/null 2>&1
```

### Tasks Managed via Scheduler:
- Periodic search log reconciliation (`search:index-status`).
- Temporary upload cleanup.
- Stale activity log archival.
