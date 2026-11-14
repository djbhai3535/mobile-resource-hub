# Advertisement Analytics & CTR Computation

## 1. Zero Fabrication Policy
Mobile Resource Hub enforces a strict Zero Fabrication policy for advertisement metrics. No dummy random numbers or artificially inflated impressions/clicks are rendered. All statistics displayed in the administrative reporting portal reflect genuine database events logged in `advertisement_logs` and atomic counters in `advertisements`.

## 2. Event Logging Architecture

### `advertisement_logs` Table
- `id`: Auto-incrementing primary key
- `advertisement_id`: Foreign key referencing `advertisements.id` (cascade delete)
- `event_type`: String (`impression` or `click`)
- `placement`: String matching placement key
- `device_category`: String (`desktop`, `tablet`, `mobile`)
- `theme_mode`: String (`light`, `dark`)
- `session_hash`: SHA-256 hash of `ip_address . salt` to calculate unique visitor metrics without storing raw PII
- `created_at`: Indexed timestamp

## 3. Atomic Counters & Shared Hosting Efficiency
- To prevent heavy aggregations on high-traffic pages, each impression atomically increments `advertisements.impressions_count`.
- Each click atomically increments `advertisements.clicks_count`.
- The logging write operation is wrapped in a fail-safe try/catch block so that database spikes never impact public page rendering latency.

## 4. CTR Mathematical Formulation
The Click-Through Rate (CTR) represents the percentage of impressions that resulted in an advertisement click:
$$CTR = \begin{cases} \left(\frac{\text{Clicks}}{\text{Impressions}}\right) \times 100, & \text{if Impressions} > 0 \\ 0.00\%, & \text{if Impressions} = 0 \end{cases}$$

Calculated values are formatted to 2 decimal places (e.g. `2.45%`).

## 5. Reporting Dashboard (`/admin/advertisements/analytics`)
- **Period Filter:** 7 days, 30 days, 90 days, or all-time overview.
- **KPI Summary Cards:** Total Impressions, Total Clicks, Global CTR, and Total Active Campaigns.
- **Breakdown by Placement:** Impression volume and CTR ranked by placement zone.
- **Breakdown by Device & Theme:** Desktop vs Tablet vs Mobile performance, and Light vs Dark mode CTR comparison.
- **Top Performing Campaigns:** Sorted table of campaigns by conversion rate.
