# Google Search Console & Webmaster Setup Guide

## Overview
This document provides production verification, sitemap submission, and indexing workflows for Google Search Console (GSC) and Bing Webmaster Tools for Mobile Resource Hub.

---

## 1. Property Setup & Domain Verification

### Recommended Method: DNS TXT Record
1. Sign in to [Google Search Console](https://search.google.com/search-console).
2. Select **Domain Property** (`mobile-resource-hub.com`).
3. Add the TXT record to your DNS zone on Hostinger / Cloudflare:
   ```text
   google-site-verification=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
   ```

### Fallback Method: HTML File or Meta Tag
If DNS access is delayed, insert the verification meta tag into `resources/views/layouts/public.blade.php`:
```html
<meta name="google-site-verification" content="XXXXXXXXXXXXXXXXX">
```

---

## 2. Sitemap Submission

1. In GSC, navigate to **Index -> Sitemaps**.
2. Enter the URL of the master sitemap index:
   ```text
   sitemap.xml
   ```
3. Click **Submit**.
4. GSC will automatically detect the index and crawl all 8 child sitemaps (`/sitemaps/devices.xml`, `/sitemaps/firmware.xml`, etc.).

---

## 3. Monitoring Crawl Efficiency & Index Coverage

### Expected Crawl Behavior
- **Pages with `<meta name="robots" content="noindex, follow">`**:
  - Global Search queries (`/search?q=...`) should report as **"Excluded by 'noindex' tag"** in GSC. This is intentional and prevents duplicate query bloat.
- **Valid URLs**:
  - Mobile specifications, firmware download pages, guides, and tools should report **"Indexed"** with valid green checkmarks under Rich Results for:
    - **Sitelinks Searchbox** (via `WebSite` JSON-LD)
    - **Product Snippets** (via `Product` JSON-LD on `/mobile-phones/{device}`)
    - **Articles & HowTo Guides** (via `Article` and `HowTo` JSON-LD on `/news/{slug}` and `/tutorials/{slug}`)
    - **Breadcrumbs** (via `BreadcrumbList` JSON-LD)

---

## 4. Addressing 404s and Soft 404s
- If a device or firmware model is permanently unpublished or deleted, the system serves an HTTP 404 status.
- Search documents for unpublished records are automatically purged from `search_documents` and sub-sitemaps, eliminating soft-404 crawl waste.
