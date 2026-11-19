<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandingAndWhatsAppTest extends TestCase
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

    public function test_whatsapp_url_generation_and_sanitization(): void
    {
        // 1. Disabled returns null
        SiteSetting::set('whatsapp_enabled', false, 'boolean', 'whatsapp');
        SiteSetting::set('whatsapp_number', '+1 (555) 123-4567', 'string', 'whatsapp');
        $this->assertNull(SiteSetting::getWhatsAppUrl());

        // 2. Enabled with spaces/symbols strips non-digits
        SiteSetting::set('whatsapp_enabled', true, 'boolean', 'whatsapp');
        $url = SiteSetting::getWhatsAppUrl();
        $this->assertNotNull($url);
        $this->assertStringStartsWith('https://wa.me/15551234567', $url);
        $this->assertStringContainsString('text=', $url);

        // 3. Custom message URL encoding
        SiteSetting::set('whatsapp_default_message', 'Hello! Need support on Galaxy S24.', 'string', 'whatsapp');
        $urlCustom = SiteSetting::getWhatsAppUrl();
        $this->assertStringContainsString('Hello%21+Need+support', $urlCustom);
    }

    public function test_whatsapp_admin_form_and_public_rendering(): void
    {
        // Admin updates settings
        $response = $this->actingAs($this->admin)->post('/admin/settings/whatsapp', [
            'whatsapp_enabled' => '1',
            'whatsapp_show_header' => '1',
            'whatsapp_number' => '447123456789',
            'whatsapp_default_message' => 'Custom support greeting',
        ]);

        $response->assertRedirect('/admin/settings/whatsapp');
        $response->assertSessionHas('success');

        $this->assertTrue((bool) SiteSetting::get('whatsapp_enabled'));
        $this->assertTrue((bool) SiteSetting::get('whatsapp_show_header'));
        $this->assertEquals('447123456789', SiteSetting::get('whatsapp_number'));

        // Verify public homepage displays floating button and header link
        $pubResponse = $this->get('/');
        $pubResponse->assertStatus(200);
        $pubResponse->assertSee('mrh-whatsapp-float', false);
        $pubResponse->assertSee('https://wa.me/447123456789', false);
        $pubResponse->assertSee('bi-whatsapp', false);

        // When disabled, floating button is hidden
        SiteSetting::set('whatsapp_enabled', false, 'boolean', 'whatsapp');
        $disabledResponse = $this->get('/');
        $disabledResponse->assertStatus(200);
        $disabledResponse->assertDontSee('id="mrhWhatsAppButton"', false);
    }

    public function test_logo_and_branding_settings_upload_and_preview(): void
    {
        Storage::fake('public');

        // Admin branding page is accessible
        $getRes = $this->actingAs($this->admin)->get('/admin/settings/branding');
        $getRes->assertStatus(200);
        $getRes->assertSee('Website Logo &amp; Identity', false);

        // Upload custom logo and favicon
        $logo = UploadedFile::fake()->create('custom_logo.png', 50, 'image/png');
        $favicon = UploadedFile::fake()->create('favicon.ico', 10, 'image/x-icon');

        $postRes = $this->actingAs($this->admin)->post('/admin/settings/branding', [
            'site_name' => 'Custom Mobile Hub',
            'site_logo' => $logo,
            'site_favicon' => $favicon,
            'site_logo_width' => 180,
            'site_logo_height' => 45,
        ]);

        $postRes->assertRedirect('/admin/settings/branding');
        $postRes->assertSessionHas('success');

        $this->assertEquals('Custom Mobile Hub', SiteSetting::getSiteName());
        $this->assertEquals(180, SiteSetting::getLogoWidth());
        $this->assertEquals(45, SiteSetting::getLogoHeight());

        $logoPath = SiteSetting::get('site_logo');
        $this->assertNotNull($logoPath);
        Storage::disk('public')->assertExists($logoPath);

        $faviconPath = SiteSetting::get('site_favicon');
        $this->assertNotNull($faviconPath);
        Storage::disk('public')->assertExists($faviconPath);

        // Check public layout renders the uploaded logo and favicon
        $publicRes = $this->get('/');
        $publicRes->assertStatus(200);
        $publicRes->assertSee(SiteSetting::getLogoUrl(), false);
        $publicRes->assertSee(SiteSetting::getFaviconUrl(), false);

        // Test removing logo resets gracefully
        $removeRes = $this->actingAs($this->admin)->post('/admin/settings/branding', [
            'site_name' => 'Custom Mobile Hub',
            'remove_logo' => '1',
            'remove_favicon' => '1',
        ]);

        $removeRes->assertRedirect('/admin/settings/branding');
        $this->assertNull(SiteSetting::get('site_logo'));
        $this->assertNull(SiteSetting::get('site_favicon'));
    }

    public function test_brand_color_metadata_and_tokens(): void
    {
        $samsung = Brand::getBrandColorMeta('samsung');
        $this->assertEquals('#0c4da2', $samsung['color']);

        $apple = Brand::getBrandColorMeta('apple');
        $this->assertEquals('#1d1d1f', $apple['color']);

        $xiaomi = Brand::getBrandColorMeta('xiaomi');
        $this->assertEquals('#ff6700', $xiaomi['color']);

        $oppo = Brand::getBrandColorMeta('oppo');
        $this->assertEquals('#008a00', $oppo['color']);

        $google = Brand::getBrandColorMeta('google');
        $this->assertEquals('#4285f4', $google['color']);

        $oneplus = Brand::getBrandColorMeta('oneplus');
        $this->assertEquals('#eb0028', $oneplus['color']);

        $motorola = Brand::getBrandColorMeta('motorola');
        $this->assertEquals('#0081c9', $motorola['color']);

        // Check fallback for unspecified brand slug
        $unknown = Brand::getBrandColorMeta('custom-unspecified-phone');
        $this->assertArrayHasKey('color', $unknown);
        $this->assertArrayHasKey('bg', $unknown);
        $this->assertArrayHasKey('border', $unknown);
    }
}
