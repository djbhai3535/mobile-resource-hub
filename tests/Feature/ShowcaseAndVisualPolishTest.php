<?php

namespace Tests\Feature;

use App\Models\ShowcaseItem;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShowcaseAndVisualPolishTest extends TestCase
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

    public function test_showcase_admin_crud_and_validation(): void
    {
        Storage::fake('public');

        // 1. Index page
        $indexRes = $this->actingAs($this->admin)->get('/admin/showcase');
        $indexRes->assertStatus(200);
        $indexRes->assertSee('3D Mobile Showcase Videos');

        // 2. Upload video
        $videoFile = UploadedFile::fake()->create('smartphone_spin.mp4', 1024, 'video/mp4');
        $posterFile = UploadedFile::fake()->create('poster.jpg', 100, 'image/jpeg');

        $storeRes = $this->actingAs($this->admin)->post('/admin/showcase', [
            'title' => 'Galaxy S25 Ultra 3D Titanium Showcase',
            'subtitle' => 'Next-generation curved titanium chassis and periscope lens assembly',
            'brand_name' => 'Samsung',
            'video' => $videoFile,
            'poster' => $posterFile,
            'sort_order' => 1,
            'is_active' => '1',
            'autoplay' => '1',
            'loop' => '1',
            'muted' => '1',
        ]);

        $storeRes->assertRedirect('/admin/showcase');
        $storeRes->assertSessionHas('success');

        $this->assertDatabaseHas('showcase_items', [
            'title' => 'Galaxy S25 Ultra 3D Titanium Showcase',
            'brand_name' => 'Samsung',
            'sort_order' => 1,
            'is_active' => 1,
            'autoplay' => 1,
            'loop' => 1,
            'muted' => 1,
        ]);

        $item = ShowcaseItem::first();
        $this->assertNotNull($item);
        Storage::disk('public')->assertExists($item->video_path);
        Storage::disk('public')->assertExists($item->poster_path);
        $this->assertStringContainsString('storage/showcase/', $item->video_url);

        // 3. Edit & Update
        $editRes = $this->actingAs($this->admin)->get("/admin/showcase/{$item->id}/edit");
        $editRes->assertStatus(200);
        $editRes->assertSee('Galaxy S25 Ultra 3D Titanium Showcase');

        $updateRes = $this->actingAs($this->admin)->put("/admin/showcase/{$item->id}", [
            'title' => 'Galaxy S25 Ultra Revised Edition',
            'subtitle' => 'Updated render',
            'brand_name' => 'Samsung',
            'sort_order' => 2,
            'is_active' => '1',
            'autoplay' => '1',
            'loop' => '1',
            'muted' => '1',
        ]);

        $updateRes->assertRedirect('/admin/showcase');
        $this->assertDatabaseHas('showcase_items', [
            'id' => $item->id,
            'title' => 'Galaxy S25 Ultra Revised Edition',
            'sort_order' => 2,
        ]);

        // 4. Toggle active status
        $toggleRes = $this->actingAs($this->admin)->post("/admin/showcase/{$item->id}/toggle");
        $toggleRes->assertRedirect();
        $this->assertFalse($item->fresh()->is_active);

        // 5. Delete
        $deleteRes = $this->actingAs($this->admin)->delete("/admin/showcase/{$item->id}");
        $deleteRes->assertRedirect('/admin/showcase');
        $this->assertDatabaseMissing('showcase_items', ['id' => $item->id]);
        Storage::disk('public')->assertMissing($item->video_path);
    }

    public function test_showcase_frontend_rendering_behavior(): void
    {
        // When no showcase items exist, the section is completely absent
        $resEmpty = $this->get('/');
        $resEmpty->assertStatus(200);
        $resEmpty->assertDontSee('showcase-3d-section', false);

        // When a video is created and active, the section is displayed
        $item = ShowcaseItem::create([
            'title' => 'Titanium 3D Showcase Video',
            'subtitle' => 'Snapdragon 8 Elite Edition',
            'brand_name' => 'Samsung',
            'video_path' => 'showcase/sample.mp4',
            'video_format' => 'mp4',
            'sort_order' => 0,
            'is_active' => true,
            'autoplay' => true,
            'loop' => true,
            'muted' => true,
        ]);

        $resActive = $this->get('/');
        $resActive->assertStatus(200);
        $resActive->assertSee('showcase-3d-section', false);
        $resActive->assertSee('Titanium 3D Showcase Video');
        $resActive->assertSee('showcase/sample.mp4', false);

        // When disabled, section is hidden again
        $item->update(['is_active' => false]);
        $resInactive = $this->get('/');
        $resInactive->assertStatus(200);
        $resInactive->assertDontSee('showcase-3d-section', false);
    }

    public function test_brand_logos_and_search_chips_visual_presentation(): void
    {
        // 1. Homepage renders authentic brand logos without broken icons
        $homeRes = $this->get('/');
        $homeRes->assertStatus(200);

        // Check authentic brand identities present in Popular Brands
        $homeRes->assertSee('Samsung');
        $homeRes->assertSee('Apple');
        $homeRes->assertSee('Xiaomi');

        // 2. Check popular search chips present and styled
        $homeRes->assertSee('Galaxy S24 Ultra');
        $homeRes->assertSee('SM-S928B');
        $homeRes->assertSee('Odin 3.14');
        $homeRes->assertSee('Qualcomm Driver');
        $homeRes->assertSee('HyperOS 2.0');
        $homeRes->assertSee('tech-pill-samsung', false);
        $homeRes->assertSee('tech-pill-cyan', false);
        $homeRes->assertSee('tech-pill-amber', false);

        // 3. Brands directory renders and displays authentic brand logos (SVG vector or custom)
        $brandsRes = $this->get('/brands');
        $brandsRes->assertStatus(200);
        $brandsRes->assertSee('aria-label="Samsung"', false);
        $brandsRes->assertSee('aria-label="Apple"', false);
    }
}
