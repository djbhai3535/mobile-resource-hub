# Advertisement Management System Architecture

## 1. Overview
The Advertisement Management System allows website administrators to manage, schedule, rotate, and monetize ad units across the Mobile Resource Hub portal.

It supports four campaign creative types:
1. **Image Banners:** Uploaded creative media files (`jpg`, `png`, `webp`, `gif`) hosted in the local storage disk, wrapped in sponsored tracking links.
2. **External URLs:** Configured partner link cards and call-to-action buttons.
3. **Approved Ad Networks:** Google AdSense, Media.net, or custom display network tags.
4. **Custom HTML Snippets:** Raw HTML/JavaScript embed tags restricted exclusively to the `Super Admin` role with audit logging.

## 2. Targeting & Rotation Mechanics
Each advertisement campaign supports granular delivery targeting:
- **Placement Targeting:** Delivered strictly into authorized placement zones.
- **Device Targeting:** Toggles for Desktop, Tablet, and Mobile viewports.
- **Theme Targeting:** Toggles for Light Theme and Dark Theme display.
- **Flight Scheduling:** Optional `start_date` and `end_date` date-time boundaries. Campaigns outside the window are automatically excluded from delivery.
- **Weighted Rotation:** Proportional random selection based on `rotation_weight` (1 to 100). Higher weights receive a greater proportion of page impressions.

## 3. Delivery Architecture
Advertisements are injected into public Blade templates using the custom `@ad('placement_key')` directive:
```blade
<!-- In resources/views/layouts/public.blade.php -->
@ad('header_below_nav')

<!-- In resources/views/public/home.blade.php -->
@ad('homepage_hero')
@ad('homepage_between_sections')

<!-- Global Footer -->
@ad('global_footer')
```
If no active, in-flight advertisement matches the placement and device context, the helper returns an empty string, producing zero layout shifting.
