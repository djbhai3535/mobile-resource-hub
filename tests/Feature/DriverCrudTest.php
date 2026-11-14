<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Driver;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverCrudTest extends TestCase
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
            'name' => 'Samsung',
            'slug' => 'samsung',
            'status' => 'published',
        ]);
    }

    public function test_admin_can_view_drivers_index(): void
    {
        Driver::create([
            'brand_id' => $this->brand->id,
            'name' => 'Samsung Android USB Driver',
            'driver_version' => 'v1.7.59.0',
            'file_type' => 'EXE Installer',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.drivers.index'));

        $response->assertStatus(200);
        $response->assertSee('Samsung Android USB Driver');
        $response->assertSee('v1.7.59.0');
    }

    public function test_admin_can_create_driver_with_mirrors(): void
    {
        $payload = [
            'brand_id' => $this->brand->id,
            'name' => 'Qualcomm QDLoader 9008 Driver',
            'slug' => 'qualcomm-9008-test',
            'driver_version' => 'v2.1.0.5',
            'file_type' => 'EXE Installer',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'published',
            'mirrors' => [
                [
                    'mirror_name' => 'Primary Driver Mirror',
                    'url' => 'https://drive.google.com/file/d/test-driver/view',
                    'is_official' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($this->adminUser)->post(route('admin.drivers.store'), $payload);

        $response->assertRedirect(route('admin.drivers.index'));
        $this->assertDatabaseHas('drivers', [
            'name' => 'Qualcomm QDLoader 9008 Driver',
            'driver_version' => 'v2.1.0.5',
            'status' => 'published',
        ]);

        $driver = Driver::where('slug', 'qualcomm-9008-test')->first();
        $this->assertNotNull($driver);
        $this->assertCount(1, $driver->mirrors);
    }

    public function test_admin_can_update_driver(): void
    {
        $driver = Driver::create([
            'brand_id' => $this->brand->id,
            'name' => 'Old Driver Name',
            'driver_version' => 'v1.0',
            'file_type' => 'EXE Installer',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->adminUser)->put(route('admin.drivers.update', $driver), [
            'brand_id' => $this->brand->id,
            'name' => 'Updated Driver Name',
            'driver_version' => 'v1.5',
            'file_type' => 'INF Driver Archive',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.drivers.index'));
        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'name' => 'Updated Driver Name',
            'driver_version' => 'v1.5',
            'file_type' => 'INF Driver Archive',
            'status' => 'published',
        ]);
    }

    public function test_admin_can_soft_delete_and_restore_driver(): void
    {
        $driver = Driver::create([
            'brand_id' => $this->brand->id,
            'name' => 'Driver To Trash',
            'file_type' => 'EXE Installer',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'draft',
        ]);

        $delResponse = $this->actingAs($this->adminUser)->delete(route('admin.drivers.destroy', $driver));
        $delResponse->assertRedirect(route('admin.drivers.index'));
        $this->assertSoftDeleted('drivers', ['id' => $driver->id]);

        $restoreResponse = $this->actingAs($this->adminUser)->post(route('admin.drivers.restore', $driver->id));
        $restoreResponse->assertRedirect(route('admin.drivers.index', ['trashed' => 1]));
        $this->assertNotSoftDeleted('drivers', ['id' => $driver->id]);
    }

    public function test_public_user_can_view_published_driver(): void
    {
        $driver = Driver::create([
            'brand_id' => $this->brand->id,
            'name' => 'Public Live Driver',
            'slug' => 'public-live-driver',
            'file_type' => 'EXE Installer',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'published',
        ]);

        $response = $this->get(route('drivers.show', $driver->slug));
        $response->assertStatus(200);
        $response->assertSee('Public Live Driver');
    }

    public function test_unauthorized_user_cannot_manage_drivers(): void
    {
        $response = $this->actingAs($this->unauthorizedUser)->get(route('admin.drivers.index'));
        $response->assertStatus(403);
    }
}
