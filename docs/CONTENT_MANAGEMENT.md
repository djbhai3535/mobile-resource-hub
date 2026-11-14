# Mobile Resource Hub — Content Management System (CMS) Specification

**Phase:** Phase 2 (Admin Panel & CMS Foundation)  
**Status:** Implemented & Validated  

---

## 1. Overview
The Mobile Resource Hub CMS manages all editorial, tutorial, repair guide, and static legal content across the portal. It bridges administrative authoring with public presentation through automated lifecycle transitions, strict slug uniqueness, and granular publication visibility.

---

## 2. Article Lifecycle & Fields

### 2.1 Database Schema (`posts`)
| Field | Type | Description |
|---|---|---|
| `id` | BigInt (Unsigned, PK) | Primary auto-increment identifier |
| `author_id` | ForeignId (`users`) | Author reference, cascade on delete |
| `category_id` | ForeignId (`categories`, nullable) | Primary category, null on delete |
| `type` | String(40) | Format: `news`, `tutorial`, `guide`, `article` |
| `title` | String(255) | Editorial headline |
| `slug` | String(191, Unique) | URL slug, auto-generated from title |
| `excerpt` | Text (Nullable) | Short summary for cards and social snippets |
| `content` | LongText | Full body copy (HTML / Markdown supported) |
| `featured_image` | String(255, Nullable) | Media library image URL |
| `seo_title` | String(255, Nullable) | Custom SERP title tag |
| `meta_description`| Text (Nullable) | Meta description for search engines |
| `canonical_url` | String(255, Nullable) | Outbound canonical reference if syndicated |
| `og_image` | String(255, Nullable) | Open Graph social card image |
| `status` | String(30, Index) | Lifecycle: `draft`, `published`, `archived` |
| `published_at` | Timestamp (Nullable, Index)| Exact release datetime |
| `deleted_at` | Timestamp (Nullable) | Soft delete timestamp |
| `created_at` / `updated_at` | Timestamps | Audit record creation & revision times |

### 2.2 Lifecycle Rules & Public Visibility
- **Draft:** Visible exclusively in the administrative portal and preview mode (`/admin/posts/{post}/preview`). Requests to public routes (`/news/{slug}`) return **HTTP 404 Not Found**.
- **Published:** Automatically indexed on the public news feed (`/news`) and detail route (`/news/{slug}`). Requires `published_at <= now()`.
- **Archived:** Retained in the database for reference but suppressed from public listings and sitemaps.
- **Trash (Soft Delete):** Removed from standard admin queries; accessible under the `?status=trash` filter with one-click restoration.

---

## 3. Taxonomy Management

### 3.1 Categories (`categories`)
- Supports self-referential hierarchical nesting via `parent_id`.
- Enforces unique slugs and active/inactive status switches.
- Displayed as filterable tags on device guides and firmware changelogs.

### 3.2 Tags (`tags` & `post_tag`)
- Many-to-many relationship allowing articles to attach multiple vendor and hardware tags (e.g. `#Samsung`, `#Snapdragon`, `#Odin`, `#Android15`).
- Fast lookup indexing for related content suggestions.

---

## 4. Static Pages CMS (`pages`)

Four foundational system pages are editable directly through `/admin/pages`:
1. **About Us** (`/about-us` &bull; `system_key: about`)
2. **Privacy Policy** (`/privacy-policy` &bull; `system_key: privacy`)
3. **Terms and Conditions** (`/terms-and-conditions` &bull; `system_key: terms`)
4. **DMCA & Copyright Policy** (`/dmca` &bull; `system_key: dmca`)

### 4.1 System Protection
- The public slugs of core pages are immutable to preserve established search rankings and canonical URLs.
- Content, SEO metadata, and publication status can be updated without touching template files.

---

## 5. Search Engine Architecture Preparation (Section 13)
Full database search indexing is scheduled for Phase 3. In Phase 2, the CMS architecture implements reliable domain events and listeners:
- `App\Events\PostSaved`: Dispatched whenever an article is created or updated.
- `App\Events\PostDeleted`: Dispatched whenever an article is soft-deleted or force-deleted.
- `App\Events\PageSaved`: Dispatched whenever a CMS page is revised.
- `App\Listeners\SearchIndexingHook`: Subscribes to all three events, validating payloads and providing the integration hook where `SearchService::indexDocument()` will be invoked in Phase 3.
