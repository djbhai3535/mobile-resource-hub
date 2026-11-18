<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Device;
use App\Models\Driver;
use App\Models\Firmware;
use App\Models\FlashFile;
use App\Models\HomepageSection;
use App\Models\Post;
use App\Models\SoftwareTool;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class HomepageService
{
    public const CACHE_KEY_HOMEPAGE_SECTIONS = 'mrh_homepage_sections_ordered';

    /**
     * Default catalog section blueprints.
     */
    public function getDefaultSections(): array
    {
        return [
            [
                'section_key' => 'hero',
                'title' => 'Search Specs, Firmware, & Drivers',
                'subtitle' => 'Verified catalog of technical specifications, stock ROMs, scatter files, and USB drivers for Android devices.',
                'is_enabled' => true,
                'sort_order' => 1,
                'item_count' => 1,
                'display_style' => 'default',
                'background_style' => 'glass',
            ],
            [
                'section_key' => 'popular_brands',
                'title' => 'Browse by Brand',
                'subtitle' => 'Select a manufacturer to explore devices, stock ROMs, and official drivers.',
                'is_enabled' => true,
                'sort_order' => 2,
                'item_count' => 12,
                'display_style' => 'grid',
                'background_style' => 'default',
            ],
            [
                'section_key' => 'latest_devices',
                'title' => 'Latest Mobile Devices',
                'subtitle' => 'Recently added smartphones and detailed technical hardware specifications.',
                'is_enabled' => true,
                'sort_order' => 3,
                'item_count' => 8,
                'display_style' => 'grid',
                'background_style' => 'default',
            ],
            [
                'section_key' => 'featured_devices',
                'title' => 'Featured Flagships',
                'subtitle' => 'Trending devices with comprehensive specifications and downloads.',
                'is_enabled' => true,
                'sort_order' => 4,
                'item_count' => 4,
                'display_style' => 'grid',
                'background_style' => 'subtle',
            ],
            [
                'section_key' => 'latest_firmware',
                'title' => 'Recent Firmware & Flash Files',
                'subtitle' => 'Official stock ROMs with verified SHA-256 checksums and regional CSC builds.',
                'is_enabled' => true,
                'sort_order' => 5,
                'item_count' => 6,
                'display_style' => 'grid',
                'background_style' => 'default',
            ],
            [
                'section_key' => 'flash_files',
                'title' => 'Tested Flash Files (ROMs)',
                'subtitle' => 'Scatter files, PAC files, and factory unbrick packages verified on test hardware.',
                'is_enabled' => true,
                'sort_order' => 6,
                'item_count' => 6,
                'display_style' => 'grid',
                'background_style' => 'default',
            ],
            [
                'section_key' => 'software_tools',
                'title' => 'Essential Flash Tools',
                'subtitle' => 'Official utilities for flashing, unbricking, and backing up Android hardware.',
                'is_enabled' => true,
                'sort_order' => 7,
                'item_count' => 4,
                'display_style' => 'grid',
                'background_style' => 'subtle',
            ],
            [
                'section_key' => 'usb_drivers',
                'title' => 'Official USB Drivers',
                'subtitle' => 'Signed ADB, Fastboot, VCOM, and manufacturer USB drivers for Windows.',
                'is_enabled' => true,
                'sort_order' => 8,
                'item_count' => 4,
                'display_style' => 'grid',
                'background_style' => 'default',
            ],
            [
                'section_key' => 'tech_news',
                'title' => 'Guides, Fixes & Tech News',
                'subtitle' => 'Latest Android OS rollout schedules, security bulletins, and technical updates.',
                'is_enabled' => true,
                'sort_order' => 9,
                'item_count' => 4,
                'display_style' => 'grid',
                'background_style' => 'default',
            ],
            [
                'section_key' => 'tutorials',
                'title' => 'Step-by-Step Repair Tutorials',
                'subtitle' => 'In-depth recovery tutorials written and verified by mobile technicians.',
                'is_enabled' => true,
                'sort_order' => 10,
                'item_count' => 4,
                'display_style' => 'grid',
                'background_style' => 'subtle',
            ],
            [
                'section_key' => 'ad_middle',
                'title' => 'Advertisement Slot',
                'subtitle' => null,
                'is_enabled' => true,
                'sort_order' => 11,
                'item_count' => 1,
                'display_style' => 'ad',
                'background_style' => 'default',
            ],
        ];
    }

    /**
     * Ensure default homepage sections exist in database.
     */
    public function ensureDefaultsExist(): void
    {
        $count = HomepageSection::count();
        if ($count === 0) {
            foreach ($this->getDefaultSections() as $sec) {
                HomepageSection::create($sec);
            }
        }
    }

    /**
     * Alias for ensureDefaultsExist.
     */
    public function ensureDefaultSections(): void
    {
        $this->ensureDefaultsExist();
    }

    /**
     * Get ordered sections list.
     *
     * Defensive against stale/corrupt cache entries (e.g. __PHP_Incomplete_Class
     * caused by serialized Eloquent objects stored before a class rename/refactor).
     * On any deserialization failure the corrupted cache keys are flushed and a
     * fresh query is executed so the application never returns HTTP 500 due to
     * cache-layer problems.
     */
    public function getOrderedSections(bool $enabledOnly = true): Collection
    {
        $this->ensureDefaultsExist();

        $cacheKey = $enabledOnly ? self::CACHE_KEY_HOMEPAGE_SECTIONS . '_enabled' : self::CACHE_KEY_HOMEPAGE_SECTIONS . '_all';

        // First attempt: read from cache
        try {
            $cached = Cache::get($cacheKey);

            // Guard: a valid cached value must be a Collection of HomepageSection models.
            // If the cache contains __PHP_Incomplete_Class instances (stale serialized data)
            // the Collection items will not be instances of HomepageSection.
            if ($cached !== null) {
                if (!($cached instanceof Collection)) {
                    throw new \RuntimeException('Cached value is not a Collection — flushing stale cache.');
                }

                // Check each item is a proper Eloquent model (not __PHP_Incomplete_Class)
                foreach ($cached as $item) {
                    if (!($item instanceof HomepageSection)) {
                        throw new \RuntimeException('Cached Collection contains non-HomepageSection items — flushing stale cache.');
                    }
                }

                return $cached;
            }
        } catch (\Throwable $e) {
            // Stale or corrupt cache — flush all homepage section cache keys
            $this->clearCache();
        }

        // Fresh DB query (cache miss or after flush)
        $fresh = $this->fetchFromDatabase($enabledOnly);

        try {
            Cache::put($cacheKey, $fresh, 3600);
        } catch (\Throwable $e) {
            // Cache write failure must never crash the public page
        }

        return $fresh;
    }

    /**
     * Execute the raw database query for homepage sections.
     */
    private function fetchFromDatabase(bool $enabledOnly): Collection
    {
        $query = HomepageSection::ordered();
        if ($enabledOnly) {
            $query->enabled();
        }
        return $query->get();
    }

    /**
     * Reorder sections.
     */
    public function reorder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            HomepageSection::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        $this->clearCache();
    }

    /**
     * Update section settings.
     */
    public function updateSection(int $id, array $data): HomepageSection
    {
        $section = HomepageSection::findOrFail($id);
        $section->update($data);
        $this->clearCache();
        return $section;
    }

    /**
     * Toggle section enabled/disabled status.
     */
    public function toggleSection(int $id): bool
    {
        $section = HomepageSection::findOrFail($id);
        $section->is_enabled = !$section->is_enabled;
        $section->save();
        $this->clearCache();
        return $section->is_enabled;
    }

    /**
     * Hydrate section with real database content.
     */
    public function hydrateSectionData(HomepageSection $section): mixed
    {
        $count = max(1, $section->item_count);

        switch ($section->section_key) {
            case 'popular_brands':
                return Brand::where('status', 'active')
                    ->withCount('devices')
                    ->orderByDesc('devices_count')
                    ->orderBy('name')
                    ->take($count)
                    ->get();

            case 'latest_devices':
                return Device::published()
                    ->with(['brand', 'modelNumbers'])
                    ->latest('id')
                    ->take($count)
                    ->get();

            case 'featured_devices':
                $featured = Device::published()
                    ->where('is_featured', true)
                    ->with(['brand', 'modelNumbers'])
                    ->latest('id')
                    ->take($count)
                    ->get();

                if ($featured->isEmpty()) {
                    // Fallback to top latest devices
                    $featured = Device::published()
                        ->with(['brand', 'modelNumbers'])
                        ->latest('id')
                        ->take($count)
                        ->get();
                }
                return $featured;

            case 'latest_firmware':
                return Firmware::published()
                    ->with(['brand', 'device'])
                    ->latest('id')
                    ->take($count)
                    ->get();

            case 'flash_files':
                return FlashFile::published()
                    ->with(['brand', 'device'])
                    ->latest('id')
                    ->take($count)
                    ->get();

            case 'software_tools':
                return SoftwareTool::published()
                    ->latest('id')
                    ->take($count)
                    ->get();

            case 'usb_drivers':
                return Driver::published()
                    ->with('brand')
                    ->latest('id')
                    ->take($count)
                    ->get();

            case 'tech_news':
                return Post::published()
                    ->where(function ($q) {
                        $q->whereNull('type')->orWhere('type', 'post')->orWhere('type', 'news');
                    })
                    ->with(['author', 'category'])
                    ->latest('published_at')
                    ->take($count)
                    ->get();

            case 'tutorials':
                return Post::published()
                    ->where('type', 'tutorial')
                    ->with(['author', 'category'])
                    ->latest('published_at')
                    ->take($count)
                    ->get();

            default:
                return collect([]);
        }
    }

    /**
     * Clear homepage cache.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_HOMEPAGE_SECTIONS . '_enabled');
        Cache::forget(self::CACHE_KEY_HOMEPAGE_SECTIONS . '_all');
    }
}
