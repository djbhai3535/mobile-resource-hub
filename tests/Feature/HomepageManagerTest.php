<?php

namespace Tests\Feature;

use App\Models\HomepageSection;
use App\Models\User;
use App\Services\HomepageService;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected HomepageService $homepageService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $this->adminUser = User::where('email', 'admin@mobilehub.local')->first();
        $this->regularUser = User::factory()->create();

        $this->homepageService = app(HomepageService::class);
        $this->homepageService->ensureDefaultSections();
    }

    public function test_guest_and_unauthorized_users_cannot_access_homepage_manager(): void
    {
        $this->get(route('admin.homepage.index'))
            ->assertRedirect(route('admin.login'));

        $this->actingAs($this->regularUser)
            ->get(route('admin.homepage.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_homepage_sections_list(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.homepage.index'));

        $response->assertOk();
        $response->assertSee('Homepage Section &amp; Layout Manager', false);
        $response->assertSee('Browse by Brand');
        $response->assertSee('Latest Mobile Devices');
    }

    public function test_toggling_section_visibility_hides_it_from_public_home(): void
    {
        $section = HomepageSection::where('section_key', 'popular_brands')->first();
        $this->assertTrue((bool)$section->is_enabled);

        // Public page shows section heading
        $this->get(route('home'))
            ->assertOk()
            ->assertSee($section->title);

        // Toggle to disabled
        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.homepage.toggle', $section->id));

        $response->assertOk();
        $response->assertJson(['success' => true, 'is_enabled' => false]);
        $section->refresh();
        $this->assertFalse((bool)$section->is_enabled);

        // Public page no longer renders the disabled section
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee($section->title);
    }

    public function test_updating_section_title_and_subtitle_reflects_on_public_home(): void
    {
        $section = HomepageSection::where('section_key', 'latest_devices')->first();

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.homepage.update', $section->id), [
                'title' => 'Cutting Edge Flagships 2026',
                'subtitle' => 'Explore the newest hardware specifications',
                'display_style' => 'grid',
                'background_style' => 'subtle',
                'item_count' => 6,
                'is_enabled' => 1,
            ]);

        $response->assertRedirect(route('admin.homepage.index'));
        $response->assertSessionHas('success');

        $section->refresh();
        $this->assertEquals('Cutting Edge Flagships 2026', $section->title);
        $this->assertEquals('Explore the newest hardware specifications', $section->subtitle);

        // Verify public home page displays the updated titles
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Cutting Edge Flagships 2026')
            ->assertSee('Explore the newest hardware specifications');
    }

    public function test_reordering_sections_updates_sort_order(): void
    {
        $sections = HomepageSection::all();
        $firstId = $sections[0]->id;
        $secondId = $sections[1]->id;

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.homepage.reorder'), [
                'ordered_ids' => [$secondId, $firstId],
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertEquals(1, HomepageSection::find($secondId)->sort_order);
        $this->assertEquals(2, HomepageSection::find($firstId)->sort_order);
    }

    public function test_empty_catalog_sections_render_gracefully_without_errors(): void
    {
        // Even when catalog is completely empty, home route must respond with 200 OK
        $response = $this->get(route('home'));
        $response->assertOk();
    }
}
