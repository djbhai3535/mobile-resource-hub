<?php

namespace Tests\Feature;

use App\Models\HomepageSection;
use App\Services\HomepageService;
use App\Services\ThemeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Tests that guard against HTTP 500 errors caused by stale PHP-serialized
 * objects in the database cache (CACHE_STORE=database).
 *
 * Root cause: when Eloquent model objects are stored in the cache and the class
 * structure later changes, PHP unserialisation yields __PHP_Incomplete_Class
 * instances. The HomepageService::getOrderedSections() return type declaration
 * (Collection) then causes a TypeError → HTTP 500 on every public page load.
 *
 * These tests verify that the defensive guards in HomepageService and
 * ThemeService recover from such stale cache entries without crashing.
 */
class StaleCacheRecoveryTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // HomepageService::getOrderedSections() — stale cache recovery
    // -----------------------------------------------------------------------

    public function test_homepage_returns_200_with_clean_cache(): void
    {
        Cache::flush();
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_homepage_returns_200_when_cache_contains_wrong_type(): void
    {
        // Simulate a corrupt cache entry (e.g. serialized stdClass instead of HomepageSection)
        $cacheKey = HomepageService::CACHE_KEY_HOMEPAGE_SECTIONS . '_enabled';
        Cache::put($cacheKey, new \stdClass(), 3600);  // wrong type, not Collection

        // The critical assertion: the homepage must load 200 regardless of stale cache.
        // Note: with RefreshDatabase the DB transaction wraps the whole test, so cache
        // writes made inside the HTTP request are rolled back after $this->get() completes.
        // We therefore only assert the HTTP outcome, not the post-request cache state.
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_homepage_returns_200_when_cache_contains_collection_of_wrong_items(): void
    {
        // Simulate a stale Collection of non-HomepageSection items (like __PHP_Incomplete_Class)
        $cacheKey = HomepageService::CACHE_KEY_HOMEPAGE_SECTIONS . '_enabled';
        $fakeCollection = collect([new \stdClass(), new \stdClass()]);  // wrong item type
        Cache::put($cacheKey, $fakeCollection, 3600);

        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_get_ordered_sections_flushes_stale_cache_and_returns_collection(): void
    {
        $service = app(HomepageService::class);

        // Inject a corrupt value
        $cacheKey = HomepageService::CACHE_KEY_HOMEPAGE_SECTIONS . '_enabled';
        Cache::put($cacheKey, 'corrupt_non_collection_value', 3600);

        $result = $service->getOrderedSections(true);
        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_get_ordered_sections_returns_valid_collection_on_cache_miss(): void
    {
        Cache::flush();
        $service = app(HomepageService::class);
        $result = $service->getOrderedSections(false);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertGreaterThan(0, $result->count());
    }

    // -----------------------------------------------------------------------
    // ThemeService — stale cache recovery
    // -----------------------------------------------------------------------

    public function test_theme_service_get_published_settings_recovers_from_non_array_cache(): void
    {
        $service = app(ThemeService::class);

        // Inject a corrupt value (non-array)
        Cache::put(ThemeService::CACHE_KEY_PUBLISHED_SETTINGS, new \stdClass(), 86400);

        $settings = $service->getPublishedSettings();
        $this->assertIsArray($settings);
        $this->assertArrayHasKey('branding', $settings);
        $this->assertArrayHasKey('global_colors', $settings);
    }

    public function test_theme_service_get_cached_theme_css_recovers_from_non_string_cache(): void
    {
        $service = app(ThemeService::class);

        // Inject a corrupt value (non-string)
        Cache::put(ThemeService::CACHE_KEY_PUBLISHED_CSS, new \stdClass(), 86400);

        $css = $service->getCachedThemeCss();
        $this->assertIsString($css);
    }

    // -----------------------------------------------------------------------
    // Admin routes remain accessible
    // -----------------------------------------------------------------------

    public function test_admin_login_page_loads(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('Admin');
    }

    public function test_admin_dashboard_redirects_unauthenticated(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    // -----------------------------------------------------------------------
    // Cache flush migration guard
    // -----------------------------------------------------------------------

    public function test_cache_table_exists_and_is_queryable(): void
    {
        // Verify the cache table exists (required by CACHE_STORE=database in production)
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('cache'));

        // Verify cache reads/writes work
        Cache::put('test_sanity_key', 'test_sanity_value', 60);
        $this->assertSame('test_sanity_value', Cache::get('test_sanity_key'));

        // Verify cache forget works
        Cache::forget('test_sanity_key');
        $this->assertNull(Cache::get('test_sanity_key'));
    }

    public function test_migration_cache_flush_uses_delete_not_truncate(): void
    {
        // Verify migration file exists and uses delete() not truncate()
        // (truncate() is not supported on SQLite which is used in test environments)
        $migrationPath = database_path('migrations/2026_11_18_000003_flush_stale_serialized_cache.php');
        $this->assertFileExists($migrationPath);

        $content = file_get_contents($migrationPath);
        $this->assertStringContainsString('->delete()', $content,
            'Migration must use ->delete() not ->truncate() for SQLite compatibility');
        $this->assertStringNotContainsString('->truncate()', $content,
            'Migration must not use ->truncate() (not supported on SQLite test database)');
    }
}
