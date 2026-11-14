<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Device;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapAndRobotsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AdminUserSeeder::class);
    }

    public function test_sitemap_index_returns_valid_xml_with_sub_sitemaps(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        $response->assertSee('<sitemapindex', false);
        $response->assertSee('/sitemaps/devices.xml', false);
        $response->assertSee('/sitemaps/firmware.xml', false);
        $response->assertSee('/sitemaps/posts.xml', false);
    }

    public function test_device_sub_sitemap_renders_published_devices_with_iso8601_lastmod(): void
    {
        $brand = Brand::create(['name' => 'Sony', 'slug' => 'sony', 'status' => 'active']);
        $device = Device::create([
            'brand_id' => $brand->id,
            'name' => 'Xperia 1 VI',
            'slug' => 'xperia-1-vi',
            'status' => 'published',
        ]);

        $response = $this->get('/sitemaps/devices.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        $response->assertSee('<urlset', false);
        $response->assertSee('/mobile-phones/xperia-1-vi', false);
        $response->assertSee('<lastmod>', false);
    }

    public function test_draft_posts_and_devices_are_excluded_from_sitemaps(): void
    {
        $author = User::first();
        $draftPost = Post::create([
            'author_id' => $author->id,
            'title' => 'Draft Unreleased Announcement',
            'slug' => 'draft-unreleased-announcement',
            'content' => 'Secret text.',
            'status' => 'draft',
        ]);

        $response = $this->get('/sitemaps/posts.xml');
        $response->assertStatus(200);
        $response->assertDontSee('draft-unreleased-announcement');
    }

    public function test_robots_txt_returns_disallow_rules_and_sitemap_directive(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $response->assertSee('Disallow: /admin/');
        $response->assertSee('Disallow: /api/');
        $response->assertSee('Disallow: /downloads/mirror/');
        $response->assertSee('Sitemap:');
    }
}
