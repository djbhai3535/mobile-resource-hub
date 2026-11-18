<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppAndEssentialPagesTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);

        $this->superAdmin = User::factory()->create([
            'email' => 'admin@editzy.website',
        ]);
        $superAdminRole = Role::where('name', 'super-admin')->first();
        $this->superAdmin->roles()->attach($superAdminRole);
    }

    public function test_super_admin_can_view_whatsapp_settings(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.settings.whatsapp'));
        $response->assertOk();
        $response->assertSee('WhatsApp Live Support Configuration');
        $response->assertSee('Floating WhatsApp Chat Button');
    }

    public function test_super_admin_can_update_whatsapp_settings(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.settings.whatsapp.update'), [
            'whatsapp_enabled' => '1',
            'whatsapp_number' => '+1 (555) 123-4567',
            'whatsapp_default_message' => 'Hello Mobile Resource Hub Support!',
        ]);

        $response->assertRedirect(route('admin.settings.whatsapp'));
        $response->assertSessionHas('success');

        $this->assertTrue(SiteSetting::get('whatsapp_enabled'));
        $this->assertSame('+1 (555) 123-4567', SiteSetting::get('whatsapp_number'));

        // Check sanitized click-to-chat URL
        $chatUrl = SiteSetting::getWhatsAppUrl();
        $this->assertNotNull($chatUrl);
        $this->assertStringContainsString('https://wa.me/15551234567', $chatUrl);
        $this->assertStringContainsString('text=Hello+Mobile+Resource+Hub+Support%21', $chatUrl);
    }

    public function test_whatsapp_button_appears_on_public_pages_when_enabled(): void
    {
        SiteSetting::set('whatsapp_enabled', true, 'boolean', 'whatsapp');
        SiteSetting::set('whatsapp_number', '15559876543', 'string', 'whatsapp');
        SiteSetting::set('whatsapp_default_message', 'Need assistance', 'string', 'whatsapp');

        $response = $this->get(url('/'));
        $response->assertOk();
        $response->assertSee('mrhWhatsAppButton');
        $response->assertSee('https://wa.me/15559876543?text=Need+assistance', false);
    }

    public function test_whatsapp_button_hidden_when_disabled(): void
    {
        SiteSetting::set('whatsapp_enabled', false, 'boolean', 'whatsapp');
        SiteSetting::set('whatsapp_number', '15559876543', 'string', 'whatsapp');

        $response = $this->get(url('/'));
        $response->assertOk();
        $response->assertDontSee('mrhWhatsAppButton');
    }

    public function test_all_essential_public_pages_return_http_200(): void
    {
        $urls = [
            '/' => 'Mobile Resource Hub',
            '/about-us' => 'About Mobile Resource Hub',
            '/contact-us' => 'Get in Touch with Us',
            '/privacy-policy' => 'Privacy Policy',
            '/terms-and-conditions' => 'Terms and Conditions',
            '/disclaimer' => 'Technical Disclaimer & Risk Disclosure',
            '/editorial-policy' => 'Editorial & Content Verification Policy',
            '/dmca' => 'DMCA & Copyright Notice',
            '/sitemap.xml' => '<sitemapindex',
            '/robots.txt' => 'User-agent: *',
        ];

        foreach ($urls as $url => $contentMatch) {
            $response = $this->get($url);
            $response->assertStatus(200, "Failed asserting that URL {$url} returns HTTP 200");
            $response->assertSee($contentMatch, false);
        }
    }

    public function test_footer_contains_all_essential_policy_links(): void
    {
        $response = $this->get(url('/'));
        $response->assertOk();

        $response->assertSee(url('/about-us'));
        $response->assertSee(url('/contact-us'));
        $response->assertSee(url('/privacy-policy'));
        $response->assertSee(url('/terms-and-conditions'));
        $response->assertSee(url('/disclaimer'));
        $response->assertSee(url('/editorial-policy'));
        $response->assertSee(url('/dmca'));
        $response->assertSee(url('/sitemap.xml'));
    }
}
