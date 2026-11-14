<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModelNumber;
use App\Services\SearchIndexingService;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchAutocompleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AdminUserSeeder::class);
    }

    public function test_short_query_returns_empty_suggestion_groups(): void
    {
        $response = $this->getJson('/api/search/suggestions?q=a');

        $response->assertStatus(200);
        $response->assertJson([
            'devices' => [],
            'firmware' => [],
            'flash_files' => [],
            'software' => [],
            'drivers' => [],
            'posts' => [],
        ]);
    }

    public function test_autocomplete_returns_matching_devices_and_model_numbers(): void
    {
        $brand = Brand::create(['name' => 'Google', 'slug' => 'google', 'status' => 'active']);
        $device = Device::create([
            'brand_id' => $brand->id,
            'name' => 'Pixel 9 Pro XL',
            'slug' => 'pixel-9-pro-xl',
            'status' => 'published',
        ]);
        DeviceModelNumber::create([
            'device_id' => $device->id,
            'model_number' => 'GEC77',
            'region' => 'Global',
            'is_primary' => true,
        ]);

        app(SearchIndexingService::class)->sync($device);

        // Search by name prefix
        $respName = $this->getJson('/api/search/suggestions?q=Pixel');
        $respName->assertStatus(200);
        $respName->assertJsonFragment(['name' => 'Pixel 9 Pro XL']);

        // Search by model code
        $respModel = $this->getJson('/api/search/suggestions?q=GEC77');
        $respModel->assertStatus(200);
        $respModel->assertJsonFragment(['model_code' => 'GEC77']);
    }
}
