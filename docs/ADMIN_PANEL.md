# Mobile Resource Hub — Admin Panel Architecture & User Guide

**Phase:** Phase 2 (Admin Panel & CMS Foundation)  
**Status:** Implemented & Validated  
**Target URL Prefix:** `/admin`  

---

## 1. Overview & Architectural Philosophy
The Mobile Resource Hub Administration Portal provides a high-security, glassmorphic management workspace for portal editors, administrators, and super-administrators. In accordance with PRD Section 6 and Section 20, the admin panel maintains strict brand continuity with the public website while enforcing defense-in-depth security principles.

- **Frontend Stack:** Bootstrap 5.3.9, Bootstrap Icons 1.11.3, Vite 6.4.6, Vanilla JavaScript.
- **Backend Stack:** Laravel 13, Eloquent ORM, MySQL (Production schema), SQLite (Test suite).
- **Design Tokens:** Strict glassmorphism (`backdrop-filter: blur(14px)`), adaptive light/dark mode persistence via `localStorage.getItem('mrh_theme')`.

---

## 2. Authentication & Security Architecture

### 2.1 Route Map
| Route | Method | Controller & Action | Middleware | Purpose |
|---|---|---|---|---|
| `/admin/login` | GET | `AuthController@showLoginForm` | `guest` | Glassmorphic admin sign-in form |
| `/admin/login` | POST | `AuthController@login` | `guest` | Rate-limited authentication handler |
| `/admin/logout` | POST | `AuthController@logout` | `admin.auth` | Invalidate session & regenerate CSRF token |
| `/admin/password/reset` | GET | `AuthController@showForgotPasswordForm` | `guest` | Administrative password recovery prompt |
| `/admin/password/email` | POST | `AuthController@sendResetLinkEmail` | `guest` | Password reset request recorder & audit |
| `/admin/dashboard` | GET | `DashboardController@index` | `admin.auth` | Core system operations dashboard |

### 2.2 Security Safeguards
1. **No Public Registration:** Self-registration is strictly disabled. Administrative accounts are provisioned exclusively through database seeders or authorized CLI commands.
2. **Brute-Force Rate Limiting:** Login attempts are throttled using Laravel's `RateLimiter` (`5 attempts per 1 minute` keyed on normalized email and client IP).
3. **Session Fixation Prevention:** `$request->session()->regenerate()` is executed immediately upon successful authentication.
4. **CSRF Protection:** All state-modifying requests (`POST`, `PUT`, `DELETE`) require a cryptographically verified `_token`.
5. **Password Security:** Passwords are hashed using Bcrypt with 12 rounds.
6. **Authorization Middleware:** `EnsureAdminAuthenticated` ensures visitors cannot access administrative routes unless authenticated and possessing an administrative role (`super-admin`, `admin`, `editor`, `author`). Unauthorized visitors receive HTTP 403 Forbidden.

---

## 3. Operational Dashboard Metrics
The admin dashboard (`/admin/dashboard`) derives 100% of its metrics directly from relational database tables with zero fabricated or hardcoded statistics:
- **Articles & Guides:** Total posts, published posts, drafts, and archived counts.
- **Taxonomy:** Live counts of active categories and catalog tags.
- **Media Assets:** Total image count and aggregate storage utilization formatted dynamically (Bytes / KB / MB).
- **Contact Inquiries:** Unread count badge and total lifetime submission counter.
- **Recent Content Table:** Last 6 authored articles with status badges and quick edit links.
- **Activity Audit Feed:** Last 10 administrative actions displaying actor, action, target entity, timestamp, and IP address.

---

## 4. Media Library Management (`/admin/media`)
- **Approved MIME Verification:** Explicit server-side MIME verification using PHP `fileinfo` (`image/jpeg`, `image/png`, `image/webp`, `image/gif`, `image/svg+xml`). Arbitrary executable uploads (`.php`, `.exe`, `.sh`, `.py`) are rejected with validation errors.
- **Cryptographic Naming:** Uploaded files are assigned 32-character random hash filenames (`Str::random(32) . '.' . $extension`) to prevent directory traversal and predictable filenames.
- **Execution Prevention:** Public storage upload folders are hardened with an `.htaccess` policy disabling script execution engines and CGI handlers.
- **Interactive Tools:** In-browser image preview, clipboard URL copy, alt text management modal, and physical file deletion.

---

## 5. Contact Inquiries Management (`/admin/messages`)
- **Database Persistence:** Messages submitted via `/contact-us` are persisted in `contact_messages` (`name`, `email`, `subject`, `message`, `status`, `ip_hash`).
- **Bot Mitigation:** Submissions containing the hidden honeypot field (`mrh_website_hp`) are accepted silently without inserting database records or triggering logs.
- **Privacy Compliance:** IP addresses are salted and hashed with SHA-256 (`ip_hash`) to comply with privacy requirements.
- **Workflow Tools:** Filter by status (`unread`, `read`, `replied`, `archived`), full message viewer, toggle read/unread status, direct `mailto:` mail client launcher, and permanent deletion.

---

## 6. Navigation Menu Administration (`/admin/menus`)
- **Supported Menus:** Header navigation menu (`location: header`) and Footer legal links (`location: footer`).
- **Item Ordering:** Drag/manual position ordering with active toggle flags.
- **URL Sanitization:** Disallows dangerous URI schemes such as `javascript:`, `data:`, and `vbscript:` to prevent stored XSS attacks.

---

## 7. Theme Customizer & Design Studio (`/admin/theme`)
- **Live Split-Screen:** Controls on the left with 9 token accordions (Branding, Global Colors, Light Palette, Dark Palette, Typography, Layout, Glassmorphism, Buttons, Animations) and a live responsive canvas iframe on the right.
- **Viewport Emulation:** Quick switchers for Desktop (100%), Tablet (768px), and Mobile (375px) device previews.
- **Draft & Live State Isolation:** Working changes are saved into a persistent Draft (`is_published = 0`). The public portal is unaffected until an administrator publishes the theme.
- **Version Snapshots & One-Click Rollback (`/admin/theme/history`):** Publishing increments the version counter, saves an immutable JSON snapshot, and recompiles `:root` and `[data-bs-theme="dark"]` CSS variables into the application cache. Any past version can be restored with a single click.

---

## 8. Homepage Section & Layout Manager (`/admin/homepage`)
- **11 Pre-configured Sections:** Hero, Popular Brands, Latest Devices, Featured Flagships, Recent Firmware, Flash Files, Software Tools, USB Drivers, Tech News, Repair Tutorials, and Middle Advertisement slot.
- **Instant Toggling:** One-click AJAX switches to activate or hide sections on the public homepage.
- **Custom Presentation:** Modal controls to adjust titles, subtitles, maximum item counts (1 to 24), display layout styles (`grid`, `carousel`, `list`, `compact`, `default`, `ad`), and background styling (`default`, `subtle`, `glass`, `dark`).
- **AJAX Reordering:** Drag-and-drop or rank-ordered updates with instant cache invalidation.

---

## 9. Advertisement Manager & Analytics (`/admin/advertisements`)
- **Campaign Management:** Manage campaigns across 17 standardized placement slots with creative types (Image Banners, URL buttons, Approved Networks, and Custom HTML Snippets).
- **Security & Privilege Gating:** Custom HTML snippets require the `super-admin` role; attempts by other administrators return HTTP 403 Forbidden. Destination URLs must pass strict HTTPS validation.
- **Click Tracking Gateway (`/ad/click/{id}`):** Validates URLs, logs click events, increments atomic counters, and issues secure 302 redirects while preventing open redirect attacks.
- **Genuine Analytics (`/admin/advertisements/analytics`):** Real-time impression and click tracking, genuine CTR calculations `(clicks / impressions) * 100`, zero fabricated metrics, and breakdowns by placement, device type, and theme mode.
