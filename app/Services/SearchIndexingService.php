<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Device;
use App\Models\Driver;
use App\Models\Firmware;
use App\Models\FlashFile;
use App\Models\Page;
use App\Models\Post;
use App\Models\SearchDocument;
use App\Models\SearchDocumentTerm;
use App\Models\SoftwareTool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SearchIndexingService
{
    /**
     * Term weights by field type.
     */
    public const WEIGHT_MODEL = 100;
    public const WEIGHT_TITLE = 80;
    public const WEIGHT_BRAND = 70;
    public const WEIGHT_SPEC = 40;
    public const WEIGHT_EXCERPT = 30;
    public const WEIGHT_TAG = 25;
    public const WEIGHT_BODY = 15;

    /**
     * Synchronize a model with the search index.
     */
    public function sync(Model $model): ?SearchDocument
    {
        // If model has soft deletes and is trashed, or is unpublished, remove it
        if (method_exists($model, 'trashed') && $model->trashed()) {
            $this->remove($model);
            return null;
        }

        if (!$this->isModelPublished($model)) {
            $this->remove($model);
            return null;
        }

        $payload = $this->extractPayload($model);
        if (!$payload) {
            $this->remove($model);
            return null;
        }

        return DB::transaction(function () use ($model, $payload) {
            $document = SearchDocument::updateOrCreate(
                [
                    'searchable_type' => get_class($model),
                    'searchable_id' => $model->id,
                ],
                [
                    'content_type' => $payload['content_type'],
                    'title' => $payload['title'],
                    'slug' => $payload['slug'],
                    'excerpt' => $payload['excerpt'],
                    'normalized_content' => $payload['normalized_content'],
                    'searchable_url' => $payload['searchable_url'],
                    'model_code' => $payload['model_code'],
                    'brand_name' => $payload['brand_name'],
                    'publication_status' => 'published',
                    'published_at' => $payload['published_at'] ?? now(),
                    'source_updated_at' => $model->updated_at ?? now(),
                    'indexed_at' => now(),
                ]
            );

            // Delete old terms
            SearchDocumentTerm::where('search_document_id', $document->id)->delete();

            // Prepare terms for batch insertion
            $termRows = [];
            $now = now();

            foreach ($payload['terms'] as $term => $meta) {
                $termRows[] = [
                    'search_document_id' => $document->id,
                    'normalized_term' => Str::limit($term, 100, ''),
                    'field_name' => $meta['field'],
                    'weight' => $meta['weight'],
                    'occurrence_count' => $meta['count'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($termRows)) {
                // Chunk insert to respect SQL parameter limits
                foreach (array_chunk($termRows, 200) as $chunk) {
                    SearchDocumentTerm::insert($chunk);
                }
            }

            return $document;
        });
    }

    /**
     * Remove a model from the search index.
     */
    public function remove(Model $model): void
    {
        SearchDocument::where('searchable_type', get_class($model))
            ->where('searchable_id', $model->id)
            ->delete();
    }

    /**
     * Check if a model is currently published.
     */
    public function isModelPublished(Model $model): bool
    {
        if (method_exists($model, 'isPublished')) {
            return $model->isPublished();
        }

        if (isset($model->status)) {
            return in_array($model->status, ['published', 'active'], true);
        }

        return true;
    }

    /**
     * Rebuild entire search index across all content types.
     */
    public function rebuildAll(?callable $progressCallback = null): array
    {
        $startTime = microtime(true);
        $counts = [
            'brands' => 0,
            'devices' => 0,
            'firmware' => 0,
            'flash_files' => 0,
            'software' => 0,
            'drivers' => 0,
            'posts' => 0,
            'pages' => 0,
            'total_documents' => 0,
            'total_terms' => 0,
        ];

        // 1. Brands
        Brand::chunk(50, function ($brands) use (&$counts, $progressCallback) {
            foreach ($brands as $brand) {
                if ($this->sync($brand)) {
                    $counts['brands']++;
                }
            }
            if ($progressCallback) {
                $progressCallback('brands', $counts['brands']);
            }
        });

        // 2. Devices
        Device::with(['brand', 'modelNumbers', 'variants', 'specificationValues.field.group'])
            ->chunk(50, function ($devices) use (&$counts, $progressCallback) {
                foreach ($devices as $device) {
                    if ($this->sync($device)) {
                        $counts['devices']++;
                    }
                }
                if ($progressCallback) {
                    $progressCallback('devices', $counts['devices']);
                }
            });

        // 3. Firmware
        Firmware::with(['brand', 'device'])
            ->chunk(50, function ($firmwares) use (&$counts, $progressCallback) {
                foreach ($firmwares as $firmware) {
                    if ($this->sync($firmware)) {
                        $counts['firmware']++;
                    }
                }
                if ($progressCallback) {
                    $progressCallback('firmware', $counts['firmware']);
                }
            });

        // 4. Flash Files
        FlashFile::with(['brand', 'device'])
            ->chunk(50, function ($flashFiles) use (&$counts, $progressCallback) {
                foreach ($flashFiles as $flashFile) {
                    if ($this->sync($flashFile)) {
                        $counts['flash_files']++;
                    }
                }
                if ($progressCallback) {
                    $progressCallback('flash_files', $counts['flash_files']);
                }
            });

        // 5. Software Tools
        SoftwareTool::chunk(50, function ($softwareTools) use (&$counts, $progressCallback) {
            foreach ($softwareTools as $tool) {
                if ($this->sync($tool)) {
                    $counts['software']++;
                }
            }
            if ($progressCallback) {
                $progressCallback('software', $counts['software']);
            }
        });

        // 6. Drivers
        Driver::with('brand')->chunk(50, function ($drivers) use (&$counts, $progressCallback) {
            foreach ($drivers as $driver) {
                if ($this->sync($driver)) {
                    $counts['drivers']++;
                }
            }
            if ($progressCallback) {
                $progressCallback('drivers', $counts['drivers']);
            }
        });

        // 7. Posts (News & Tutorials)
        Post::with(['category', 'tags'])->chunk(50, function ($posts) use (&$counts, $progressCallback) {
            foreach ($posts as $post) {
                if ($this->sync($post)) {
                    $counts['posts']++;
                }
            }
            if ($progressCallback) {
                $progressCallback('posts', $counts['posts']);
            }
        });

        // 8. Pages
        Page::chunk(50, function ($pages) use (&$counts, $progressCallback) {
            foreach ($pages as $page) {
                if ($this->sync($page)) {
                    $counts['pages']++;
                }
            }
            if ($progressCallback) {
                $progressCallback('pages', $counts['pages']);
            }
        });

        $counts['total_documents'] = SearchDocument::count();
        $counts['total_terms'] = SearchDocumentTerm::count();
        $counts['duration_seconds'] = round(microtime(true) - $startTime, 3);

        return $counts;
    }

    /**
     * Extract searchable payload and terms for a given model.
     */
    protected function extractPayload(Model $model): ?array
    {
        if ($model instanceof Post) {
            return $this->extractPostPayload($model);
        }

        if ($model instanceof Page) {
            return $this->extractPagePayload($model);
        }

        if ($model instanceof Brand) {
            return $this->extractBrandPayload($model);
        }

        if ($model instanceof Device) {
            return $this->extractDevicePayload($model);
        }

        if ($model instanceof Firmware) {
            return $this->extractFirmwarePayload($model);
        }

        if ($model instanceof FlashFile) {
            return $this->extractFlashFilePayload($model);
        }

        if ($model instanceof SoftwareTool) {
            return $this->extractSoftwarePayload($model);
        }

        if ($model instanceof Driver) {
            return $this->extractDriverPayload($model);
        }

        return null;
    }

    /**
     * Post Payload
     */
    protected function extractPostPayload(Post $post): array
    {
        $terms = [];
        $title = $post->title;
        $excerpt = $post->excerpt ?: Str::limit(strip_tags($post->content), 200);
        $body = strip_tags($post->content);

        $this->addTerms($terms, $title, 'title', self::WEIGHT_TITLE);
        if ($excerpt) {
            $this->addTerms($terms, $excerpt, 'excerpt', self::WEIGHT_EXCERPT);
        }
        $this->addTerms($terms, $body, 'body', self::WEIGHT_BODY);

        // Tags & Categories
        $tagNames = $post->tags->pluck('name')->implode(' ');
        $categoryName = $post->category?->name ?? '';
        $this->addTerms($terms, $tagNames . ' ' . $categoryName, 'tag', self::WEIGHT_TAG);

        $normalizedContent = $this->normalizeText("{$title} {$excerpt} {$categoryName} {$tagNames} {$body}");

        $url = $post->type === 'tutorial'
            ? route('tutorials.show', $post->slug)
            : route('news.show', $post->slug);

        return [
            'content_type' => $post->type === 'tutorial' ? 'tutorial' : 'post',
            'title' => $title,
            'slug' => $post->slug,
            'excerpt' => $excerpt,
            'normalized_content' => $normalizedContent,
            'searchable_url' => $url,
            'model_code' => null,
            'brand_name' => null,
            'published_at' => $post->published_at ?? now(),
            'terms' => $terms,
        ];
    }

    /**
     * Page Payload
     */
    protected function extractPagePayload(Page $page): array
    {
        $terms = [];
        $title = $page->title;
        $body = strip_tags($page->content);
        $excerpt = Str::limit($body, 200);

        $this->addTerms($terms, $title, 'title', self::WEIGHT_TITLE);
        $this->addTerms($terms, $body, 'body', self::WEIGHT_BODY);

        $normalizedContent = $this->normalizeText("{$title} {$body}");
        $url = url('/' . ltrim($page->slug, '/'));

        return [
            'content_type' => 'page',
            'title' => $title,
            'slug' => $page->slug,
            'excerpt' => $excerpt,
            'normalized_content' => $normalizedContent,
            'searchable_url' => $url,
            'model_code' => null,
            'brand_name' => null,
            'published_at' => $page->created_at ?? now(),
            'terms' => $terms,
        ];
    }

    /**
     * Brand Payload
     */
    protected function extractBrandPayload(Brand $brand): array
    {
        $terms = [];
        $title = $brand->name;
        $excerpt = $brand->description ?: "Official mobile phone specifications, firmware, flash files, and drivers for {$brand->name}.";
        $body = strip_tags($brand->description ?? '');

        $this->addTerms($terms, $title, 'brand', self::WEIGHT_BRAND);
        $this->addTerms($terms, $body, 'body', self::WEIGHT_BODY);

        $normalizedContent = $this->normalizeText("{$title} {$excerpt} {$body}");
        $url = url('/brands/' . $brand->slug);

        return [
            'content_type' => 'brand',
            'title' => $title,
            'slug' => $brand->slug,
            'excerpt' => $excerpt,
            'normalized_content' => $normalizedContent,
            'searchable_url' => $url,
            'model_code' => null,
            'brand_name' => $brand->name,
            'published_at' => $brand->created_at ?? now(),
            'terms' => $terms,
        ];
    }

    /**
     * Device Payload
     */
    protected function extractDevicePayload(Device $device): array
    {
        $terms = [];
        $title = $device->name;
        $brandName = $device->brand?->name ?? '';

        $this->addTerms($terms, $title, 'title', self::WEIGHT_TITLE);
        if ($brandName) {
            $this->addTerms($terms, $brandName, 'brand', self::WEIGHT_BRAND);
        }

        // Model numbers
        $models = [];
        if ($device->relationLoaded('modelNumbers')) {
            $models = $device->modelNumbers->pluck('model_number')->all();
        } else {
            $models = $device->modelNumbers()->pluck('model_number')->all();
        }

        $primaryModel = $models[0] ?? null;
        foreach ($models as $m) {
            $this->addModelTerms($terms, $m);
        }

        // Variants
        $variants = [];
        if ($device->relationLoaded('variants')) {
            $variants = $device->variants->pluck('name')->all();
        } else {
            $variants = $device->variants()->pluck('name')->all();
        }
        foreach ($variants as $v) {
            $this->addTerms($terms, $v, 'spec', self::WEIGHT_SPEC);
        }

        // Specifications
        $specsText = '';
        $specValues = $device->relationLoaded('specificationValues')
            ? $device->specificationValues
            : $device->specificationValues()->with('field.group')->get();

        foreach ($specValues as $val) {
            $fieldName = $val->field?->label ?? $val->field?->key ?? '';
            $fieldVal = $val->value ?? '';
            $specsText .= " {$fieldName} {$fieldVal}";
        }
        $this->addTerms($terms, $specsText, 'spec', self::WEIGHT_SPEC);

        $excerpt = $device->meta_description ?: "{$title} specifications, model numbers " . implode(', ', array_slice($models, 0, 4)) . ", firmware and driver downloads.";

        $normalizedContent = $this->normalizeText("{$brandName} {$title} " . implode(' ', $models) . " " . implode(' ', $variants) . " {$specsText}");
        $url = url('/mobile-phones/' . $device->slug);

        return [
            'content_type' => 'device',
            'title' => $title,
            'slug' => $device->slug,
            'excerpt' => $excerpt,
            'normalized_content' => $normalizedContent,
            'searchable_url' => $url,
            'model_code' => $primaryModel,
            'brand_name' => $brandName,
            'published_at' => $device->created_at ?? now(),
            'terms' => $terms,
        ];
    }

    /**
     * Firmware Payload
     */
    protected function extractFirmwarePayload(Firmware $firmware): array
    {
        $terms = [];
        $title = $firmware->title;
        $brandName = $firmware->brand?->name ?? '';
        $deviceName = $firmware->device?->name ?? '';
        $modelNumber = $firmware->model_number ?? '';

        $this->addTerms($terms, $title, 'title', self::WEIGHT_TITLE);
        if ($brandName) {
            $this->addTerms($terms, $brandName, 'brand', self::WEIGHT_BRAND);
        }
        if ($deviceName) {
            $this->addTerms($terms, $deviceName, 'title', self::WEIGHT_TITLE);
        }
        if ($modelNumber) {
            $this->addModelTerms($terms, $modelNumber);
        }

        // Technical metadata
        $techMeta = "{$firmware->android_version} {$firmware->build_number} {$firmware->region} {$firmware->csc} {$firmware->pda} {$firmware->os_version}";
        $this->addTerms($terms, $techMeta, 'spec', self::WEIGHT_SPEC);

        if ($firmware->changelog) {
            $this->addTerms($terms, strip_tags($firmware->changelog), 'body', self::WEIGHT_BODY);
        }
        if ($firmware->instructions) {
            $this->addTerms($terms, strip_tags($firmware->instructions), 'body', self::WEIGHT_BODY);
        }

        $excerpt = "Official stock firmware update for {$title} ({$modelNumber}), Android {$firmware->android_version}, Region: {$firmware->region} ({$firmware->csc}).";
        $normalizedContent = $this->normalizeText("{$brandName} {$deviceName} {$title} {$modelNumber} {$techMeta} " . strip_tags($firmware->instructions ?? '') . ' ' . strip_tags($firmware->changelog ?? ''));
        $url = route('firmware.show', $firmware->slug);

        return [
            'content_type' => 'firmware',
            'title' => $title,
            'slug' => $firmware->slug,
            'excerpt' => $excerpt,
            'normalized_content' => $normalizedContent,
            'searchable_url' => $url,
            'model_code' => $modelNumber,
            'brand_name' => $brandName,
            'published_at' => $firmware->release_date ?? $firmware->created_at ?? now(),
            'terms' => $terms,
        ];
    }

    /**
     * Flash File Payload
     */
    protected function extractFlashFilePayload(FlashFile $flashFile): array
    {
        $terms = [];
        $title = $flashFile->title;
        $brandName = $flashFile->brand?->name ?? '';
        $deviceName = $flashFile->device?->name ?? '';
        $modelNumber = $flashFile->model_number ?? '';

        $this->addTerms($terms, $title, 'title', self::WEIGHT_TITLE);
        if ($brandName) {
            $this->addTerms($terms, $brandName, 'brand', self::WEIGHT_BRAND);
        }
        if ($deviceName) {
            $this->addTerms($terms, $deviceName, 'title', self::WEIGHT_TITLE);
        }
        if ($modelNumber) {
            $this->addModelTerms($terms, $modelNumber);
        }

        $techMeta = "{$flashFile->chipset} {$flashFile->flashing_tool} {$flashFile->android_version} {$flashFile->file_version} {$flashFile->package_type}";
        $this->addTerms($terms, $techMeta, 'spec', self::WEIGHT_SPEC);

        if ($flashFile->instructions) {
            $this->addTerms($terms, strip_tags($flashFile->instructions), 'body', self::WEIGHT_BODY);
        }

        $excerpt = "Tested flash file (ROM) for {$title} ({$modelNumber}). Chipset: {$flashFile->chipset}, Flash tool: {$flashFile->flashing_tool}.";
        $normalizedContent = $this->normalizeText("{$brandName} {$deviceName} {$title} {$modelNumber} {$techMeta} " . strip_tags($flashFile->instructions ?? ''));
        $url = route('flash-files.show', $flashFile->slug);

        return [
            'content_type' => 'flash_file',
            'title' => $title,
            'slug' => $flashFile->slug,
            'excerpt' => $excerpt,
            'normalized_content' => $normalizedContent,
            'searchable_url' => $url,
            'model_code' => $modelNumber,
            'brand_name' => $brandName,
            'published_at' => $flashFile->created_at ?? now(),
            'terms' => $terms,
        ];
    }

    /**
     * Software Tool Payload
     */
    protected function extractSoftwarePayload(SoftwareTool $tool): array
    {
        $terms = [];
        $title = $tool->name;
        $developer = $tool->developer ?? '';

        $this->addTerms($terms, $title, 'title', self::WEIGHT_TITLE);
        if ($developer) {
            $this->addTerms($terms, $developer, 'brand', self::WEIGHT_BRAND);
        }

        $meta = "{$tool->category} {$tool->version} {$tool->license} {$tool->os_compatibility}";
        $this->addTerms($terms, $meta, 'spec', self::WEIGHT_SPEC);

        if ($tool->description) {
            $this->addTerms($terms, strip_tags($tool->description), 'body', self::WEIGHT_BODY);
        }
        if ($tool->system_requirements) {
            $this->addTerms($terms, strip_tags($tool->system_requirements), 'spec', self::WEIGHT_SPEC);
        }
        if ($tool->instructions) {
            $this->addTerms($terms, strip_tags($tool->instructions), 'body', self::WEIGHT_BODY);
        }

        $excerpt = Str::limit(strip_tags($tool->description ?? "Download {$title} official flashing and service tool."), 200);
        $normalizedContent = $this->normalizeText("{$title} {$developer} {$meta} " . strip_tags($tool->description ?? '') . ' ' . strip_tags($tool->instructions ?? ''));
        $url = route('software.show', $tool->slug);

        return [
            'content_type' => 'software',
            'title' => $title,
            'slug' => $tool->slug,
            'excerpt' => $excerpt,
            'normalized_content' => $normalizedContent,
            'searchable_url' => $url,
            'model_code' => null,
            'brand_name' => $developer,
            'published_at' => $tool->created_at ?? now(),
            'terms' => $terms,
        ];
    }

    /**
     * Driver Payload
     */
    protected function extractDriverPayload(Driver $driver): array
    {
        $terms = [];
        $title = $driver->name;
        $brandName = $driver->brand?->name ?? '';

        $this->addTerms($terms, $title, 'title', self::WEIGHT_TITLE);
        if ($brandName) {
            $this->addTerms($terms, $brandName, 'brand', self::WEIGHT_BRAND);
        }

        $meta = "{$driver->driver_type} {$driver->driver_version} {$driver->supported_devices} {$driver->windows_versions}";
        $this->addTerms($terms, $meta, 'spec', self::WEIGHT_SPEC);

        if ($driver->installation_guide) {
            $this->addTerms($terms, strip_tags($driver->installation_guide), 'body', self::WEIGHT_BODY);
        }

        $excerpt = Str::limit(strip_tags($driver->installation_guide ?? "Official USB Driver for {$title}."), 200);
        $normalizedContent = $this->normalizeText("{$title} {$brandName} {$meta} " . strip_tags($driver->installation_guide ?? ''));
        $url = route('drivers.show', $driver->slug);

        return [
            'content_type' => 'driver',
            'title' => $title,
            'slug' => $driver->slug,
            'excerpt' => $excerpt,
            'normalized_content' => $normalizedContent,
            'searchable_url' => $url,
            'model_code' => null,
            'brand_name' => $brandName,
            'published_at' => $driver->created_at ?? now(),
            'terms' => $terms,
        ];
    }

    /**
     * Tokenize text into words / terms (minimum 2 chars).
     */
    public function tokenize(string $text): array
    {
        $cleaned = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $cleaned = mb_strtolower($cleaned, 'UTF-8');

        // Replace punctuation except hyphens and alphanumeric
        $cleaned = preg_replace('/[^\p{L}\p{N}\-_]+/u', ' ', $cleaned);

        $tokens = preg_split('/\s+/', $cleaned, -1, PREG_SPLIT_NO_EMPTY);
        $result = [];

        foreach ($tokens as $token) {
            $t = trim($token, '-_');
            if (mb_strlen($t, 'UTF-8') >= 2) {
                $result[] = $t;
            }
        }

        return array_values(array_unique($result));
    }

    /**
     * Normalize entire text content block for full-text phrase scanning.
     */
    public function normalizeText(string $text): string
    {
        $cleaned = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $cleaned = mb_strtolower($cleaned, 'UTF-8');
        return trim(preg_replace('/\s+/', ' ', $cleaned));
    }

    /**
     * Add model code terms (handles SM-S928B, SMS928B, S928B, SM, MT6769, etc.).
     */
    protected function addModelTerms(array &$terms, string $model): void
    {
        $model = trim($model);
        if (empty($model)) {
            return;
        }

        $lower = mb_strtolower($model, 'UTF-8');
        $stripped = preg_replace('/[^a-z0-9]/i', '', $lower);

        $variants = [$lower, $stripped];

        // If contains hyphen or underscore, split sub-tokens
        if (preg_match('/[-_]/', $lower)) {
            $parts = preg_split('/[-_]+/', $lower);
            foreach ($parts as $part) {
                if (strlen($part) >= 2) {
                    $variants[] = $part;
                }
            }
        }

        foreach (array_unique($variants) as $term) {
            if (strlen($term) >= 2) {
                if (!isset($terms[$term])) {
                    $terms[$term] = [
                        'field' => 'model',
                        'weight' => self::WEIGHT_MODEL,
                        'count' => 1,
                    ];
                } else {
                    $terms[$term]['weight'] = max($terms[$term]['weight'], self::WEIGHT_MODEL);
                    $terms[$term]['count']++;
                }
            }
        }
    }

    /**
     * Helper to append terms into accumulator array.
     */
    protected function addTerms(array &$terms, string $text, string $fieldName, int $weight): void
    {
        $tokens = $this->tokenize($text);
        foreach ($tokens as $token) {
            if (!isset($terms[$token])) {
                $terms[$token] = [
                    'field' => $fieldName,
                    'weight' => $weight,
                    'count' => 1,
                ];
            } else {
                $terms[$token]['weight'] = max($terms[$token]['weight'], $weight);
                $terms[$token]['count']++;
            }
        }
    }
}
