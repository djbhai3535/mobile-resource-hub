# Technical SEO Automation — Mobile Resource Hub

## Overview
Mobile Resource Hub implements a comprehensive technical SEO automation architecture conforming to modern Google Search Central and Schema.org standards.

---

## Technical Implementations

### 1. Canonical URLs
Every public page renders a self-referential canonical URL tag in `<head>`:
```html
<link rel="canonical" href="https://example.com/mobile-phones/galaxy-s24-ultra">
```
- Eliminates duplicate content penalties arising from query string variations (`?ref=...`, `?page=...`).
- Search results pages (`/search?q=...`) dynamically output:
```html
<meta name="robots" content="noindex, follow">
```
This instructs crawlers not to index infinite parameter search combinations while allowing them to crawl and discover linked resource pages.

### 2. Social Graph Metadata (OpenGraph & Twitter Cards)
Every public resource dynamically populates:
- `og:title`, `og:description`, `og:url`, `og:type` (`website`, `article`, `product`).
- `og:image`: Uses high-resolution featured device/article image with a fallback to `images/og-default.png`.
- `twitter:card`: `summary_large_image`.

### 3. Schema.org JSON-LD Structured Data

#### A. WebSite & SearchAction (Global)
Declared on all pages, enabling Google Sitelinks Search Box:
```json
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "Mobile Resource Hub",
  "url": "https://example.com",
  "potentialAction": {
    "@type": "SearchAction",
    "target": {
      "@type": "EntryPoint",
      "urlTemplate": "https://example.com/search?q={search_term_string}"
    },
    "query-input": "required name=search_term_string"
  }
}
```

#### B. Product Schema (Mobile Device Specifications)
Attached to `/mobile-phones/{device}`:
```json
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "Samsung Galaxy S24 Ultra",
  "brand": {
    "@type": "Brand",
    "name": "Samsung"
  },
  "model": "SM-S928B",
  "description": "Full technical specifications, firmware, and USB drivers.",
  "category": "Smartphone"
}
```

#### C. SoftwareApplication Schema (Firmware, Tools, Drivers)
Attached to `/firmware/{slug}`, `/software/{slug}`, `/drivers/{slug}`:
```json
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "Samsung Odin 3.14.4",
  "applicationCategory": "UtilitiesApplication",
  "operatingSystem": "Windows"
}
```

#### D. Article / HowTo Schema (News & Tutorials)
Attached to editorial articles and repair guides with published and modified timestamps.
