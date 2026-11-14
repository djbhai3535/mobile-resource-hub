<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Device;
use App\Models\FlashFile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashFileCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $unauthorizedUser;
    protected Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);

        $this->adminUser = User::factory()->create();
        $this->adminUser->roles()->attach(Role::where('name', 'super-admin')->first());

        $this->unauthorizedUser = User::factory()->create();
        $this->unauthorizedUser->roles()->attach(Role::where('name', 'author')->first());

        $this->brand = Brand::create([
            'name' => 'Xiaomi',
            'slug' => 'xiaomi',
            'status' => 'published',
        ]);
    }

    public function test_admin_can_view_flash_files_index(): void
    {
        FlashFile::create([
            'brand_id' => $this->brand->id,
            'title' => 'Redmi Note 13 Pro 5G Fastboot ROM',
            'model_number' => '2312DRA50G',
            'file_type' => 'stock_rom',
            'chipset' => 'Snapdragon 7s Gen 2',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.flash-files.index'));

        $response->assertStatus(200);
        $response->assertSee('Redmi Note 13 Pro 5G Fastboot ROM');
        $response->assertSee('Snapdragon 7s Gen 2');
    }

    public function test_admin_can_create_flash_file_with_mirrors(): void
    {
        $payload = [
            'brand_id' => $this->brand->id,
            'title' => 'Redmi Note 12 Scatter File',
            'slug' => 'redmi-note-12-scatter',
            'model_number' => '2312DRA50G',
            'file_type' => 'scatter_firmware',
            'chipset' => 'MediaTek MT6785',
            'flashing_tool' => 'SP Flash Tool v6',
            'distribution_permission' => 'authorized_mirror',
            'is_verified' => 1,
            'status' => 'published',
            'mirrors' => [
                [
                    'mirror_name' => 'Fastboot Server',
                    'url' => 'https://bigota.d.miui.com/test_flash.zip',
                    'is_official' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($this->adminUser)->post(route('admin.flash-files.store'), $payload);

        $response->assertRedirect(route('admin.flash-files.index'));
        $this->assertDatabaseHas('flash_files', [
            'title' => 'Redmi Note 12 Scatter File',
            'chipset' => 'MediaTek MT6785',
            'status' => 'published',
        ]);

        $file = FlashFile::where('slug', 'redmi-note-12-scatter')->first();
        $this->assertNotNull($file);
        $this->assertCount(1, $file->mirrors);
    }

    public function test_admin_can_update_flash_file(): void
    {
        $file = FlashFile::create([
            'brand_id' => $this->brand->id,
            'title' => 'Old Flash Title',
            'model_number' => '2312DRA50G',
            'file_type' => 'scatter_firmware',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->adminUser)->put(route('admin.flash-files.update', $file), [
            'brand_id' => $this->brand->id,
            'title' => 'Updated Flash Title',
            'model_number' => '2312DRA50G',
            'file_type' => 'pac_file',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.flash-files.index'));
        $this->assertDatabaseHas('flash_files', [
            'id' => $file->id,
            'title' => 'Updated Flash Title',
            'file_type' => 'pac_file',
            'status' => 'published',
        ]);
    }

    public function test_admin_can_soft_delete_and_restore_flash_file(): void
    {
        $file = FlashFile::create([
            'brand_id' => $this->brand->id,
            'title' => 'Flash To Trash',
            'model_number' => '2312DRA50G',
            'file_type' => 'stock_rom',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'draft',
        ]);

        $delResponse = $this->actingAs($this->adminUser)->delete(route('admin.flash-files.destroy', $file));
        $delResponse->assertRedirect(route('admin.flash-files.index'));
        $this->assertSoftDeleted('flash_files', ['id' => $file->id]);

        $restoreResponse = $this->actingAs($this->adminUser)->post(route('admin.flash-files.restore', $file->id));
        $restoreResponse->assertRedirect(route('admin.flash-files.index', ['trashed' => 1]));
        $this->assertNotSoftDeleted('flash_files', ['id' => $file->id]);
    }

    public function test_public_user_can_view_published_flash_file(): void
    {
        $file = FlashFile::create([
            'brand_id' => $this->brand->id,
            'title' => 'Public Live Flash File',
            'slug' => 'public-live-flash-file',
            'model_number' => '2312DRA50G',
            'file_type' => 'scatter_firmware',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'published',
        ]);

        $response = $this->get(route('flash-files.show', $file->slug));
        $response->assertStatus(200);
        $response->assertSee('Public Live Flash File');
    }

    public function test_unauthorized_user_cannot_manage_flash_files(): void
    {
        $response = $this->actingAs($this->unauthorizedUser)->get(route('admin.flash-files.index'));
        $response->assertStatus(403);
    }
}
