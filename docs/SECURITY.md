# Mobile Resource Hub — Security & Operational Specification

## 1. Authentication & Role-Based Access Control (RBAC)
- Public visitors cannot register or log in.
- Admin authentication is restricted to the `/admin/login` endpoint.
- Brute-force protection: strict rate limiting (`throttle:5,1` - 5 attempts per minute).
- Session regeneration upon login to eliminate session fixation.
- Strict server-side permission checks via Laravel Gates/Policies on every controller action.

## 2. Input Sanitization & Content Security
- CSRF tokens required on all POST/PUT/DELETE forms.
- Blade automatic escaping `{{ $val }}` for all dynamic text to prevent XSS.
- Rich-text editor HTML sanitized with a strict allowlist.
- SQL injection prevention via PDO parameterized queries in Eloquent.

## 3. Upload & File Protection
- Direct execution of `.php`, `.phtml`, `.exe`, or scripts in upload folders is prohibited via `.htaccess`.
- Server-side MIME verification using `fileinfo` extension.
- Random cryptographic hash filenames on upload (prevents directory traversal or predictable filenames).
- Large binaries (firmware archives, flash files) stored externally or streamed through an authorized download proxy with rate limits.

## 4. Privacy & Audit Logs
- No personal data captured from public visitors.
- IP addresses in analytics and contact messages are hashed with salt or truncated to ensure GDPR/privacy compliance.
- Privileged admin actions logged to `activity_logs`.
