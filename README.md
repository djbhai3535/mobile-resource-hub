# Mobile Resource Hub

> A public, search-driven mobile technology and resource portal built with Laravel, Blade, Bootstrap 5.3, and MySQL for Hostinger Shared Hosting.

---

## 🚀 Features
- **Modern Glassmorphic UI:** Translucent sticky floating header with backdrop blur, smooth card designs, and dark/light mode toggle with zero flicker (FOUT prevented).
- **Search-Centric Catalog:** Brand directories, smartphone specification sheets, firmware releases, flash files, drivers, and technical guides.
- **Shared-Hosting Optimized Search:** Custom token-based search indexing (`search_documents` + `search_terms`) providing complete coverage of model codes, short alphanumeric identifiers, and stop-word immunity without requiring external daemon engines.
- **Strict Role-Based Admin Panel:** Multi-tier administration (`Super Admin`, `Admin`, `Editor`, `Device Manager`, `Firmware Manager`, `SEO Manager`, `Ads Manager`).
- **Technical SEO:** Dynamic sitemaps, structured JSON-LD schemas (`Product`, `SoftwareApplication`, `Article`, `BreadcrumbList`), and canonical URL management.
- **Secure File Delivery:** Metadata indexing with authorized external mirrors, cryptographic checksum verifications, and download event tracking.

---

## 🛠️ Tech Stack & Requirements
- **PHP:** 8.3+ (Hostinger target: 8.5)
- **Framework:** Laravel 13
- **Database:** MySQL / SQLite
- **CSS / UI:** Bootstrap 5.3.x, Bootstrap Icons, Custom Glassmorphic Design System
- **Build Tool:** Vite 6+
- **Environment:** Tested for Hostinger Shared Hosting (Unlimited / Business tier)

---

## 📂 Documentation
- [Product Requirements Document (PRD v1.0)](docs/PRD.md)
- [Architecture Specification](docs/ARCHITECTURE.md)
- [Database Schema Specification](docs/DATABASE.md)
- [UI/UX & Design System Blueprint](docs/UI_UX_BLUEPRINT.md)
- [Search Engine Specification](docs/SEARCH_DESIGN.md)
- [SEO & Discoverability Guide](docs/SEO_IMPLEMENTATION.md)
- [Security & Operational Specification](docs/SECURITY.md)
- [Hostinger Shared Hosting Deployment](docs/SHARED_HOSTING_DEPLOYMENT.md)
- [Phase 0 Feasibility Report](docs/PHASE_REPORTS/PHASE_0_FEASIBILITY_REPORT.md)

---

## 💻 Local Setup
1. **Clone repository:**
   ```bash
   git clone <repo-url>
   cd "d:/mobile webiste"
   ```
2. **Install PHP and Node dependencies:**
   ```bash
   composer install
   npm install
   ```
3. **Environment setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. **Database migration & seeding:**
   ```bash
   php artisan migrate --seed
   ```
5. **Compile frontend assets:**
   ```bash
   npm run build
   ```
6. **Start local development server:**
   ```bash
   php artisan serve
   ```
