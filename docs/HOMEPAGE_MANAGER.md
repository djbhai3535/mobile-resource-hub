# Homepage Section & Layout Manager Architecture

## 1. Overview
The Mobile Resource Hub Homepage Section Manager provides a flexible administrative dashboard to control section ordering, visibility toggles, section titles, subtitles, display counts, and presentation styles without touching Blade code or redeploying the application.

## 2. Managed Homepage Sections
The system ships with 11 pre-configured section blueprints managed via the `homepage_sections` table:
1. **`hero`**: Hero showcase with dynamic search engine, quick tag pills, and device highlights.
2. **`popular_brands`**: Grid or carousel of top smartphone manufacturers with device counts.
3. **`latest_devices`**: Recently added smartphones with key hardware specifications.
4. **`featured_devices`**: Flagship handsets marked as featured.
5. **`latest_firmware`**: Verified stock ROMs, PDA/AP build numbers, and Android versions.
6. **`flash_files`**: Verified scatter files, PAC firmware, and chipset flash files.
7. **`software_tools`**: Flashtools, FRP utilities, and unlock software with OS badges.
8. **`usb_drivers`**: Signed manufacturer USB drivers for Windows.
9. **`tech_news`**: Latest published articles, Android release notes, and security bulletins.
10. **`tutorials`**: Step-by-step firmware flashing and unbricking repair guides.
11. **`ad_middle`**: Dynamic homepage banner placement slot.

## 3. Administrative Capabilities
Located at `/admin/homepage`:
- **Real-Time Toggle:** One-click AJAX switch to instantly show or hide any section from the public landing page.
- **Section Customization:** Edit modal allows updating:
  - Custom Title & Subtitle / Description.
  - Item Count limit (1 to 24 items).
  - Display Style: `grid`, `carousel`, `list`, `compact`, `default`, `ad`.
  - Background Style: `default`, `subtle`, `glass`, `dark`.
  - Category Filter: Optional slug filter to scope data (e.g. `tutorials`, `samsung`).
- **Drag-and-Drop Reordering:** Instant reordering API `/admin/homepage/reorder` updates `sort_order` and invalidates cached layouts.

## 4. Shared Hosting Caching
- Sections order is cached via `Cache::rememberForever(HomepageService::CACHE_KEY_HOMEPAGE_SECTIONS)`.
- Updates, toggles, or reorders automatically invalidate the cache using `Cache::forget()`.
- Catalog models (`Brand`, `Device`, `Firmware`, etc.) are hydrated using eager loading (`withCount`, `take($count)`), keeping MySQL query execution under 5 queries for the entire homepage load.
