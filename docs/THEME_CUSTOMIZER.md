# Theme Customizer Architecture & Implementation

## 1. Overview
The Mobile Resource Hub Theme Customizer provides a live, split-screen administrative interface that enables website administrators to customize branding, color palettes, typography, glassmorphism tokens, and layout styles without modifying application source files.

All customization tokens compile safely into CSS custom properties (`:root` variables) injected dynamically into public Blade views.

## 2. Architecture & Split-Screen Interface
- **Left Panel (Customizer Controls):** Organized accordion categories for:
  - **Branding:** Website name, tagline, primary logo, dark logo, mobile mark, and favicon.
  - **Global Colors:** Primary brand color, secondary accent, success, warning, danger, and informational colors.
  - **Light Palette:** Light background body, card/surface fill, border tones, and heading/body typography colors.
  - **Dark Palette:** Dark background body, elevated glass surface fill, glowing border tones, and contrasting text colors.
  - **Typography:** Body font families, heading font families, base size, scale ratio, and font weights.
  - **Layout & Geometry:** Container maximum widths, border-radius hierarchy (sm, md, lg, xl, full), and spacing scale.
  - **Glassmorphism:** Surface opacity, background blur radius (`backdrop-filter`), and specular highlight borders.
  - **Buttons & Micro-interactions:** Button border radius, button elevation shadow, transition timing, and hover animations.
- **Right Panel (Live Public Preview Canvas):**
  - High-fidelity iframe rendering the real public application via `/admin/theme/preview`.
  - Responsive viewport switchers: Desktop (100%), Tablet (768px), Mobile (375px).
  - Mode preview switchers: Force Light Mode, Force Dark Mode.
  - Live postMessage synchronization: controls send instant CSS variable updates to the iframe DOM (`document.documentElement.style.setProperty(...)`) providing instantaneous zero-latency feedback before saving.

## 3. Safe CSS Variable Compilation
To prevent CSS injection attacks or layout breakage, the `ThemeService::compileCss()` engine applies strict whitelist validation:
- **Colors:** Validated against `^#(?:[0-9a-fA-F]{3,4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$` or `rgba?\([\d\s,.]+\)`. Invalid hex strings are discarded and replaced with safe system defaults.
- **Dimensions & Opacity:** Validated for numeric values and allowed units (`px`, `rem`, `%`).
- **Font Families:** Whitelisted system-safe and modern web fonts (`system-ui`, `Inter`, `Roboto`, `Plus Jakarta Sans`, `Outfit`, `monospace`).

## 4. Shared Hosting Performance & Caching
Compiling CSS dynamically on every request would degrade performance on shared hosting.
- The compiled published CSS is cached permanently via `Cache::rememberForever('mrh_theme_published_css')`.
- Blade layouts inject `<style id="mrh-dynamic-theme-css">{!! app(ThemeService::class)->getCachedThemeCss() !!}</style>`.
- Cache is automatically invalidated and recompiled when an administrator publishes a new theme version or restores a historical snapshot.
- Zero extra database queries are executed for theme styling during public page browsing.
