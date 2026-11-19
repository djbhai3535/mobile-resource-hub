<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Device;
use App\Models\PageView;
use App\Models\SearchLog;
use App\Models\User;
use App\Services\SearchIndexingService;
use App\Services\SearchService;
use App\Services\VisitorAnalyticsService;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StepTwoModulesTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $this->adminUser = User::where('email', 'admin@mobilehub.local')->first();
    }

    /**
     * 1. Test visitor tracking and unique visitor counting.
     */
    public function test_visitor_tracking_records_public_page_view(): void
    {
        PageView::truncate();

        $response = $this->get('/about-us', [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('page_views', [
            'path' => '/about-us',
            'device_type' => 'desktop',
        ]);
    }

    /**
     * 2. Test excluding admin traffic and known bots.
     */
    public function test_tracking_excludes_admin_traffic_and_known_bots(): void
    {
        PageView::truncate();

        // Admin visit must NOT be recorded
        $this->actingAs($this->adminUser)->get('/admin/dashboard');
        $this->assertDatabaseMissing('page_views', [
            'path' => '/admin/dashboard',
        ]);

        // Known bot user-agent must NOT be recorded
        $this->get('/about-us', [
            'User-Agent' => 'Googlebot/2.1 (+http://www.google.com/bot.html)',
        ]);
        $this->assertDatabaseCount('page_views', 0);
    }

    /**
     * 3. Test public aggregate statistics JSON endpoint.
     */
    public function test_public_live_analytics_endpoint_returns_json(): void
    {
        $response = $this->getJson('/api/analytics/live');

        $response->assertOk()
            ->assertJsonStructure([
                'live_visitors',
                'today_visitors',
                'monthly_visitors',
                'total_visitors',
            ]);
    }

    /**
     * 4. Test daily and monthly analytics aggregations.
     */
    public function test_visitor_analytics_service_aggregations(): void
    {
        PageView::truncate();

        PageView::create([
            'visitor_hash' => 'hash_test_1',
            'ip_hash' => 'ip_hash_1',
            'path' => '/test-page',
            'device_type' => 'mobile',
            'visited_date' => now()->toDateString(),
            'created_at' => now(),
        ]);

        \Illuminate\Support\Facades\Cache::flush();

        $service = app(VisitorAnalyticsService::class);
        $analytics = $service->getDashboardAnalytics('30d');

        $this->assertGreaterThanOrEqual(1, $analytics['total_page_views']);
        $this->assertGreaterThanOrEqual(1, $analytics['total_unique_visitors']);
        $this->assertNotEmpty($analytics['daily_trend']);
    }

    /**
     * 5. Test brand creation and duplicate prevention.
     */
    public function test_brand_duplicate_prevention(): void
    {
        $initialCount = Brand::count();

        // Create first brand
        $brand = Brand::firstOrCreate(
            ['slug' => 'brand-unique-test'],
            ['name' => 'Brand Unique Test', 'status' => 'published']
        );
        $this->assertNotNull($brand->id);

        // Try creating with same slug
        $duplicate = Brand::firstOrCreate(
            ['slug' => 'brand-unique-test'],
            ['name' => 'Brand Unique Test Duplicate', 'status' => 'published']
        );
        $this->assertEquals($brand->id, $duplicate->id);
        $this->assertEquals($initialCount + 1, Brand::count());
    }

    /**
     * 6. Test device-to-brand relationship.
     */
    public function test_device_belongs_to_brand(): void
    {
        $brand = Brand::firstOrCreate(
            ['slug' => 'test-brand-rel'],
            ['name' => 'Test Brand Rel', 'status' => 'published']
        );

        $device = Device::create([
            'brand_id' => $brand->id,
            'name' => 'Test Device Pro',
            'slug' => 'test-device-pro-' . uniqid(),
            'device_category' => 'smartphone',
            'status' => 'published',
            'market_status' => 'available',
        ]);

        $this->assertEquals($brand->id, $device->brand->id);
        $this->assertTrue($brand->devices->contains($device));
    }

    /**
     * 7. Test brand-specific public device listings.
     */
    public function test_brand_detail_shows_only_its_devices(): void
    {
        $brandA = Brand::firstOrCreate(
            ['slug' => 'brand-alpha'],
            ['name' => 'Brand Alpha', 'status' => 'published']
        );
        $brandB = Brand::firstOrCreate(
            ['slug' => 'brand-beta'],
            ['name' => 'Brand Beta', 'status' => 'published']
        );

        $devA = Device::create([
            'brand_id' => $brandA->id,
            'name' => 'Alpha Phone One',
            'slug' => 'alpha-phone-one',
            'device_category' => 'smartphone',
            'status' => 'published',
            'market_status' => 'available',
        ]);

        $devB = Device::create([
            'brand_id' => $brandB->id,
            'name' => 'Beta Phone One',
            'slug' => 'beta-phone-one',
            'device_category' => 'smartphone',
            'status' => 'published',
            'market_status' => 'available',
        ]);

        $response = $this->get('/brands/' . $brandA->slug);
        $response->assertOk();
        $response->assertSee('Alpha Phone One');
        $response->assertDontSee('Beta Phone One');
    }

    /**
     * 8. Test View All Brands page.
     */
    public function test_brands_directory_lists_all_brands(): void
    {
        $this->seed(\Database\Seeders\CompleteMobileBrandsSeeder::class);

        $response = $this->get('/brands');
        $response->assertOk();
        $response->assertSee('Mobile Brand Directory');
        $response->assertSee('Samsung');
        $response->assertSee('Apple');
    }

    /**
     * 9. Test global search across content types.
     */
    public function test_global_search_finds_indexed_content(): void
    {
        $brand = Brand::firstOrCreate(
            ['slug' => 'brand-search-test'],
            ['name' => 'QuantumBrand', 'status' => 'published']
        );

        $indexer = app(SearchIndexingService::class);
        $indexer->sync($brand);

        $searchService = app(SearchService::class);
        $result = $searchService->search('QuantumBrand');

        $this->assertGreaterThanOrEqual(1, $result['total_results']);
        $this->assertEquals('QuantumBrand', $result['paginator']->items()[0]->title);
    }

    /**
     * 10. Test unpublished content is excluded from search results.
     */
    public function test_search_excludes_draft_and_unpublished_content(): void
    {
        $draftBrand = Brand::create([
            'name' => 'SecretDraftBrand',
            'slug' => 'secret-draft-brand',
            'status' => 'draft',
        ]);

        $indexer = app(SearchIndexingService::class);
        $indexer->sync($draftBrand);

        $searchService = app(SearchService::class);
        $result = $searchService->search('SecretDraftBrand');

        $this->assertEquals(0, $result['total_results']);
    }

    /**
     * 11. Test search analytics logging and zero-result tracking.
     */
    public function test_search_logs_queries_and_zero_results(): void
    {
        $uniqueTerm = 'nonexistent_device_query_' . uniqid();

        $searchService = app(SearchService::class);
        $searchService->search($uniqueTerm);

        $this->assertDatabaseHas('search_logs', [
            'query' => $uniqueTerm,
            'results_count' => 0,
        ]);
    }

    /**
     * 12. Test 3D Hero component rendering fallback.
     */
    public function test_3d_hero_renders_on_homepage(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('smartphone-3d-container');
        $response->assertSee('hero-visitor-stats-bar');
        $response->assertSee('Verified Mobile Technology');
    }
}
