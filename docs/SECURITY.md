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

## 5. Download Gateway & Private Storage Threat Defense (Phase 4)
- **Private Storage Isolation:** Firmware ROMs, flash files, and tool payloads are kept outside the document root (`storage/app/private_downloads/`).
- **Open Redirect Mitigation:** External mirror URLs are strictly validated to require HTTP or HTTPS schemes, rejecting `javascript:`, `data:`, or malformed URI wrappers.
- **SSRF Defense:** External URLs undergo DNS inspection via `FileStorageService::isSafeExternalUrl()`, explicitly forbidding `localhost`, `127.0.0.1`, `::1`, and RFC1918 private subnets (`10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`).
- **Chunked Stream Delivery:** Local files are streamed in 64 KB chunks (`FileStorageService::streamDownload()`) to protect against server memory exhaustion and DDoS vectors on shared hosting.
- **Gateway Rate Limiting:** The download proxy route `/downloads/mirror/{mirror}` is protected by a strict `throttle:60,1` middleware to mitigate automated scraping and denial-of-service attempts.

## 6. Theme Customization & Advertisement Security (Phase 6)
- **Safe CSS Variable Compiler:** All dynamic CSS tokens submitted through the Theme Customizer are validated with strict regex patterns (`^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$|^rgba?\([0-9\s,\.]+\)$`). Semicolons, braces, and arbitrary stylesheet injection strings are eliminated, preventing CSS-based injection or layout hijacking.
- **Open Redirect Prevention:** The advertisement click tracking gateway `/ad/click/{ad}` validates target URLs using `AdvertisementService::isValidDestinationUrl()`. Non-compliant, invalid, or dangerous schemes (`javascript:`, `data:`, `vbscript:`, protocol-relative `//`) are intercepted and safely redirected to the website root (`/`).
- **Privilege Separation for Custom HTML Ads:** Display network scripts (Google AdSense, pre-rolls) can execute client-side JavaScript. Only users with the `super-admin` role are authorized to save or update `custom_html` advertisement types. Requests by standard administrators, editors, or authors are rejected with HTTP 403 Forbidden.
- **SEO & Reverse Tabnabbing Protection:** All public advertisement anchor tags automatically append `target="_blank" rel="sponsored noopener noreferrer"` preventing `window.opener` access and maintaining compliance with search engine guidelines.
