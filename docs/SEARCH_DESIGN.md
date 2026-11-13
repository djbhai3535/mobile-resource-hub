# Mobile Resource Hub — Search Engine Design Specification

## 1. Problem Statement & Shared Hosting Constraints
Standard search solutions on VPS or dedicated servers rely on external persistent background engines (such as Elasticsearch, Meilisearch, or Redis). In a shared hosting environment (Hostinger Unlimited tier), long-running daemon processes are killed by host process monitors, and MySQL FULLTEXT defaults suffer from two major flaws:
1. **Minimum Token Length:** Omits short technical model codes (e.g., `G9`, `X5`, `M2`, `A15`).
2. **Stop-word Lists:** Silently drops common technical terms (e.g., `one`, `plus`, `for`, `about`, `new`).

## 2. Search Engine Architecture: Dual-Index Strategy
To ensure 100% search completeness, instant freshness upon publication, and sub-50ms execution on Hostinger MySQL:

```
[Content Published / Updated]
         │
         ▼
[SearchIndexService::sync(Model)]
         │
         ├───► Write/Update `search_documents` row
         │     (Stores unified canonical record, titles, model code, normalized plain text)
         │
         └───► Tokenize & Normalize
               │
               ├── Split on spaces and punctuation into word tokens
               ├── Retain alphanumeric codes (e.g. "SM-S928B" -> ["sm-s928b", "sms928b", "s928b", "sm", "s928"])
               ├── Calculate token importance weight (Title=10, ModelCode=15, Body=2)
               │
               ▼
         Write to `search_terms` (Indexed by `(term, search_document_id)`)
```

## 3. Query Execution & Relevance Ranking
When a user searches for query `$q`:
1. **Normalization:** The query is lowercased and stripped of dangerous characters. Sub-tokens and exact expressions are extracted.
2. **Candidate Selection:**
   - Exact model code match (highest weight: 100 points)
   - Exact title match (90 points)
   - Token intersection match via `search_terms` join (50 points per matching term)
   - Prefix match for live autocomplete (`term LIKE 'sams%'`)
3. **Draft & Privacy Protection:**
   - Query strictly enforces `WHERE is_published = 1`.
   - Admin-only or unpublished draft entities can never appear in public queries.

## 4. Benchmark & Acceptance Scenarios
- **Unique Body Word Test:** A unique term in an article body appears in search immediately after publishing.
- **Model Code Normalization:** Searching `SM-S928B` or `SMS928B` finds the Samsung Galaxy S24 Ultra immediately.
- **Short Identifier Support:** 2-character identifiers (e.g., `K8`, `G9`) are matched accurately without stop-word dropouts.
