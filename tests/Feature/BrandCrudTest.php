<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Device;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $authorUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
        $this->seed(\Database\Seeders\SpecificationSeeder::class);

        $this->adminUser = User::factory()->create();
        $this->adminUser->roles()->attach(Role::where('name', 'super-admin')->first());

        $this->authorUser = User::factory()->create();
        $this->authorUser->roles()->attach(Role::where('name', 'author')->first());
    }

    public function test_admin_can_view_brand_index(): void
    {
        Brand::create([
            'name' => 'Sony',
            'slug' => 'sony',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.brands.index'));

        $response->assertStatus(200);
        $response->assertSee('Sony');
    }

    public function test_admin_can_create_brand(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.brands.store'), [
            'name' => 'Nokia Mobile',
            'short_description' => 'Iconic Finnish phone company.',
            'country_of_origin' => 'Finland',
            'status' => 'published',
            'sort_order' => 10,
        ]);

        $response->assertRedirect(route('admin.brands.index'));
        $this->assertDatabaseHas('brands', [
            'name' => 'Nokia Mobile',
            'slug' => 'nokia-mobile',
            'country_of_origin' => 'Finland',
            'status' => 'published',
        ]);
    }

    public function test_admin_can_update_brand(): void
    {
        $brand = Brand::create([
            'name' => 'Motorola Original',
            'slug' => 'motorola-original',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->adminUser)->put(route('admin.brands.update', $brand), [
            'name' => 'Motorola Mobility',
            'slug' => 'motorola',
            'country_of_origin' => 'United States',
            'status' => 'published',
            'sort_order' => 5,
        ]);

        $response->assertRedirect(route('admin.brands.index'));
        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'name' => 'Motorola Mobility',
            'slug' => 'motorola',
            'status' => 'published',
        ]);
    }

    public function test_brand_deletion_prevented_if_devices_attached(): void
    {
        $brand = Brand::create([
            'name' => 'Asus',
            'slug' => 'asus',
            'status' => 'published',
        ]);

        Device::create([
            'brand_id' => $brand->id,
            'name' => 'ROG Phone 8',
            'slug' => 'asus-rog-phone-8',
            'status' => 'published',
            'market_status' => 'available',
            'device_category' => 'smartphone',
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.brands.destroy', $brand));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'deleted_at' => null,
        ]);
    }

    public function test_brand_can_be_deleted_when_no_devices_attached(): void
    {
        $brand = Brand::create([
            'name' => 'Empty Brand',
            'slug' => 'empty-brand',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.brands.destroy', $brand));

        $response->assertRedirect(route('admin.brands.index'));
        $this->assertSoftDeleted('brands', ['id' => $brand->id]);

        // Test restore
        $restoreResponse = $this->actingAs($this->adminUser)->post(route('admin.brands.restore', $brand->id));
        $restoreResponse->assertRedirect(route('admin.brands.index', ['trashed' => 1]));
        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'deleted_at' => null,
        ]);
    }

    public function test_public_brand_page_shows_published_devices(): void
    {
        $brand = Brand::create([
            'name' => 'Nothing Tech',
            'slug' => 'nothing',
            'status' => 'published',
        ]);

        Device::create([
            'brand_id' => $brand->id,
            'name' => 'Nothing Phone 2',
            'slug' => 'nothing-phone-2',
            'status' => 'published',
            'market_status' => 'available',
            'device_category' => 'smartphone',
        ]);

        $response = $this->get(route('public.brand.detail', 'nothing'));
        $response->assertStatus(200);
        $response->assertSee('Nothing Tech');
        $response->assertSee('Nothing Phone 2');
    }
}
