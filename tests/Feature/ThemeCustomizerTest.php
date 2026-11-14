<?php

namespace Tests\Feature;

use App\Models\Theme;
use App\Models\ThemeSetting;
use App\Models\ThemeVersion;
use App\Models\User;
use App\Services\ThemeService;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ThemeCustomizerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected ThemeService $themeService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $this->adminUser = User::where('email', 'admin@mobilehub.local')->first();
        $this->regularUser = User::factory()->create();

        $this->themeService = app(ThemeService::class);
    }

    public function test_guest_and_unauthorized_users_cannot_access_theme_customizer(): void
    {
        $this->get(route('admin.theme.index'))
            ->assertRedirect(route('admin.login'));

        $this->actingAs($this->regularUser)
            ->get(route('admin.theme.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_theme_customizer_split_screen(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.theme.index'));

        $response->assertOk();
        $response->assertSee('Theme Customizer');
        $response->assertSee('Save Draft');
        $response->assertSee('Publish Theme');
        $response->assertSee('Theme Preview');
    }

    public function test_saving_draft_does_not_modify_published_theme_css(): void
    {
        // First ensure initial baseline
        $theme = $this->themeService->getActiveTheme();
        $publishedCssBefore = $this->themeService->getCachedThemeCss();

        $payload = [
            'branding' => [
                'site_name' => 'Draft Modified Name',
                'tagline' => 'New Draft Tagline',
            ],
            'global_colors' => [
                'primary' => '#ff5500',
                'accent' => '#00ff88',
            ],
            'light_palette' => [
                'bg_app' => '#fafafa',
            ],
            'dark_palette' => [
                'bg_app' => '#05070f',
            ],
            'typography' => [
                'font_family' => 'Roboto',
            ],
            'layout' => [
                'container_max_width' => '1300px',
            ],
            'glassmorphism' => [
                'card_transparency' => '0.85',
            ],
            'buttons' => [
                'border_radius' => '10px',
            ],
            'animations' => [
                'enabled' => 1,
            ],
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.theme.draft'), $payload);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        // Check draft is updated in database
        $draftSettings = ThemeSetting::where('theme_id', $theme->id)->where('is_published', false)->first();
        $this->assertEquals('#ff5500', $draftSettings->global_colors['primary']);
        $this->assertEquals('Draft Modified Name', $draftSettings->branding['site_name']);

        // Check published CSS remains unchanged
        $publishedCssAfter = $this->themeService->getCachedThemeCss();
        $this->assertEquals($publishedCssBefore, $publishedCssAfter);
        $this->assertStringNotContainsString('#ff5500', $publishedCssAfter);
    }

    public function test_color_validation_rejects_invalid_values(): void
    {
        $payload = [
            'global_colors' => [
                'primary' => 'not-a-valid-hex-color',
            ],
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.theme.draft'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['global_colors.primary']);
    }

    public function test_publishing_draft_increments_version_snapshots_history_and_updates_live_css(): void
    {
        $theme = $this->themeService->getActiveTheme();
        $initialVersion = $theme->version;

        // Save a draft first
        $payload = [
            'branding' => [
                'site_name' => 'Published Brand Hub',
                'tagline' => 'Fastest Downloads',
            ],
            'global_colors' => [
                'primary' => '#10b981',
                'accent' => '#f59e0b',
            ],
        ];

        $this->actingAs($this->adminUser)->postJson(route('admin.theme.draft'), $payload)->assertOk();

        // Now publish
        $publishResponse = $this->actingAs($this->adminUser)
            ->post(route('admin.theme.publish'), [
                'change_summary' => 'Phase 6 Emerald Theme Launch',
            ]);

        $publishResponse->assertRedirect(route('admin.theme.index'));
        $publishResponse->assertSessionHas('success');

        // Verify version incremented
        $theme->refresh();
        $this->assertEquals($initialVersion + 1, $theme->version);

        // Verify snapshot stored in theme_versions
        $snapshot = ThemeVersion::where('theme_id', $theme->id)
            ->where('version_number', $theme->version)
            ->first();
        $this->assertNotNull($snapshot);
        $this->assertEquals('Phase 6 Emerald Theme Launch', $snapshot->change_summary);
        $this->assertEquals($this->adminUser->id, $snapshot->published_by);

        // Verify live CSS was updated and contains the new primary color
        $liveCss = $this->themeService->getCachedThemeCss();
        $this->assertStringContainsString('#10b981', $liveCss);

        // Verify public home page renders the compiled CSS
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('#10b981', false);
    }

    public function test_restoring_a_previous_theme_version(): void
    {
        $theme = $this->themeService->getActiveTheme();

        // 1. Publish version 1 with color #111111
        $this->actingAs($this->adminUser)->postJson(route('admin.theme.draft'), [
            'global_colors' => ['primary' => '#111111'],
        ]);
        $this->actingAs($this->adminUser)->post(route('admin.theme.publish'), ['change_summary' => 'V1']);
        $v1Number = $theme->fresh()->version;
        $v1Version = ThemeVersion::where('theme_id', $theme->id)->where('version_number', $v1Number)->first();

        // 2. Publish version 2 with color #222222
        $this->actingAs($this->adminUser)->postJson(route('admin.theme.draft'), [
            'global_colors' => ['primary' => '#222222'],
        ]);
        $this->actingAs($this->adminUser)->post(route('admin.theme.publish'), ['change_summary' => 'V2']);

        $currentCss = $this->themeService->getCachedThemeCss();
        $this->assertStringContainsString('#222222', $currentCss);

        // 3. Rollback to version 1
        $restoreResponse = $this->actingAs($this->adminUser)
            ->post(route('admin.theme.restore', $v1Version->id));

        $restoreResponse->assertRedirect(route('admin.theme.index'));
        $restoreResponse->assertSessionHas('success');

        // Check live CSS now has #111111 again
        $restoredCss = $this->themeService->getCachedThemeCss();
        $this->assertStringContainsString('#111111', $restoredCss);
    }
}
