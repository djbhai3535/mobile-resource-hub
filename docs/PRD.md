# MOBILE RESOURCE HUB | PRODUCT REQUIREMENTS DOCUMENT
# MOBILE RESOURCE HUB
## PRODUCT REQUIREMENTS DOCUMENT
### Project Specification | v1.0
**Date:** 13 November 2026

## Document Control
| Field | Value |
|---|---|
| Document version | 1.0 |
| Prepared | 13 November 2026 |
| Status | Product baseline for design and implementation |
| Project | Mobile Resource Hub (working title) |
| Public content language | English |
| Admin interface language | English |
| Hosting target | Hostinger Shared Web Hosting — current Unlimited / Business-equivalent tier |
| Primary audience | Google Antigravity development agent and project owner |

**Purpose of this document:** This PRD is the source of truth for building Mobile Resource Hub. The development agent must follow the requirements, respect phase boundaries, test real functionality, and must not represent mockups as completed features.

---

## 1. Executive Summary
Mobile Resource Hub is a public, search-driven mobile technology and resource portal. Visitors can discover mobile phone specifications, browse brands and device models, find firmware information, view flash-file resources, access software and USB-driver listings, and read technology news and tutorials.

Public visitors do not create accounts and do not log in. Only authorized administrators use the protected administration area to manage the website.

The project is intended to become a complete content management platform rather than a static website or a simple blog. Administrators must be able to create, edit, publish, organize, and optimize content from the admin panel. Every newly published public item must become discoverable through the website’s own global search without requiring a manual search-index rebuild.

The visual identity will use a premium glassmorphism-inspired interface, a sticky floating navigation header, a prominent search control, polished cards, subtle animations, and both Light and Dark themes. The website must be fully responsive across mobile phones, tablets, laptops, and desktop screens.

### 1.1 Product decisions
| Area | Decision |
|---|---|
| Product | Mobile Resource Hub |
| Public language | English |
| Public accounts | Not required; no public registration |
| Admin access | Protected admin login only |
| Visual design | Premium glassmorphism-inspired UI |
| Themes | Light and Dark, with saved visitor preference |
| Backend | Laravel 13 |
| PHP | PHP 8.5 target, subject to hosting and dependency verification |
| Database | MySQL |
| Frontend | Laravel Blade, Bootstrap 5.3.x, JavaScript, Vite |
| Search | Full-site content search, exact/prefix/token search and autocomplete |
| SEO | Server-rendered content, technical SEO, sitemap and structured data |
| Hosting | Hostinger Shared Web Hosting, current Unlimited / Business-equivalent tier preferred |
| Source control | GitHub |
| Development tool | Google Antigravity |

### 1.2 Hosting and runtime decision note
As checked on 13 November 2026, Hostinger’s Pakistan shared-hosting page shows Premium and Unlimited plans. Hostinger’s current help-center limits page states that the Web Business plan is now offered as Unlimited on most pages. The published current Unlimited tier lists 2 CPU cores, 3 GB RAM, 50 GB disk, 60 PHP workers, and 150 databases; the Premium tier lists 1 CPU core, 2 GB RAM, 20 GB disk, and 40 PHP workers. Exact plan features can vary by region, purchase generation, promotion, and account; verify the exact limits in the checkout and hPanel before purchase. [R1][R2]

PHP 8.5 is the current stable PHP branch as of the preparation date. PHP 8.6 is still a release candidate and must not be used for production. Laravel 13 supports PHP 8.3 through 8.5. Therefore, target PHP 8.5 only after confirming that the selected Hostinger plan exposes it and all required PHP extensions and Composer dependencies pass a deployment compatibility check. Hostinger applies patch updates for a selected PHP branch automatically. [R3][R4][R5]

Hosting recommendation: choose the current Unlimited / Business-equivalent shared tier rather than Premium if the price is acceptable. It provides more headroom for Laravel, database queries, admin operations, content search, and growth. Shared hosting is appropriate for the initial launch and moderate traffic, but it is not a promise of unlimited compute. If traffic, concurrent searches, or content volume outgrow the account limits, plan an upgrade to Cloud Hosting or VPS.

---

## 2. Product Vision, Goals, and Success Measures
### 2.1 Vision
Provide one reliable, easy-to-use destination where a visitor can search for a mobile device, any relevant model identifier, a specification, firmware metadata, a driver, software, or a technology guide and reach the most relevant published resource quickly.

### 2.2 Primary goals
1. Publish useful and accurate mobile-device information in a consistent structure.
2. Make firmware, flash-file, software, and driver resources easy to discover and understand.
3. Let administrators manage content without editing source code for routine publishing tasks.
4. Make every published public content item searchable through one global search experience.
5. Build crawlable, server-rendered pages with sound technical SEO foundations.
6. Deliver a polished, accessible, responsive website in Light and Dark modes.
7. Run on Hostinger Shared Hosting without requiring root access, persistent Node.js processes, Redis, or VPS-only services.
8. Keep downloadable resources lawful, clearly described, integrity-checkable, and safely delivered.

### 2.3 Success measures
| Measure | Target / validation |
|---|---|
| Public page coverage | Every agreed public page template renders correctly |
| Search completeness | A unique word present only in a published article body or specification can be found |
| Search freshness | A newly published item appears in search immediately after successful publish/index update |
| Search privacy | Draft, private, unpublished, and admin-only records never appear publicly |
| Responsive layout | No horizontal overflow at agreed viewport widths |
| Theme behavior | Light/Dark choice persists and all core components remain readable |
| SEO metadata | Every indexable content page has a unique title, description, canonical URL, and appropriate schema where applicable |
| Sitemap | Published, canonical, indexable URLs are included; private and noindex URLs are excluded |
| Security | No known critical/high security issue remains open at launch |
| Performance | Measure Core Web Vitals and server response on the real hosting plan; optimize against LCP ≤ 2.5 s, INP ≤ 200 ms, CLS ≤ 0.1 where field conditions allow |
| Operational recovery | A documented database/content backup and restore procedure is tested |

---

## 3. Scope
### 3.1 In scope
- Public homepage and navigation.
- Brand directory and individual brand pages.
- Mobile-phone directory and individual device pages.
- Structured and extensible mobile specifications.
- Firmware directory and firmware detail pages.
- Flash-file resource listings and details.
- Mobile software and utility listings and details.
- USB driver listings and details.
- Technology news, articles, tutorials, and guides.
- Global website search across published public text and metadata.
- Live search suggestions and search-result filters.
- Protected admin login and role-based admin access.
- Admin CRUD for all agreed content modules.
- Media library and safe image/file metadata management.
- Homepage section and navigation management.
- Light/Dark theme controls and editable design tokens.
- SEO metadata, sitemap, robots, canonical URLs, redirects, and structured data.
- Advertisement placement management (script activation is controlled and restricted).
- Contact form and protected message storage.
- Download metadata, authorized external links, integrity checksums, and download analytics.
- Hostinger Shared Hosting deployment documentation.
- GitHub-ready source repository, migrations, tests, and setup documentation.

### 3.2 Out of scope for the initial release
- Public visitor registration or user profiles.
- Public comments, forums, social feeds, or user-uploaded content.
- Payments, subscriptions, paid download accounts, or cryptocurrency payments.
- A native Android/iOS app.
- A public API unless separately approved.
- A proprietary search cluster requiring Elasticsearch or a permanently running search service.
- Binary-content inspection of ZIP, RAR, EXE, APK, or firmware files. Search covers their names, descriptions, and metadata only.
- Hosting or redistributing copyrighted firmware/software without explicit distribution rights.
- Guaranteed Google ranking, guaranteed indexing, or guaranteed traffic.
- VPS-only services such as custom Nginx modules, root-managed daemons, Redis servers, Supervisor-managed workers, or persistent Node.js processes.

---

## 4. Users, Roles, and Permissions
### 4.1 Public visitor
A public visitor can browse all published public pages, use global search, view device specifications, read articles, and open authorized download links. No account is required.

### 4.2 Administrator roles
| Role | Permissions |
|---|---|
| Super Admin | Full control, user/role administration, site settings, security settings, backups, theme, ads, and all content |
| Admin | Manage site content and settings assigned by Super Admin; cannot remove or demote the Super Admin |
| Editor | Create and edit articles, guides, static pages, categories, and media; publish rights configurable |
| Device Manager | Manage brands, devices, variants, specification groups, fields, and values |
| Firmware Manager | Manage firmware records, file metadata, mirrors, compatibility, instructions, and download status |
| SEO Manager | Manage metadata, slugs, canonical settings, redirects, sitemap exclusions, and structured-data settings |
| Ads Manager | Manage approved ad slots and placements; cannot edit arbitrary server code or admin credentials |

Permissions must be enforced server-side on every protected route and action. Hiding a button in the interface is not sufficient authorization.

### 4.3 Authentication rules
- Only administrators can authenticate.
- No public registration route or public user-account workflow is to be created.
- Admin login must use secure password hashing, CSRF protection, session regeneration, and login throttling.
- Password reset must use a configured and tested email transport.
- Two-factor authentication is strongly recommended for Super Admin and Admin accounts.
- Admin sessions must expire after a configurable inactivity period.
- All privileged actions must be recorded in activity logs.

---

## 5. Information Architecture and Public Routes
| Page | Suggested route | Purpose |
|---|---|---|
| Homepage | `/` | Main discovery page |
| All mobile phones | `/mobile-phones` | Browse and filter devices |
| Brand directory | `/brands` | Browse manufacturers |
| Brand detail | `/brands/{brand-slug}` | Brand profile and devices |
| Device detail | `/mobile-phones/{device-slug}` | Full specification page |
| Firmware directory | `/firmware` | Browse firmware records |
| Firmware detail | `/firmware/{firmware-slug}` | Build, region, compatibility, instructions, and downloads |
| Flash files | `/flash-files` | Browse flash-file resources |
| Flash file detail | `/flash-files/{file-slug}` | File metadata and authorized download details |
| Software directory | `/software` | Browse tools and utilities |
| Software detail | `/software/{software-slug}` | Version, OS, compatibility, and downloads |
| USB drivers | `/drivers` | Browse drivers |
| Driver detail | `/drivers/{driver-slug}` | Driver details and official source |
| News | `/news` | Article listing |
| Article detail | `/news/{article-slug}` | Full article |
| Tutorials | `/tutorials` | Guide listing |
| Tutorial detail | `/tutorials/{tutorial-slug}` | Full tutorial |
| Search | `/search?q={query}` | Global search results |
| About Us | `/about-us` | Site identity and purpose |
| Contact Us | `/contact-us` | Contact form and contact details |
| Privacy Policy | `/privacy-policy` | Privacy and data-use disclosure |
| Terms | `/terms-and-conditions` | Website terms |
| DMCA | `/dmca` | Copyright notice and removal process |
| Not Found | HTTP 404 | Helpful not-found page |

All public pages must use the shared header, footer, typography, spacing, theme tokens, and breadcrumb components where relevant.

---

## 6. UX and Visual Design Requirements
### 6.1 Brand direction
The design must feel like a premium mobile-technology resource portal: modern, fast, trustworthy, content-focused, and visually organized. It must not look like an unmodified generic blog theme.

### 6.2 Glassmorphism navigation
The primary navigation header must include:
- Sticky or floating position while scrolling.
- Translucent background with `backdrop-filter: blur(...)` where supported.
- A subtle border and restrained shadow/glow.
- Rounded container corners on suitable viewport sizes.
- Logo, primary menu links, search access, and theme toggle.
- Clear hover, focus, active, and expanded states.
- Keyboard-operable dropdowns.
- Mobile hamburger navigation with a clear close action.
- A graceful opaque fallback when backdrop blur is unsupported.
- Glassmorphism must be used selectively. Body text and long-form articles must retain strong contrast and readable surfaces.

### 6.3 Search control design
- Rounded input with search icon.
- Visible search action button.
- Clear-input control when a query exists.
- Focus ring and subtle animated focus state.
- Live suggestion panel with category labels.
- Keyboard support: Up/Down to navigate suggestions, Enter to open, Escape to close.
- Touch-friendly sizing on mobile.
- Loading, empty, error, and no-result states.

### 6.4 Theme tokens
| Token | Light | Dark |
|---|---|---|
| Background | `#F5F7FB` | `#0B1220` |
| Surface | `#FFFFFF` | `#141E30` |
| Primary | `#2563EB` | `#60A5FA` |
| Text | `#172033` | `#E5EDF8` |
| Muted text | `#64748B` | `#94A3B8` |
| Border | `#E2E8F0` | `#263449` |
| Secondary | `#475569` | `#A8B5C7` |

Theme persistence in `localStorage`, system preference default, no flash of unstyled theme (FOUT/theme flash).

### 6.5 Typography, components, and motion
- Font: Inter with system fallback.
- Spacing & radius: consistent scale, 10–16px radius.
- Icons: Bootstrap Icons.
- Cards: thumbnail, category/brand, title, metadata, action.
- Motion: subtle animations, respects `prefers-reduced-motion`.

### 6.6 Responsive behavior
Viewports: 360px to 1440px+.

---

## 7. Public Website Functional Requirements
(PUB-001 through PUB-010, brand/device browsing, structured specs, firmware, software, driver, news/tutorials, legal pages, contact form with honeypot/rate limit/SMTP).

---

## 8. Global Search Requirements and Design
Full text of published public content, token-based index for MySQL on shared hosting, zero stop-word loss, model code normalization, live suggestions, exact/phrase/prefix matching, rate limiting.

---

## 9. Admin Panel Requirements
Route `/admin`, RBAC, dashboard with real statistics, CRUD modules, publishing lifecycle (Draft, In Review, Scheduled, Published, Archived), slug collisions prevention, activity logs.

---

## 10. Theme, Navigation, Homepage, and Advertisement Management
Configurable design tokens, navigation editor, homepage layout manager, ad slot placeholders with safety constraints.

---

## 11. SEO and Google Discoverability Requirements
Unique meta tags, canonical URLs, robots.txt, dynamic XML sitemaps, structured JSON-LD (WebSite, BreadcrumbList, Article, TechArticle, etc.), redirect management.

---

## 12. Download, File, and Content-Rights Requirements
Metadata stored in DB, large binaries linked or on external object storage, strict server-side upload validation, distribution rights status tracking, download events analytics.

---

## 13. Data Model and Database Requirements
Complete normalized schema across users, roles, permissions, activity logs, brands, devices, variants, spec groups/fields/values, firmware, software, drivers, posts, categories, tags, media, menus, theme settings, site settings, ads, SEO, redirects, search documents, search terms, download events, and contact messages.

---

## 14. Technical Architecture and Technology Stack
Laravel 13, PHP 8.5 target (PHP 8.3 minimum compatible), MySQL, Bootstrap 5.3.x, Vite, Blade components, thin controllers, modular service layer.

---

## 15. Hostinger Shared Hosting Deployment Requirements
Public/private directory separation (`public_html` vs private app root), shared hosting constraints (no root, no Redis, no daemons), Vite build before deployment.

---

## 16. Security, Privacy, and Operational Requirements
CSRF, rate-limiting, secure sessions, sanitization, role authorization, database backups, audit logging.

---

## 17. Performance and Scalability Requirements
Eager loading (no N+1), selective caching, asset minification, optimized queries, pagination.

---

## 18. Analytics and Reporting
Internal privacy-first metrics, GA4 support if configured, Search Console integration.

---

## 19. Acceptance Criteria and Test Plan
Rigorous criteria across public UI, CMS, search, SEO, downloads, security, and hosting.

---

## 20. Delivery Phases and Dependencies
- **Phase 0 — Feasibility:** Confirm Hostinger plan/version, PHP 8.5, extensions, MySQL, SSH/Composer/Cron, file layout, storage approach. Exit criteria: Deployment feasibility checklist approved
- **Phase 1 — Foundation and public UI:** Laravel setup, Blade layout, Bootstrap, design system, responsive pages, Light/Dark, glass header, public templates. Exit criteria: All public templates render and responsive/theme tests pass; no fake backend claims
- **Phase 2 — Admin and CMS:** Admin login, RBAC, dashboard, static pages, categories/tags, articles, menus, homepage manager, media. Exit criteria: Secure CRUD and publishing workflow tests pass
- **Phase 3 — Brand/device catalog:** Brands, devices, variants, dynamic specification fields/values, detail pages, relations. Exit criteria: A device can be created and rendered with structured specs and related resources
- **Phase 4 — Firmware/software/drivers:** Firmware records, metadata, rights tracking, file metadata, mirrors, download events, software, drivers. Exit criteria: Authorized resource workflow and safe download behavior pass
- **Phase 5 — Global search and SEO:** Full-content token index, search suggestions/results/ranking, SEO manager, sitemap, redirects, schema. Exit criteria: Search acceptance tests and SEO tests pass
- **Phase 6 — Theme, ads, and analytics:** Theme manager, homepage layout editor, ad slots, internal reports, optional GA4/Search Console support. Exit criteria: Settings are editable, safe, persisted, and reflected on public pages
- **Phase 7 — Hardening and QA:** Security, accessibility, performance, backup/restore, cross-device tests. Exit criteria: Release checklist passes with no open critical/high issues
- **Phase 8 — GitHub and Hostinger launch:** Production build, repository review, deploy, SSL, database, monitoring, smoke tests. Exit criteria: Live site passes production smoke tests and recovery plan is documented

---

## 21. Antigravity Engineering Instructions
Strict adherence to phased implementation, tests, documentation, zero mockups represented as completed backend features, clear reports.
