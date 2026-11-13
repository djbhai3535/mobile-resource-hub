# Mobile Resource Hub — Technical SEO Specification

## 1. On-Page SEO Requirements
Every server-rendered view includes strict on-page optimization:
- **Title Structure:** `<Title> | Mobile Resource Hub`
- **Meta Description:** Canonical, unique summary generated dynamically per resource.
- **Canonical URL:** Fully qualified absolute URL (`https://...`), excluding duplicate parameters.
- **Heading Hierarchy:** Strict single `<h1>` per page, semantic `<h2>`, `<h3>`.
- **OpenGraph & Twitter Card:** `og:title`, `og:description`, `og:image`, `og:type`, `twitter:card`.

## 2. Dynamic XML Sitemap Engine
- Route: `/sitemap.xml` generates a sitemap index pointing to segmented sub-sitemaps:
  - `/sitemaps/devices.xml`
  - `/sitemaps/brands.xml`
  - `/sitemaps/firmware.xml`
  - `/sitemaps/software.xml`
  - `/sitemaps/posts.xml`
  - `/sitemaps/pages.xml`
- Excludes: drafts, password resets, `/admin/*`, internal query parameters, and `noindex` entities.

## 3. Structured Data (JSON-LD)
- **All Pages:** `WebSite` with SearchAction specification.
- **Navigation:** `BreadcrumbList`.
- **Devices:** `Product` / `TechArticle` schema with verified manufacturer specifications.
- **Software / Drivers:** `SoftwareApplication` with operating system and version schema.
- **Articles / Tutorials:** `Article` / `TechArticle` with author byline, datePublished, and dateModified.
