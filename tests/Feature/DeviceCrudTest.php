<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Device;
use App\Models\Role;
use App\Models\SpecificationField;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
        $this->seed(\Database\Seeders\SpecificationSeeder::class);

        $this->adminUser = User::factory()->create();
        $this->adminUser->roles()->attach(Role::where('name', 'super-admin')->first());

        $this->brand = Brand::create([
            'name' => 'OnePlus',
            'slug' => 'oneplus',
            'status' => 'published',
        ]);
    }

    public function test_admin_can_create_device_with_models_and_variants(): void
    {
        $techField = SpecificationField::where('key', 'network_technology')->first();

        $postData = [
            'brand_id' => $this->brand->id,
            'name' => 'OnePlus 12',
            'marketing_name' => 'OnePlus 12 5G Flagship',
            'slug' => 'oneplus-12',
            'device_category' => 'smartphone',
            'status' => 'published',
            'market_status' => 'available',
            'os' => 'Android',
            'os_version' => 'Android 14, OxygenOS 14',
            'short_description' => 'Snapdragon 8 Gen 3 flagship with Hasselblad cameras.',
            'model_numbers' => [
                ['model_number' => 'CPH2581', 'region' => 'Global', 'is_primary' => true],
                ['model_number' => 'CPH2583', 'region' => 'India', 'is_primary' => false],
            ],
            'variants' => [
                ['name' => '256GB / 12GB RAM', 'ram' => '12GB', 'internal_storage' => '256GB', 'color' => 'Silky Black', 'notes' => '$799'],
                ['name' => '512GB / 16GB RAM', 'ram' => '16GB', 'internal_storage' => '512GB', 'color' => 'Flowy Emerald', 'notes' => '$899'],
            ],
            'specs' => [
                $techField->id => 'GSM / CDMA / HSPA / LTE / 5G',
            ],
        ];

        $response = $this->actingAs($this->adminUser)->post(route('admin.devices.store'), $postData);

        $response->assertRedirect(route('admin.devices.index'));

        $this->assertDatabaseHas('devices', [
            'name' => 'OnePlus 12',
            'slug' => 'oneplus-12',
            'status' => 'published',
            'market_status' => 'available',
        ]);

        $device = Device::where('slug', 'oneplus-12')->first();
        $this->assertCount(2, $device->modelNumbers);
        $this->assertCount(2, $device->variants);
        $this->assertDatabaseHas('device_specification_values', [
            'device_id' => $device->id,
            'field_id' => $techField->id,
            'value' => 'GSM / CDMA / HSPA / LTE / 5G',
        ]);
    }

    public function test_publication_status_independent_of_market_status(): void
    {
        // Upcoming unannounced phone published on website
        $upcoming = Device::create([
            'brand_id' => $this->brand->id,
            'name' => 'OnePlus 13 Rumored',
            'slug' => 'oneplus-13-rumored',
            'device_category' => 'smartphone',
            'status' => 'published', // Published on site
            'market_status' => 'upcoming', // Commercial status
        ]);

        // Discontinued legacy phone published on site for archive
        $discontinued = Device::create([
            'brand_id' => $this->brand->id,
            'name' => 'OnePlus One',
            'slug' => 'oneplus-one',
            'device_category' => 'smartphone',
            'status' => 'published',
            'market_status' => 'discontinued',
        ]);

        $this->assertEquals('published', $upcoming->status);
        $this->assertEquals('upcoming', $upcoming->market_status);

        $this->assertEquals('published', $discontinued->status);
        $this->assertEquals('discontinued', $discontinued->market_status);
    }

    public function test_device_soft_delete_and_restore(): void
    {
        $device = Device::create([
            'brand_id' => $this->brand->id,
            'name' => 'Device To Delete',
            'slug' => 'device-to-delete',
            'device_category' => 'smartphone',
            'status' => 'draft',
            'market_status' => 'available',
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.devices.destroy', $device));
        $response->assertRedirect(route('admin.devices.index'));

        $this->assertSoftDeleted('devices', ['id' => $device->id]);

        $restoreResponse = $this->actingAs($this->adminUser)->post(route('admin.devices.restore', $device->id));
        $restoreResponse->assertRedirect(route('admin.devices.index', ['trashed' => 1]));

        $this->assertDatabaseHas('devices', [
            'id' => $device->id,
            'deleted_at' => null,
        ]);
    }

    public function test_public_device_detail_page_loads(): void
    {
        $device = Device::create([
            'brand_id' => $this->brand->id,
            'name' => 'OnePlus Open',
            'marketing_name' => 'OnePlus Open Foldable',
            'slug' => 'oneplus-open',
            'device_category' => 'smartphone',
            'status' => 'published',
            'market_status' => 'available',
            'os' => 'Android',
            'os_version' => 'Android 13, OxygenOS 13.2',
        ]);

        $response = $this->get(route('public.device.detail', 'oneplus-open'));
        $response->assertStatus(200);
        $response->assertSee('OnePlus Open Foldable');
        $response->assertSee('OnePlus');
    }
}
