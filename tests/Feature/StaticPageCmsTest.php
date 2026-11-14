<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\ContentSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaticPageCmsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AdminUserSeeder::class);
        $this->seed(ContentSeeder::class);

        $this->admin = User::where('email', 'admin@mobilehub.local')->first();
    }

    public function test_admin_can_view_static_pages_list(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/pages');
        $response->assertStatus(200);
        $response->assertSee('About Mobile Resource Hub');
        $response->assertSee('Privacy Policy');
    }

    public function test_admin_can_edit_static_page_content(): void
    {
        $aboutPage = Page::where('system_key', 'about')->first();

        $response = $this->actingAs($this->admin)->put("/admin/pages/{$aboutPage->id}", [
            'title' => 'Updated About Portal Title',
            'slug' => 'about-us',
            'content' => '<p>Updated editorial mission text for Mobile Resource Hub testing.</p>',
            'seo_title' => 'Updated About SEO Title',
            'meta_description' => 'Updated meta description.',
            'status' => 'published',
        ]);

        $response->assertRedirect('/admin/pages');
        $this->assertDatabaseHas('pages', [
            'id' => $aboutPage->id,
            'title' => 'Updated About Portal Title',
        ]);

        // Verify public route renders updated content
        $publicResponse = $this->get('/about-us');
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee('Updated editorial mission text for Mobile Resource Hub testing.');
    }
}
