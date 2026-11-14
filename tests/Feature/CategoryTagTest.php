<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTagTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $this->admin = User::where('email', 'admin@mobilehub.local')->first();
    }

    public function test_category_crud_operations(): void
    {
        // 1. Create Category
        $response = $this->actingAs($this->admin)->post('/admin/categories', [
            'name' => 'Custom ROMs',
            'slug' => 'custom-roms',
            'description' => 'AOSP and LineageOS based third-party builds.',
            'status' => 'active',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('categories', ['slug' => 'custom-roms']);

        $category = Category::where('slug', 'custom-roms')->first();

        // 2. Update Category
        $updateResponse = $this->actingAs($this->admin)->put("/admin/categories/{$category->id}", [
            'name' => 'Custom ROMs & Kernels',
            'slug' => 'custom-roms-kernels',
            'description' => 'Updated description.',
            'status' => 'active',
        ]);

        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('categories', ['slug' => 'custom-roms-kernels']);

        // 3. Delete Category
        $deleteResponse = $this->actingAs($this->admin)->delete("/admin/categories/{$category->id}");
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_tag_crud_operations(): void
    {
        // 1. Create Tag
        $response = $this->actingAs($this->admin)->post('/admin/tags', [
            'name' => 'Fastboot Flashing',
            'slug' => 'fastboot-flashing',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tags', ['slug' => 'fastboot-flashing']);

        $tag = Tag::where('slug', 'fastboot-flashing')->first();

        // 2. Delete Tag
        $deleteResponse = $this->actingAs($this->admin)->delete("/admin/tags/{$tag->id}");
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}
