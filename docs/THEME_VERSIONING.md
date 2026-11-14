# Theme Versioning, Snapshots & Rollback Architecture

## 1. Concept & Requirement
In high-traffic websites, design updates carry operational risk. A change to brand colors, fonts, or glassmorphism opacity can introduce visual regressions. Mobile Resource Hub implements a Git-like Theme Versioning and Snapshot system:
1. **Separation of Concerns:** Administrators modify and test changes in a persistent **Draft** state (`is_published = 0`). The public site continues serving the active **Published** state (`is_published = 1`).
2. **Atomic Publishing:** When publishing, the system compiles the current draft, increments the theme's version counter, captures an immutable JSON snapshot in `theme_versions`, marks the settings as active, and updates the public cache.
3. **One-Click Instant Rollback:** Every past published version is listed in the Theme History ledger (`/admin/theme/history`). Administrators can preview or restore any past version with a single click.

## 2. Database Schema

### `themes` Table
- `id`: Primary key
- `name`: Human-readable theme title (default: "Mobile Resource Hub Neo")
- `slug`: Unique identifier (`mrh-neo`)
- `is_active`: Boolean flag indicating active theme
- `version`: Unsigned integer representing current live version number (e.g., `1`, `2`, `3`)
- `created_by`, `updated_by`: Foreign keys to `users` table

### `theme_settings` Table
- `id`: Primary key
- `theme_id`: Foreign key referencing `themes.id` (cascade delete)
- `is_published`: Boolean (`false` = working draft, `true` = live published settings)
- `branding`, `global_colors`, `light_palette`, `dark_palette`, `typography`, `layout`, `glassmorphism`, `buttons`, `animations`: JSON token groups
- Unique constraint on `[theme_id, is_published]` ensuring exactly one working draft and one live version per theme.

### `theme_versions` Table (Historical Ledger)
- `id`: Primary key
- `theme_id`: Foreign key referencing `themes.id`
- `version_number`: Unsigned integer capturing version number
- `settings_snapshot`: Complete JSON dump of all 9 configuration token groups at publication time
- `change_summary`: Explanatory log note (e.g. "Switched primary accent to emerald for winter campaign")
- `published_by`: Foreign key referencing `users.id`
- `published_at`: Timestamp of deployment

## 3. Restoration & Rollback Flow
When an administrator clicks "Restore This Version" on version `k`:
1. The system verifies administrator authorization (`admin.auth` + admin role).
2. The snapshot stored in `theme_versions.settings_snapshot` is retrieved.
3. Both the active published record (`is_published = 1`) and the current working draft (`is_published = 0`) are updated with the snapshot configuration.
4. The theme's active version is updated to reflect the restored version number.
5. `ThemeService::recompileAndCache()` compiles the newly restored CSS variables and replaces the Redis/file cache entry `mrh_theme_published_css`.
6. An audit event is logged in `activity_logs`.
7. Public visitors immediately see the restored appearance without application restart or downtime.
