<?php

namespace Tests\Feature;

use App\Models\Advertisement;
use App\Models\AdvertisementLog;
use App\Models\Role;
use App\Models\User;
use App\Services\AdvertisementService;
use Carbon\Carbon;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvertisementSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $normalAdmin;
    protected User $guestUser;
    protected AdvertisementService $adService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $this->superAdmin = User::where('email', 'admin@mobilehub.local')->first();
        $this->normalAdmin = User::where('email', 'editor@mobilehub.local')->first();
        $this->guestUser = User::factory()->create();

        $this->adService = app(AdvertisementService::class);
    }

    public function test_guest_and_unauthorized_users_blocked_from_ad_admin_routes(): void
    {
        $this->get(route('admin.advertisements.index'))
            ->assertRedirect(route('admin.login'));

        $this->actingAs($this->guestUser)
            ->get(route('admin.advertisements.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_campaigns_and_placements(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('admin.advertisements.index'))
            ->assertOk()
            ->assertSee('Advertisement Campaigns');

        $this->actingAs($this->superAdmin)
            ->get(route('admin.advertisements.placements'))
            ->assertOk()
            ->assertSee('Advertisement Placements')
            ->assertSee('header_below_nav')
            ->assertSee('homepage_hero')
            ->assertSee('global_footer');
    }

    public function test_admin_can_create_image_banner_advertisement(): void
    {
        $payload = [
            'name' => 'Tech Partner Banner',
            'placement' => 'homepage_hero',
            'type' => 'url',
            'destination_url' => 'https://example.com/partner-deal',
            'image_alt' => 'Check Partner Deal',
            'campaign_name' => 'Q4 Promo',
            'priority' => 5,
            'rotation_weight' => 20,
            'show_desktop' => 1,
            'show_tablet' => 1,
            'show_mobile' => 1,
            'show_light_theme' => 1,
            'show_dark_theme' => 1,
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.advertisements.store'), $payload);

        $response->assertRedirect(route('admin.advertisements.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('advertisements', [
            'name' => 'Tech Partner Banner',
            'placement' => 'homepage_hero',
            'destination_url' => 'https://example.com/partner-deal',
        ]);
    }

    public function test_super_admin_can_create_custom_html_ad_while_normal_admin_is_forbidden(): void
    {
        $payload = [
            'name' => 'AdSense Script Snippet',
            'placement' => 'sidebar_top',
            'type' => 'custom_html',
            'custom_html' => '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>',
            'priority' => 1,
            'rotation_weight' => 10,
            'is_active' => 1,
        ];

        // Normal admin attempt must be rejected (403 Forbidden)
        $this->actingAs($this->normalAdmin)
            ->post(route('admin.advertisements.store'), $payload)
            ->assertForbidden();

        // Super Admin attempt must succeed
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.advertisements.store'), $payload);

        $response->assertRedirect(route('admin.advertisements.index'));
        $this->assertDatabaseHas('advertisements', [
            'name' => 'AdSense Script Snippet',
            'type' => 'custom_html',
        ]);
    }

    public function test_schedule_filtering_renders_active_and_omits_expired_campaigns(): void
    {
        // 1. Create an active ad
        $activeAd = Advertisement::create([
            'name' => 'Active Banner',
            'placement' => 'header_below_nav',
            'type' => 'url',
            'destination_url' => 'https://example.com/active',
            'image_alt' => 'Active Promotion',
            'start_date' => Carbon::now()->subDays(2),
            'end_date' => Carbon::now()->addDays(5),
            'is_active' => true,
            'rotation_weight' => 10,
        ]);

        // 2. Create an expired ad
        $expiredAd = Advertisement::create([
            'name' => 'Expired Banner',
            'placement' => 'header_below_nav',
            'type' => 'url',
            'destination_url' => 'https://example.com/expired',
            'image_alt' => 'Expired Promotion',
            'start_date' => Carbon::now()->subDays(10),
            'end_date' => Carbon::now()->subDays(2),
            'is_active' => true,
            'rotation_weight' => 10,
        ]);

        // 3. Render header_below_nav
        $html = $this->adService->render('header_below_nav');

        $this->assertStringContainsString('Active Banner', $html);
        $this->assertStringNotContainsString('Expired Banner', $html);

        // Check impression log created for active ad
        $activeAd->refresh();
        $this->assertGreaterThanOrEqual(1, $activeAd->impressions_count);
        $this->assertDatabaseHas('advertisement_logs', [
            'advertisement_id' => $activeAd->id,
            'event_type' => 'impression',
        ]);
    }

    public function test_click_gateway_records_click_and_redirects_to_verified_https_target(): void
    {
        $ad = Advertisement::create([
            'name' => 'Secure Target Ad',
            'placement' => 'global_footer',
            'type' => 'url',
            'destination_url' => 'https://verified-partner.com/deal',
            'is_active' => true,
            'impressions_count' => 10,
            'clicks_count' => 0,
        ]);

        $response = $this->get(route('ad.click', $ad->id));

        // Must redirect (302) to the destination
        $response->assertRedirect('https://verified-partner.com/deal');

        // Check clicks count incremented
        $ad->refresh();
        $this->assertEquals(1, $ad->clicks_count);

        // Check advertisement_logs has 'click'
        $this->assertDatabaseHas('advertisement_logs', [
            'advertisement_id' => $ad->id,
            'event_type' => 'click',
        ]);
    }

    public function test_click_gateway_prevents_open_redirect_when_url_is_invalid(): void
    {
        $ad = Advertisement::create([
            'name' => 'Suspicious Target Ad',
            'placement' => 'global_footer',
            'type' => 'url',
            'destination_url' => 'javascript:alert(1)',
            'is_active' => true,
        ]);

        $response = $this->get(route('ad.click', $ad->id));

        // Must safely fall back to home ('/')
        $response->assertRedirect(url('/'));
    }

    public function test_analytics_reports_genuine_ctr_and_counts(): void
    {
        $ad = Advertisement::create([
            'name' => 'Analytics Ad',
            'placement' => 'homepage_hero',
            'type' => 'url',
            'destination_url' => 'https://example.com',
            'is_active' => true,
            'impressions_count' => 200,
            'clicks_count' => 10,
        ]);

        // Expected CTR = (10 / 200) * 100 = 5.0%
        $this->assertEquals(5.0, $ad->ctr);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.advertisements.analytics'));

        $response->assertOk();
        $response->assertSee('Analytics Ad');
        $response->assertSee('200');
        $response->assertSee('10');
        $response->assertSee('5%');
    }
}
