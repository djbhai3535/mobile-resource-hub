# Search Indexing Lifecycle & Rebuild Operations

## Architectural Principle: Automatic Lifecycle Synchronization
In Mobile Resource Hub, content published anywhere in the application **automatically becomes searchable immediately without manual administrator intervention**.

There are **no cron jobs required for new content to appear in search**, and **no long-running queue workers needed**.

---

## Domain Event Listeners (`SearchIndexingHook`)

The application dispatches lifecycle events whenever content is saved, published, unpublished, restored, or deleted:

| Event | Model | Indexing Action |
| :--- | :--- | :--- |
| `PostSaved` | `Post` | Indexes if published; deletes from index if draft or scheduled. |
| `PostDeleted` | `Post` | Removes document and all associated terms from inverted index. |
| `PageSaved` | `Page` | Indexes published static CMS pages. |
| `BrandSaved` | `Brand` | Indexes active brand portal records. |
| `BrandDeleted` | `Brand` | Removes brand document from index. |
| `DeviceSaved` | `Device` | Gathers brand name, model codes, variants, and dynamic specs and indexes. |
| `DeviceDeleted` | `Device` | Removes device document from index. |
| `FirmwareSaved` | `Firmware` | Indexes stock ROM metadata (CSC, PDA, Android version, region, instructions). |
| `FirmwareDeleted` | `Firmware` | Removes firmware from search index. |
| `FlashFileSaved` | `FlashFile` | Indexes scatter/PAC/flash file metadata (chipset, tool, instructions). |
| `FlashFileDeleted` | `FlashFile` | Removes flash file from search index. |
| `SoftwareSaved` | `SoftwareTool` | Indexes tool description, OS compatibility, system requirements, instructions. |
| `SoftwareDeleted` | `SoftwareTool` | Removes tool from search index. |
| `DriverSaved` | `Driver` | Indexes USB driver type, supported devices, Windows OS versions, guide. |
| `DriverDeleted` | `Driver` | Removes driver from search index. |

All event subscriptions are bound in `App\Listeners\SearchIndexingHook`, which is registered as an Event Subscriber in `EventServiceProvider` / Laravel 13 auto-discovery.

---

## Command Line Operations

### 1. Full Search Index Rebuild
```bash
php artisan search:index-rebuild
```
- Performs a chunked (50 records per batch) database scan across all 8 searchable models.
- Completely idempotent: running repeatedly produces consistent document and term counts.
- Updates or inserts `search_documents`, clears and re-inserts `search_document_terms`.
- Output displays an aligned status table with document counts by content type, total inverted terms, and duration.

### 2. Search Index Health Status
```bash
php artisan search:index-status
```
- Summarizes documents count grouped by `content_type` and `publication_status`.
- Displays total terms indexed, total recorded user searches, and zero-result search count.

---

## Transactional Integrity
Every document indexing operation is wrapped in a database transaction (`DB::transaction(...)`):
1. `SearchDocument::updateOrCreate(...)`
2. `SearchDocumentTerm::where('search_document_id', $document->id)->delete()`
3. Batch insert of all new terms via `array_chunk($termRows, 200)`
Foreign key `CASCADE ON DELETE` ensures that deleting a `SearchDocument` immediately deletes all associated terms.
