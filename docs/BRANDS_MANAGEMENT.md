# Mobile Brands Management Module — Mobile Resource Hub

## 1. Overview
The **Mobile Brands Management** module provides full lifecycle management of smartphone and hardware manufacturers. The system is accessible via `/admin/brands` and powers both administrative oversight and the public-facing brand directories at `/brands` and `/brands/{brand}`.

## 2. Capabilities & Architecture

### 2.1 Fields & Metadata
Each brand record encapsulates:
- **Identity:** Brand Name (unique), URL Slug (auto-generated from name if omitted, unique index), Logo upload path (`public` disk storage).
- **Descriptions:** Short Summary / Tagline (card listings), Full Profile & Historical Background (HTML/Markdown rendered).
- **Verification & Origin:** Country of Origin (verified metadata), Official Website URL.
- **Workflow & Display:** Publication Status (`draft`, `published`, `archived`), Sort Order (numeric priority).
- **SEO & Social Sharing:** SEO Title, Meta Description, Canonical URL, Open Graph Image.

### 2.2 Access Control & Authorization
Governed by `BrandPolicy` registered in `AppServiceProvider`:
- `viewAny` / `view`: Super Admin, Administrator, Editor, Author.
- `create` / `update`: Super Admin, Administrator, Editor.
- `delete` / `restore`: Super Admin, Administrator only.

### 2.3 Deletion Safety & Relational Integrity
To prevent accidental orphan records or catalog breakage:
- **Pre-deletion Check:** The controller actively checks `$brand->devices()->count()`. If devices are associated, deletion is blocked with an informative flash error alerting the admin of the number of attached devices.
- **Soft Deletes:** Deletion uses Laravel `SoftDeletes`. Deleted brands can be inspected under `/admin/brands?trashed=1` and restored with one click.
- **Foreign Key Constraint:** In MySQL migrations, `devices.brand_id` enforces `restrictOnDelete()`.

### 2.4 Event-Driven Search Hooks
Every creation, update, or deletion dispatches:
- `App\Events\BrandSaved`: Fired on creation/update for real-time search indexing.
- `App\Events\BrandDeleted`: Fired on soft or force delete to unindex search documents.
- Handled via `App\Listeners\SearchIndexingHook`.

## 3. Public Integration
- `/brands`: Paginated directory of published brands displaying device counts, origins, and verified logos.
- `/brands/{slug}`: Dedicated manufacturer hub featuring corporate overview, official links, and all published mobile models with model codes and quick spec access.
