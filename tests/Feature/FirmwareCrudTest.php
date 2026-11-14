<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Device;
use App\Models\Firmware;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FirmwareCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $unauthorizedUser;
    protected Brand $brand;
    protected Device $device;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);

        $this->adminUser = User::factory()->create();
        $this->adminUser->roles()->attach(Role::where('name', 'super-admin')->first());

        $this->unauthorizedUser = User::factory()->create();
        $this->unauthorizedUser->roles()->attach(Role::where('name', 'author')->first());

        $this->brand = Brand::create([
            'name' => 'Samsung',
            'slug' => 'samsung',
            'status' => 'published',
        ]);

        $this->device = Device::create([
            'brand_id' => $this->brand->id,
            'name' => 'Galaxy S24 Ultra',
            'slug' => 'galaxy-s24-ultra',
            'market_status' => 'available',
            'status' => 'published',
        ]);
    }

    public function test_admin_can_view_firmware_index(): void
    {
        Firmware::create([
            'brand_id' => $this->brand->id,
            'device_id' => $this->device->id,
            'title' => 'Galaxy S24 Ultra Android 15 OneUI 7.0',
            'model_number' => 'SM-S928B',
            'csc' => 'OXM',
            'file_type' => 'Stock ROM',
            'archive_format' => '.tar.md5',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.firmware.index'));

        $response->assertStatus(200);
        $response->assertSee('Galaxy S24 Ultra Android 15 OneUI 7.0');
        $response->assertSee('SM-S928B');
    }

    public function test_admin_can_create_firmware_with_mirrors(): void
    {
        $payload = [
            'brand_id' => $this->brand->id,
            'device_id' => $this->device->id,
            'title' => 'Galaxy S24 Ultra OneUI 7 Test Release',
            'slug' => 'galaxy-s24-ultra-oneui-7-test',
            'model_number' => 'SM-S928B',
            'region' => 'Europe',
            'csc' => 'EUX',
            'android_version' => 'Android 15',
            'os_version' => 'One UI 7.0',
            'build_number' => 'S928BXXU3AXK2',
            'file_type' => 'Stock ROM',
            'archive_format' => '.tar.md5',
            'file_size_formatted' => '9.15 GB',
            'md5_checksum' => '4a8f93e9812b1c3d90f23a9b9872e41c',
            'sha256_checksum' => 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855',
            'distribution_permission' => 'authorized_mirror',
            'is_verified' => 1,
            'status' => 'published',
            'mirrors' => [
                [
                    'mirror_name' => 'Official CDN Server',
                    'url' => 'https://download.samsung.com/firmware/S928B.zip',
                    'is_official' => 1,
                ],
                [
                    'mirror_name' => 'Fast Cloud Mirror',
                    'url' => 'https://drive.google.com/file/d/test-firmware/view',
                    'is_official' => 0,
                ],
            ],
        ];

        $response = $this->actingAs($this->adminUser)->post(route('admin.firmware.store'), $payload);

        $response->assertRedirect(route('admin.firmware.index'));
        $this->assertDatabaseHas('firmwares', [
            'title' => 'Galaxy S24 Ultra OneUI 7 Test Release',
            'model_number' => 'SM-S928B',
            'csc' => 'EUX',
            'status' => 'published',
        ]);

        $fw = Firmware::where('slug', 'galaxy-s24-ultra-oneui-7-test')->first();
        $this->assertNotNull($fw);
        $this->assertCount(2, $fw->mirrors);
    }

    public function test_admin_can_update_firmware(): void
    {
        $firmware = Firmware::create([
            'brand_id' => $this->brand->id,
            'title' => 'Original Title',
            'model_number' => 'SM-S928B',
            'file_type' => 'Stock ROM',
            'archive_format' => '.zip',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->adminUser)->put(route('admin.firmware.update', $firmware), [
            'brand_id' => $this->brand->id,
            'title' => 'Updated Firmware Title',
            'model_number' => 'SM-S928B',
            'file_type' => 'Stock ROM',
            'archive_format' => '.tar.md5',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.firmware.index'));
        $this->assertDatabaseHas('firmwares', [
            'id' => $firmware->id,
            'title' => 'Updated Firmware Title',
            'status' => 'published',
        ]);
    }

    public function test_admin_can_soft_delete_and_restore_firmware(): void
    {
        $firmware = Firmware::create([
            'brand_id' => $this->brand->id,
            'title' => 'To Be Trashed',
            'model_number' => 'SM-S928B',
            'file_type' => 'Stock ROM',
            'archive_format' => '.zip',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'draft',
        ]);

        // Trash
        $delResponse = $this->actingAs($this->adminUser)->delete(route('admin.firmware.destroy', $firmware));
        $delResponse->assertRedirect(route('admin.firmware.index'));
        $this->assertSoftDeleted('firmwares', ['id' => $firmware->id]);

        // Restore
        $restoreResponse = $this->actingAs($this->adminUser)->post(route('admin.firmware.restore', $firmware->id));
        $restoreResponse->assertRedirect(route('admin.firmware.index', ['trashed' => 1]));
        $this->assertNotSoftDeleted('firmwares', ['id' => $firmware->id]);
    }

    public function test_public_user_can_view_published_firmware(): void
    {
        $firmware = Firmware::create([
            'brand_id' => $this->brand->id,
            'device_id' => $this->device->id,
            'title' => 'Public Live Firmware',
            'slug' => 'public-live-firmware',
            'model_number' => 'SM-S928B',
            'file_type' => 'Stock ROM',
            'archive_format' => '.tar.md5',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'published',
        ]);

        $response = $this->get(route('firmware.show', $firmware->slug));
        $response->assertStatus(200);
        $response->assertSee('Public Live Firmware');
        $response->assertSee('SM-S928B');
    }

    public function test_unauthorized_user_cannot_manage_firmware(): void
    {
        $response = $this->actingAs($this->unauthorizedUser)->get(route('admin.firmware.index'));
        $response->assertStatus(403);
    }
}
