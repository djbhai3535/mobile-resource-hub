# Advertisement Security & Open Redirect Prevention

## 1. Security Architecture Overview
Monetization and banner delivery systems are frequent targets for injection attacks, cross-site scripting (XSS), and malicious redirects. The Mobile Resource Hub implements multi-layered security controls:

## 2. Protection Layers

### Layer 1: Strict HTTPS Destination Validation
- All destination URLs entered by administrators are validated by `AdvertisementService::isValidDestinationUrl()`.
- Valid patterns:
  - Secure HTTPS URLs (`https://...`) with valid domain syntax.
  - Relative internal URLs (`/...`) for internal site promotions.
- Plaintext `http://`, `javascript:`, `data:`, `vbscript:`, or protocol-relative (`//`) schemes are blocked outright and rejected during form validation.

### Layer 2: Open Redirect Prevention Gateway
- Public links route through the click tracking gateway:
  `GET /ad/click/{advertisement}`
- When triggered:
  1. The controller checks if `destination_url` is non-empty and passes `isValidDestinationUrl()`.
  2. If the URL is tampered with or violates security policy, the gateway aborts redirect to the target and issues a fallback redirect to the application homepage (`/`).
  3. Valid URLs are issued an explicit `302 Found` redirection.

### Layer 3: Privilege Separation for Custom HTML Snippets
- Display network tags (such as Google AdSense, pre-roll snippets) require executing external JavaScript.
- **Strict Role Restriction:** Only users holding the `super-admin` role are permitted to create or modify `custom_html` advertisement types.
- Attempted creation or editing of `custom_html` ads by non-super-admin users (e.g., standard administrators, editors, authors) immediately triggers an HTTP `403 Forbidden` response.
- Every creation and modification is logged in `activity_logs` with the user ID, timestamp, and IP address.

### Layer 4: SEO and Security Link Attributes
- All generated anchor tags enforce:
  `target="_blank" rel="sponsored noopener noreferrer"`
- `sponsored`: Complies with Google Webmaster and Search Console link qualification guidelines for paid/sponsored links.
- `noopener noreferrer`: Prevents reverse tabnabbing attacks (`window.opener` abuse).
