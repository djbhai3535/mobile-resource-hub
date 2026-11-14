# Search Analytics & Content Demand Intelligence

## Overview
The Search Analytics module (`/admin/search-analytics`) provides administrators with complete visibility into visitor search intent, content discovery trends, zero-result search patterns, and catalog indexing health.

---

## Analytics Capabilities

### 1. Key Performance Indicators (KPIs)
- **Total Searches (30-day window)**: Aggregate count of user queries across web and autocomplete interfaces.
- **Unique Query Volume**: Count of distinct search keywords investigated by visitors.
- **Zero-Result Rate (%)**: Percentage of searches that yielded no catalog matches (`results_count == 0`). A high rate reveals content gaps that users are actively demanding.
- **Origin Breakdown**: Distribution of queries originating from `/search` (web full page) vs `/api/search/suggestions` (autocomplete).

### 2. Content Gap Discovery: Zero-Result Queries
A dedicated table highlights the most frequent queries that returned zero matches. This acts as an automated content editorial roadmap:
- If `Redmi Note 14 Pro` or `SP Flash Tool v6` appears repeatedly with 0 results, administrators can immediately prioritize drafting and publishing these specifications or download packages.

### 3. Top Popular Queries
Surfaces the most heavily searched keywords along with average returned result counts.

### 4. Search Request Audit Log
A detailed, filterable log displaying:
- Timestamp
- Search query string
- Target category tab
- Origin channel
- Results count (color-coded: green for results > 0, red badge for 0)
- Deep link to preview the exact live search results page

---

## Privacy Architecture
- **Zero Personal Identifiable Information (PII)**: Visitor names, emails, and full raw IP addresses are never persisted in `search_logs`.
- **Hashed Session Identifier**: Requests are grouped using an anonymized SHA-256 session hash: `hash('sha256', request()->ip() . '_mrh_search_salt')`.
- Compliant with GDPR, CCPA, and global privacy standards.
