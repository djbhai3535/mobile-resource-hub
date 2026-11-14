# Mobile Resource Hub — Role-Based Access Control (RBAC) Specification

**Phase:** Phase 2 (Admin Panel & CMS Foundation)  
**Status:** Implemented & Validated  

---

## 1. Overview
Mobile Resource Hub implements a normalized, relational Role-Based Access Control (RBAC) architecture backed by the `roles`, `permissions`, `role_user`, and `permission_role` database tables. Permissions are enforced on backend controllers and gates, rather than merely hiding user interface elements.

---

## 2. Defined Roles & Responsibilities

| Role Slug | Display Name | Authority Level | Description |
|---|---|---|---|
| `super-admin` | Super Administrator | **Tier 1 (Root)** | Unrestricted global access; automatic bypass via `Gate::before`. |
| `admin` | Administrator | **Tier 2 (Full)** | Manages portal content, taxonomy, static pages, media assets, menus, and activity logs. |
| `editor` | Senior Content Editor | **Tier 3 (Editorial)** | Manages, edits, reviews, and publishes all editorial posts and taxonomy categories. |
| `author` | Staff Writer / Author | **Tier 4 (Authoring)** | Creates and edits own draft articles. Cannot publish directly without editorial review. |

---

## 3. Permissions Matrix

| Permission Slug | Module | Description | Super Admin | Admin | Editor | Author |
|---|---|---|:---:|:---:|:---:|:---:|
| `posts.view` | Posts | View posts management screen | ✓ | ✓ | ✓ | ✓ |
| `posts.create` | Posts | Author new articles & guides | ✓ | ✓ | ✓ | ✓ |
| `posts.edit` | Posts | Edit any author's articles | ✓ | ✓ | ✓ | ✗ *(Own only)* |
| `posts.publish` | Posts | Publish or unpublish articles | ✓ | ✓ | ✓ | ✗ |
| `posts.delete` | Posts | Soft-delete articles | ✓ | ✓ | ✓ | ✗ *(Draft only)* |
| `categories.manage`| Taxonomy | Create, edit, and delete categories | ✓ | ✓ | ✓ | ✗ |
| `tags.manage` | Taxonomy | Create and delete tags | ✓ | ✓ | ✓ | ✗ |
| `pages.manage` | Pages | Edit static CMS legal pages | ✓ | ✓ | ✗ | ✗ |
| `media.manage` | Media | Upload and delete image assets | ✓ | ✓ | ✓ | ✓ |
| `messages.manage` | Messages | Read and manage contact inquiries | ✓ | ✓ | ✗ | ✗ |
| `menus.manage` | Navigation | Reorder and update navigation links | ✓ | ✓ | ✗ | ✗ |
| `activity.view` | Audit | Inspect administrative activity logs | ✓ | ✓ | ✗ | ✗ |
| `settings.manage` | Settings | Manage core site configuration | ✓ | ✗ | ✗ | ✗ |

---

## 4. Enforcement Architecture

### 4.1 Middleware Enforcement
- `admin.auth` (`App\Http\Middleware\EnsureAdminAuthenticated`): Verifies the session and requires at least one recognized administrative role. Public visitors attempting access receive HTTP 403 Forbidden.
- `role:...` (`App\Http\Middleware\EnsureUserHasRole`): Restricts routes to specific role slugs.
- `permission:...` (`App\Http\Middleware\EnsureUserHasPermission`): Evaluates granular permission strings.

### 4.2 Policy Enforcement (`App\Policies\PostPolicy`)
- **`update`:** Evaluates `$user->isEditor() || ($user->isAuthor() && $post->author_id === $user->id)`.
- **`publish`:** Restricts publishing strictly to editors, administrators, and super-administrators.
- **`delete`:** Prevents authors from deleting published articles; permits deletion of own drafts only.

---

## 5. Administrative Activity Logging (Section 12)
Every administrative operation is intercepted and persisted to the `activity_logs` table via `App\Services\ActivityLogger`:
- **Captured Events:** `login`, `logout`, `create`, `update`, `publish`, `unpublish`, `delete`, `restore`, `settings_change`.
- **Captured Metadata:** Actor `user_id`, target `subject_type` and `subject_id`, client IP address, and browser User-Agent.
- **Credential Redaction:** The logger automatically redacts sensitive dictionary keys (`password`, `token`, `secret`, `api_key`, `_token`) to ensure authentication tokens are never exposed in database audit logs.
- **Tamper Resistance:** Audit logs are append-only. No UI deletion or edit endpoints are provided.
