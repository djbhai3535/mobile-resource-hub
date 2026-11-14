# Advertisement Placements Catalog & Specifications

## 1. Placements Directory
The application defines 17 standardized placement slots across all primary layout positions:

| Key | Label | Typical Dimensions | Target Context |
|---|---|---|---|
| `header_top` | Header Above Navigation | 728x90 / Fluid Leaderboard | Global top bar |
| `header_below_nav` | Below Navigation Bar | 970x90 / 728x90 Leaderboard | Global layout header |
| `homepage_hero` | Homepage Below Hero Search | 970x250 / 728x90 Billboard | Homepage |
| `homepage_between_sections` | Homepage Between Content | 728x90 / Fluid Card Banner | Homepage middle |
| `homepage_bottom` | Homepage Above Footer | 970x90 / Fluid Banner | Homepage lower section |
| `sidebar_top` | Sidebar Top Slot | 300x250 / 336x280 Medium Rectangle | Detail sidebars |
| `sidebar_middle` | Sidebar Middle Slot | 300x250 Medium Rectangle | Detail sidebars |
| `sidebar_sticky` | Sidebar Sticky Bottom | 300x600 Half Page Skyscraper | Sticky desktop rail |
| `article_before_content` | Article Top | 728x90 / Fluid | Guides & Tech News |
| `article_in_content` | Article In-Body Mid-Post | 728x90 / 300x250 In-Article | Guides & Tech News |
| `article_after_content` | Article Bottom | 728x90 Leaderboard | Guides & Tech News |
| `device_specs_top` | Device Specs Header | 728x90 Banner | Device specifications |
| `device_specs_bottom` | Device Specs Footer | 728x90 Banner | Device specifications |
| `firmware_page_top` | Firmware Listing Top | 728x90 Banner | Firmware & ROM pages |
| `download_page_timer` | Download Countdown Timer | 336x280 / 300x250 Rectangle | Download timer modal/page |
| `download_complete` | Download Completed Screen | 728x90 Banner | Download finish view |
| `global_footer` | Above Global Footer | 970x90 / Fluid Leaderboard | Global layout bottom |

## 2. Responsive Fallbacks
Banners are styled with `.mrh-ad-slot` container styles featuring `max-width: 100%`, `height: auto`, and `overflow: hidden`, guaranteeing fluid responsiveness on mobile viewports.
