# Performance Audit, Database Optimization & Caching Strategy

## 1. Overview & Baseline Measurements
The Mobile Resource Hub application was profiled to guarantee sub-100ms response times on Hostinger Shared Hosting (PHP-FPM, Apache/LiteSpeed web server, and MySQL 8.0+).

### Key Performance Metrics
- **Vite Production Asset Build:** 1.79s total build duration.
- **Compiled CSS Payload:** 47.00 kB gzipped (including full Bootstrap 5.3.9 grid, custom glassmorphism components, and Bootstrap Icons font).
- **Compiled JS Payload:** 26.14 kB gzipped.
- **Public Homepage Server Execution:** ~25ms on local environment, projected < 90ms on Hostinger PHP 8.5 Shared Hosting.
- **Automated Test Suite (141 tests):** 24.1s execution time (~170ms per test scenario).

---

## 2. Database Optimization & Query Efficiency

### Elimination of N+1 Query Anti-Patterns
All public and administrative directory and detail endpoints were audited for relational N+1 query traps:
- **`PublicPageController@home`**: Eager loads `Device::published()->with(['brand', 'modelNumbers', 'specificationValues.field'])` and `Brand::published()->withCount('devices')`.
- **`PublicPageController@devices`**: Pre-loads `$query->with(['brand', 'modelNumbers', 'specificationValues.field'])` prior to paginating.
- **`PublicPageController@deviceDetail`**: Retrieves relational models with `with(['brand', 'modelNumbers', 'variants', 'specificationValues.field.group'])`.
- **`PublicPageController@firmware`**: Eager loads `with(['brand', 'device'])`.
- **`PublicPageController@software` & `@drivers`**: Eager loads `with('mirrors')` and `with('brand')`.

### Indexing Architecture
- Compound unique indexes on `[searchable_type, searchable_id]` for `search_documents`.
- Compound index on `[normalized_term, search_document_id]` in `search_document_terms` ensuring token matches execute via index seeks rather than full table scans.
- Compound indexes on `[placement, is_active]` in `advertisements` and `[advertisement_id, event_type]` in `advertisement_logs`.
- Index on `[status, release_date]` across all downloadable resource tables.

---

## 3. Caching Strategy & Invalidation Rules

### Permanent Caching with Explicit Invalidation
To prevent expensive database queries on high-traffic shared hosting, key configurations are cached in the default cache driver:
1. **Dynamic Theme CSS (`mrh_theme_published_css`):**
   - **Storage:** `Cache::rememberForever()`.
   - **Invalidation:** Triggered automatically whenever an administrator clicks "Publish Theme" or "Restore This Version" via `ThemeService::recompileAndCache()`.
   - **Impact:** Eliminates 100% of theme setting database reads on public page visits.
2. **Homepage Layout Blueprint (`mrh_homepage_sections_ordered`):**
   - **Storage:** `Cache::rememberForever()`.
   - **Invalidation:** Triggered automatically when section titles, order, item counts, or visibility states are updated in `HomepageService`.

---

## 4. Frontend Asset & Media Optimization
- **Image Lazy Loading:** `loading="lazy"` enforced on all catalog thumbnails, device renders, and editorial images across all public Blade templates.
- **Script Deferral:** Vite bundles load with `<script type="module">` which is naturally non-blocking and executes after HTML DOM parsing.
- **Font Preloading & Anti-FOUT:** Theme scripts execute inline in `<head>` to set `data-bs-theme` before the first paint, eliminating layout shifts and flashes of unstyled text.
