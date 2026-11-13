# Mobile Resource Hub — UI/UX & Design System Blueprint

## 1. Visual Language & Glassmorphism Design
The design system pairs modern translucent glassmorphism with crisp contrast for technical typography and content readability.

### Design Principles:
1. **Focused Glassmorphism:** Translucent glass cards and navigation are utilized for layout frames, headers, and quick-filter badges, using `backdrop-filter: blur(12px)` and subtle linear border gradients.
2. **High-Contrast Content Reading:** Long-form specifications and body text utilize opaque or semi-opaque surfaces (`--surface`) to avoid readability fatigue.
3. **Responsive Geometry:** Rounded corners (`12px` to `16px`), distinct elevation shadows, and touch-optimized tap targets (`>= 44px`).

## 2. Color System & Design Tokens

```css
:root {
  /* Light Theme (Default) */
  --bg-app: #F5F7FB;
  --surface: #FFFFFF;
  --surface-glass: rgba(255, 255, 255, 0.85);
  --surface-glass-border: rgba(226, 232, 240, 0.8);
  --primary: #2563EB;
  --primary-hover: #1D4ED8;
  --primary-light: #EFF6FF;
  --text-main: #172033;
  --text-muted: #64748B;
  --border-subtle: #E2E8F0;
  --secondary: #475569;
  --card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);
  --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
}

[data-bs-theme="dark"] {
  /* Dark Theme */
  --bg-app: #0B1220;
  --surface: #141E30;
  --surface-glass: rgba(20, 30, 48, 0.85);
  --surface-glass-border: rgba(38, 52, 73, 0.8);
  --primary: #60A5FA;
  --primary-hover: #93C5FD;
  --primary-light: rgba(96, 165, 250, 0.15);
  --text-main: #E5EDF8;
  --text-muted: #94A3B8;
  --border-subtle: #263449;
  --secondary: #A8B5C7;
  --card-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.35);
  --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.4);
}
```

## 3. Responsive Breakpoints
- **Mobile Compact (360px – 575px):** Single column, bottom or top floating search trigger, compact glass header with mobile hamburger drawer.
- **Tablet Portrait (576px – 767px):** 2-column device and article card grids.
- **Tablet Landscape (768px – 991px):** Expanded navigation, 2-to-3 column grids.
- **Desktop (992px – 1199px):** Full sticky glass header, 3-column card grids, sidebar filters.
- **Large Desktop (1200px – 1440px+):** Max container width `1280px`, 4-column device grids, 3-column article grids, detailed 2-column specification tables.

## 4. Key Interactive Components
1. **Floating Glass Header:** Sticky at `top: 1rem` (or `0` on mobile), backdrop blur, brand logo, desktop nav links, instant search input, and one-click Theme Switcher (Moon/Sun) with zero theme flicker (FOUT script in `<head>`).
2. **Prominent Search Bar:**
   - Large hero search bar on homepage and sleek header search bar on all subpages.
   - Interactive live autocomplete dropdown showing grouped results:
     - 📱 *Devices*
     - ⚙️ *Firmware & Flash Files*
     - 🛠️ *Software & Drivers*
     - 📰 *Articles & Guides*
   - Keyboard navigable (Up/Down arrow keys, Enter to navigate, Escape to dismiss).
3. **Device Specification Tables:**
   - Grouped accordion or tabbed view (General, Network, Body, Display, Platform, Memory, Camera, Battery, Connectivity).
   - Distinct badges for verified vs. unconfirmed parameters.
