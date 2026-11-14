<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModelNumber;
use App\Models\Firmware;
use App\Models\Post;
use App\Models\SearchDocument;
use App\Models\SpecificationField;
use App\Models\SpecificationGroup;
use App\Models\User;
use App\Services\SearchIndexingService;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    protected SearchIndexingService $indexer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AdminUserSeeder::class);
        $this->indexer = app(SearchIndexingService::class);
    }

    public function test_unique_body_word_in_published_article_is_searchable(): void
    {
        $author = User::first();

        // Create a post with a completely distinct technical keyword
        $uniqueKeyword = 'SupercalifragilisticXRay789';
        $post = Post::create([
            'author_id' => $author->id,
            'title' => 'Advanced Bootloader Diagnostic Guide',
            'slug' => 'bootloader-diagnostic-guide',
            'content' => "<p>During low-level partition repair, inspect the {$uniqueKeyword} memory block.</p>",
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Verify indexing hook indexed it
        $this->indexer->sync($post);

        $response = $this->get('/search?q=' . $uniqueKeyword);

        $response->assertStatus(200);
        $response->assertSee('Bootloader Diagnostic Guide');
        $response->assertSee('<mark', false);
        $response->assertSee($uniqueKeyword);
    }

    public function test_exact_model_number_and_short_tokens_are_searchable(): void
    {
        $brand = Brand::create([
            'name' => 'Apex Mobile',
            'slug' => 'apex-mobile',
            'status' => 'active',
        ]);

        $device = Device::create([
            'brand_id' => $brand->id,
            'name' => 'Apex Pro 5G',
            'slug' => 'apex-pro-5g',
            'status' => 'published',
            'release_date' => now()->toDateString(),
        ]);

        DeviceModelNumber::create([
            'device_id' => $device->id,
            'model_number' => 'APX-7000Z',
            'region' => 'Global',
            'is_primary' => true,
        ]);

        $this->indexer->sync($device);

        // 1. Search full model code
        $respModel = $this->get('/search?q=APX-7000Z');
        $respModel->assertStatus(200);
        $respModel->assertSee('Apex Pro 5G');

        // 2. Search short model sub-token "7000Z"
        $respSub = $this->get('/search?q=7000Z');
        $respSub->assertStatus(200);
        $respSub->assertSee('Apex Pro 5G');

        // 3. Search short token "5G"
        $resp5g = $this->get('/search?q=5G');
        $resp5g->assertStatus(200);
        $resp5g->assertSee('Apex Pro');
        $resp5g->assertSee('apex-pro-5g');
    }

    public function test_dynamic_specifications_are_searchable(): void
    {
        $brand = Brand::create([
            'name' => 'Nova Corp',
            'slug' => 'nova-corp',
            'status' => 'active',
        ]);

        $device = Device::create([
            'brand_id' => $brand->id,
            'name' => 'Nova Ultra 12',
            'slug' => 'nova-ultra-12',
            'status' => 'published',
        ]);

        $group = SpecificationGroup::create(['name' => 'Hardware', 'slug' => 'hardware', 'sort_order' => 1]);
        $field = SpecificationField::create([
            'group_id' => $group->id,
            'label' => 'Chipset',
            'key' => 'chipset',
            'field_type' => 'text',
        ]);

        $device->specificationValues()->create([
            'field_id' => $field->id,
            'value' => 'MediaTek Dimensity 9300-Plus Ultra',
        ]);

        $this->indexer->sync($device);

        $response = $this->get('/search?q=Dimensity');
        $response->assertStatus(200);
        $response->assertSee('Nova Ultra 12');
    }

    public function test_category_filtering_tabs_isolate_content_types(): void
    {
        $brand = Brand::create(['name' => 'Titan', 'slug' => 'titan', 'status' => 'active']);
        $device = Device::create([
            'brand_id' => $brand->id,
            'name' => 'Titan Prime',
            'slug' => 'titan-prime',
            'status' => 'published',
        ]);

        $firmware = Firmware::create([
            'brand_id' => $brand->id,
            'device_id' => $device->id,
            'title' => 'Titan Prime Android 15 Stock Firmware',
            'slug' => 'titan-prime-android-15-fw',
            'model_number' => 'TP-900',
            'build_number' => 'TP_V1',
            'status' => 'published',
        ]);

        $this->indexer->sync($device);
        $this->indexer->sync($firmware);

        // Search with type=firmware
        $respFw = $this->get('/search?q=Titan&type=firmware');
        $respFw->assertStatus(200);
        $respFw->assertSee('Prime Android 15 Stock Firmware');
        $respFw->assertSee('Stock Firmware');
        $respFw->assertDontSee('Smartphone Spec');

        // Search with type=device
        $respDev = $this->get('/search?q=Titan&type=device');
        $respDev->assertStatus(200);
        $respDev->assertSee('Smartphone Spec');
        $respDev->assertDontSee('Stock Firmware');
    }

    public function test_zero_results_renders_helpful_fallback(): void
    {
        $response = $this->get('/search?q=NonExistentKeywordXYZ999');

        $response->assertStatus(200);
        $response->assertSee('No matching results found');
        $response->assertSee('NonExistentKeywordXYZ999');
    }

    public function test_search_results_page_has_noindex_follow_robots_tag(): void
    {
        $response = $this->get('/search?q=Samsung');

        $response->assertStatus(200);
        $response->assertSee('<meta name="robots" content="noindex, follow">', false);
    }
}
