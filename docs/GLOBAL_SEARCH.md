# Global Search Architecture — Mobile Resource Hub

## Overview
The Mobile Resource Hub global search engine enables instant discovery of published content across the entire technical catalog. It is engineered specifically for deployment on shared hosting environments (Hostinger cPanel/PHP-FPM/MySQL) with **zero background daemons**, **zero memory hogs**, and **zero external services** (no Elasticsearch, Redis, Meilisearch server, Docker, or persistent queue workers).

---

## Technical Specifications

### 1. Dual-Tier Index Structure
Search combines two synchronized database layers:
1. **Denormalized Document Table (`search_documents`)**:
   - Holds consolidated textual content, titles, slugs, URLs, publication statuses, model codes, and brand names.
   - Enables fast phrase containment queries (`LIKE %phrase%`) and direct exact-match boosts on titles, model numbers, and brands.
2. **Inverted Token Index (`search_document_terms`)**:
   - Breaks document contents into normalized tokens (minimum 2 characters, preserving alphanumeric tokens like `5G`, `G99`, `MT6769`, `SM-S928B`, `Odin`).
   - Assigns field-based relevance weights.
   - Computes relevance scoring via SQL aggregation: `SUM(weight * occurrence_count)`.

---

## Relevance Scoring Weight Hierarchy

| Field / Concept | Weight | Purpose |
| :--- | :---: | :--- |
| **Model Code / Sub-tokens** | **100** | Exact technical identifiers (`SM-S928B`, `S928B`, `24030PN60G`, `GEC77`) |
| **Title / Product Name** | **80** | Primary commercial and release titles |
| **Brand Name** | **70** | Manufacturer references (`Samsung`, `Xiaomi`, `Google`) |
| **Specifications & Metadata** | **40** | Chipset, display panel, Android version, build number, CSC, SoC |
| **Headings & Excerpt** | **30** | Summary overviews and section headings |
| **Categories & Tags** | **25** | Editorial taxonomy classifications |
| **Full Body Content / Guides** | **15** | Deep text body, instructions, release notes, changelogs |

### Dynamic Boost Multipliers
When evaluating candidate records against the user query:
- **Exact Title Match**: +500 points
- **Exact Model Number Match**: +600 points
- **Title Prefix Match**: +250 points
- **Substring Title Match**: +150 points
- **Model Code Substring Match**: +300 points
- **All Query Tokens Present Bonus**: +100 points
- **Normalized Content Substring Match**: +80 points

---

## Query Tokenization & Normalization

1. **HTML Stripping & Decoding**: Strips all HTML formatting, decodes HTML entities into Unicode characters.
2. **Case Normalization**: Converts all text to lowercase using UTF-8 `mb_strtolower`.
3. **Punctuation Filtering**: Replaces non-alphanumeric characters with spaces while preserving hyphens and underscores within alphanumeric tokens.
4. **Hardware Model Expansion**:
   - Original: `SM-S928B`
   - Stripped: `sms928b`
   - Sub-tokens: `sm`, `s928b`, `s928`
5. **No Fulltext Stopword Omission**: Unlike MySQL default `FULLTEXT` which drops words like `about`, `for`, `one`, `how`, and short tokens under 4 letters, our inverted index indexes terms down to 2 characters, ensuring queries like `One UI 7`, `5G`, and `How to root` return exact results.

---

## User Interface & Features

1. **Category Filter Tabs**:
   - `All Results`, `Devices`, `Firmware`, `Flash Files`, `Tools`, `Drivers`, `News`, `Tutorials`, `Brands`, `Pages`.
   - Dynamic counter badge for each category.
2. **Keyword Highlighting**:
   - Escapes HTML entities safely.
   - Wraps matched query tokens in `<mark class="bg-warning-subtle text-dark fw-semibold px-1 rounded">`.
3. **Centered Snippet Generation**:
   - Locates the first occurrence of query tokens in the body and crops a ~220-character preview window centered around the match.
4. **Live Autocomplete API**:
   - `GET /api/search/suggestions?q={query}`
   - Debounced frontend calls (250ms), keyboard navigation (Up/Down/Enter/Escape), grouped by content category.
5. **Privacy-Conscious Search Logging**:
   - Logs query, category, results count, origin (`web`, `api`), and an anonymized session hash (`sha256(ip + salt)`).
   - Zero PII stored.
