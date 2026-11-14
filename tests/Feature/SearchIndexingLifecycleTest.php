<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceSpecificationValue;
use App\Models\Firmware;
use App\Models\Post;
use App\Models\SearchDocument;
use App\Models\SearchDocumentTerm;
use App\Models\SpecificationField;
use App\Models\SpecificationGroup;
use App\Models\User;
use App\Services\SearchIndexingService;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchIndexingLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AdminUserSeeder::class);
    }

    public function test_post_creation_automatically_indexes_document(): void
    {
        $author = User::first();

        $post = Post::create([
            'author_id' => $author->id,
            'title' => 'How to Unlock Bootloader on Xiaomi HyperOS',
            'slug' => 'unlock-bootloader-xiaomi-hyperos',
            'content' => 'Complete walkthrough for enabling OEM unlocking in developer options.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->assertDatabaseHas('search_documents', [
            'searchable_type' => Post::class,
            'searchable_id' => $post->id,
            'title' => 'How to Unlock Bootloader on Xiaomi HyperOS',
            'publication_status' => 'published',
        ]);

        $doc = SearchDocument::where('searchable_type', Post::class)->where('searchable_id', $post->id)->first();
        $this->assertNotNull($doc);

        $this->assertDatabaseHas('search_document_terms', [
            'search_document_id' => $doc->id,
            'normalized_term' => 'bootloader',
        ]);
    }

    public function test_unpublishing_or_trashing_post_removes_it_from_search_index(): void
    {
        $author = User::first();

        $post = Post::create([
            'author_id' => $author->id,
            'title' => 'Temporary Secret Post',
            'slug' => 'temporary-secret-post',
            'content' => 'Confidential technical information.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->assertDatabaseHas('search_documents', [
            'searchable_id' => $post->id,
        ]);

        // Unpublish
        $post->update(['status' => 'draft']);

        $this->assertDatabaseMissing('search_documents', [
            'searchable_id' => $post->id,
        ]);

        // Re-publish
        $post->update(['status' => 'published']);
        $this->assertDatabaseHas('search_documents', [
            'searchable_id' => $post->id,
        ]);

        // Soft delete
        $post->delete();
        $this->assertDatabaseMissing('search_documents', [
            'searchable_id' => $post->id,
        ]);
    }

    public function test_updating_article_removes_stale_word_from_search(): void
    {
        $author = User::first();

        $post = Post::create([
            'author_id' => $author->id,
            'title' => 'Fastboot Flashing Instructions',
            'slug' => 'fastboot-flashing-instructions',
            'content' => 'Execute the temporaryUniqueStaleKeyword778 partition command.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Verify initial word is searchable
        $respInitial = $this->get('/search?q=temporaryUniqueStaleKeyword778');
        $respInitial->assertStatus(200);
        $respInitial->assertSee('Fastboot Flashing Instructions');

        // Update post removing the unique word
        $post->update([
            'content' => 'Execute the standard vendor flash partition command.',
        ]);

        // Verify stale word disappears completely from search results
        $respStale = $this->get('/search?q=temporaryUniqueStaleKeyword778');
        $respStale->assertStatus(200);
        $respStale->assertSee('No matching results found');
        $respStale->assertDontSee('Fastboot Flashing Instructions');

        // Verify new word is searchable
        $respNew = $this->get('/search?q=vendor');
        $respNew->assertStatus(200);
        $respNew->assertSee('Fastboot Flashing Instructions');
    }

    public function test_rebuild_artisan_command_indexes_all_content_idempotently(): void
    {
        $brand = Brand::create(['name' => 'OnePlus', 'slug' => 'oneplus', 'status' => 'active']);
        Device::create([
            'brand_id' => $brand->id,
            'name' => 'OnePlus 13',
            'slug' => 'oneplus-13',
            'status' => 'published',
        ]);

        $this->artisan('search:index-rebuild')
            ->expectsOutputToContain('Total Documents')
            ->assertExitCode(0);

        $initialDocCount = SearchDocument::count();
        $initialTermCount = SearchDocumentTerm::count();

        // Run rebuild a second time - must be strictly idempotent
        $this->artisan('search:index-rebuild')
            ->assertExitCode(0);

        $this->assertEquals($initialDocCount, SearchDocument::count());
        $this->assertEquals($initialTermCount, SearchDocumentTerm::count());
    }

    public function test_status_artisan_command_renders_table(): void
    {
        $this->artisan('search:index-status')
            ->expectsOutputToContain('Search Index Status Overview')
            ->expectsOutputToContain('Total Indexed Documents')
            ->assertExitCode(0);
    }

    public function test_device_custom_specification_value_is_searchable(): void
    {
        $brand = Brand::create(['name' => 'Sony', 'slug' => 'sony', 'status' => 'active']);
        $device = Device::create([
            'brand_id' => $brand->id,
            'name' => 'Xperia Pro 1',
            'slug' => 'xperia-pro-1',
            'status' => 'published',
        ]);

        $group = SpecificationGroup::create([
            'name' => 'Optics',
            'slug' => 'optics',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $field = SpecificationField::create([
            'group_id' => $group->id,
            'label' => 'Sensor Type',
            'key' => 'optics_sensor_type',
            'field_type' => 'text',
            'is_active' => true,
        ]);

        DeviceSpecificationValue::create([
            'device_id' => $device->id,
            'field_id' => $field->id,
            'value' => 'ExmorRS1InchUniqueSensor',
        ]);

        // Trigger indexing synchronization
        app(SearchIndexingService::class)->sync($device->fresh());

        $response = $this->get('/search?q=ExmorRS1InchUniqueSensor');
        $response->assertStatus(200);
        $response->assertSee('Xperia Pro 1');
    }

    public function test_firmware_build_number_and_csc_are_searchable(): void
    {
        $brand = Brand::create(['name' => 'Samsung', 'slug' => 'samsung', 'status' => 'active']);
        $device = Device::create([
            'brand_id' => $brand->id,
            'name' => 'Galaxy S25 Ultra',
            'slug' => 'galaxy-s25-ultra',
            'status' => 'published',
        ]);

        $firmware = Firmware::create([
            'brand_id' => $brand->id,
            'device_id' => $device->id,
            'title' => 'Galaxy S25 Ultra OneUI 7 Official',
            'slug' => 's25-ultra-oneui7-s938bxxu1axb1',
            'model_number' => 'SM-S938B',
            'build_number' => 'S938BXXU1AXB1',
            'csc' => 'EUX_UNIQUE_CSC',
            'android_version' => '15.0',
            'status' => 'published',
        ]);

        // Verify build number search
        $respBuild = $this->get('/search?q=S938BXXU1AXB1');
        $respBuild->assertStatus(200);
        $respBuild->assertSee('Galaxy S25 Ultra OneUI 7 Official');

        // Verify CSC search
        $respCsc = $this->get('/search?q=EUX_UNIQUE_CSC');
        $respCsc->assertStatus(200);
        $respCsc->assertSee('Galaxy S25 Ultra OneUI 7 Official');
    }
}
