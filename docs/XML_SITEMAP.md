# Dynamic XML Sitemap Architecture & Crawler Directives

## Architecture Overview
Mobile Resource Hub generates fully dynamic, categorized XML Sitemaps conforming to the [Sitemaps.org 0.9 protocol](https://www.sitemaps.org/protocol.html).

The system uses a **Master Sitemap Index** (`/sitemap.xml`) which links to 8 specialized category sub-sitemaps. This structure prevents index bloat, conforms to Googlebot URL limits, and allows category-specific crawl budgeting.

---

## Sitemap Structure

### 1. Master Sitemap Index
**Route**: `GET /sitemap.xml`  
**Controller**: `App\Http\Controllers\Public\SitemapController@index`  
**Content-Type**: `application/xml; charset=utf-8`  
**Header Directive**: `X-Robots-Tag: noindex` (prevents the index itself from showing as a search result snippet)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc>https://example.com/sitemaps/devices.xml</loc>
    <lastmod>2026-11-14T02:26:00+00:00</lastmod>
  </sitemap>
  <sitemap>
    <loc>https://example.com/sitemaps/brands.xml</loc>
    <lastmod>2026-11-14T02:26:00+00:00</lastmod>
  </sitemap>
  ...
</sitemapindex>
```

### 2. Category Sub-Sitemaps
**Route**: `GET /sitemaps/{section}.xml`

| Sub-Sitemap | Target Content | Change Frequency | Priority |
| :--- | :--- | :---: | :---: |
| `/sitemaps/devices.xml` | Published mobile phone specification pages | `weekly` | `0.8` |
| `/sitemaps/brands.xml` | Active manufacturer brand portals | `weekly` | `0.7` |
| `/sitemaps/firmware.xml` | Published official stock firmware ROMs | `weekly` | `0.7` |
| `/sitemaps/flash-files.xml` | Tested scatter, PAC, and raw flash files | `weekly` | `0.7` |
| `/sitemaps/software.xml` | Flashing tools and servicing software packages | `monthly` | `0.6` |
| `/sitemaps/drivers.xml` | Verified USB, Fastboot, and VCOM drivers | `monthly` | `0.6` |
| `/sitemaps/posts.xml` | Published tech news and step-by-step tutorials | `weekly` | `0.7` |
| `/sitemaps/pages.xml` | Core static routes (Home, About, Privacy, etc.) | `daily` / `monthly` | `1.0` - `0.3` |

---

## Strict Exclusion Rules
To protect search engine crawl trust and preserve domain authority:
1. **Zero Draft Content**: Content where `status != 'published'` or `isPublished() === false` is never emitted.
2. **Zero Trashed Records**: Soft-deleted entries are excluded immediately.
3. **ISO 8601 Timestamps**: Every entry includes a real `<lastmod>` derived from the model's `updated_at` attribute formatted via `$model->updated_at->toIso8601String()`.

---

## Crawler Directives: `robots.txt`
**Route & Physical File**: `/robots.txt`
```text
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /api/
Disallow: /downloads/mirror/

Sitemap: /sitemap.xml
```
- Completely blocks search crawlers from entering administrative portals (`/admin/`).
- Disallows direct indexing of internal JSON API endpoints (`/api/`).
- Disallows tracking gateway mirror redirect clicks (`/downloads/mirror/`).
- Points directly to the master sitemap index.
