<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Device;
use App\Models\Driver;
use App\Models\Firmware;
use App\Models\FlashFile;
use App\Models\Page;
use App\Models\Post;
use App\Models\SoftwareTool;
use Illuminate\Support\Carbon;

class SeoService
{
    /**
     * Build standard Schema.org Organization + WebSite JSON-LD.
     */
    public function getWebSiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'Mobile Resource Hub',
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/search') . '?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Build Device Product Schema.
     */
    public function getDeviceSchema(Device $device): array
    {
        $brandName = $device->brand?->name ?? 'Mobile';
        $models = $device->modelNumbers()->pluck('model_number')->all();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $device->name,
            'brand' => [
                '@type' => 'Brand',
                'name' => $brandName,
            ],
            'model' => implode(', ', $models),
            'description' => $device->meta_description ?: "Full specifications, model numbers, firmware downloads, and drivers for {$device->name}.",
            'url' => url('/mobile-phones/' . $device->slug),
            'category' => 'Smartphone',
        ];
    }

    /**
     * Build Software/Firmware Application Schema.
     */
    public function getSoftwareSchema(string $name, string $version, string $category, string $url, ?string $description = null): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $name,
            'softwareVersion' => $version,
            'applicationCategory' => $category,
            'operatingSystem' => 'Android, Windows, Linux',
            'description' => $description ?: "Official download and installation package for {$name}.",
            'url' => $url,
        ];
    }

    /**
     * Build Article/Tutorial Schema.
     */
    public function getArticleSchema(Post $post): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => $post->type === 'tutorial' ? 'HowTo' : 'Article',
            'headline' => $post->title,
            'description' => $post->excerpt ?: strip_tags($post->content),
            'url' => $post->type === 'tutorial' ? route('tutorials.show', $post->slug) : route('news.show', $post->slug),
            'datePublished' => ($post->published_at ?? $post->created_at)?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => 'Mobile Resource Hub Editorial Team',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Mobile Resource Hub',
                'url' => url('/'),
            ],
        ];
    }

    /**
     * Build BreadcrumbList Schema.
     */
    public function getBreadcrumbsSchema(array $items): array
    {
        $elements = [];
        $pos = 1;

        foreach ($items as $name => $url) {
            $elements[] = [
                '@type' => 'ListItem',
                'position' => $pos++,
                'name' => $name,
                'item' => $url ?: url()->current(),
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $elements,
        ];
    }

    /**
     * Generate Sitemap Index XML.
     */
    public function renderSitemapIndex(): string
    {
        $sections = [
            'devices',
            'brands',
            'firmware',
            'flash-files',
            'software',
            'drivers',
            'posts',
            'pages',
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($sections as $section) {
            $loc = url("/sitemaps/{$section}.xml");
            $lastmod = now()->toIso8601String();
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>{$loc}</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }

        $xml .= '</sitemapindex>';
        return $xml;
    }

    /**
     * Render Sub-sitemap by section.
     */
    public function renderSubSitemap(string $section): ?string
    {
        $items = [];

        switch ($section) {
            case 'devices':
                $items = Device::published()->select('slug', 'updated_at')->get()->map(function ($d) {
                    return [
                        'loc' => url('/mobile-phones/' . $d->slug),
                        'lastmod' => $d->updated_at?->toIso8601String() ?? now()->toIso8601String(),
                        'changefreq' => 'weekly',
                        'priority' => '0.8',
                    ];
                })->all();
                break;

            case 'brands':
                $items = Brand::where('status', 'active')->select('slug', 'updated_at')->get()->map(function ($b) {
                    return [
                        'loc' => url('/brands/' . $b->slug),
                        'lastmod' => $b->updated_at?->toIso8601String() ?? now()->toIso8601String(),
                        'changefreq' => 'weekly',
                        'priority' => '0.7',
                    ];
                })->all();
                break;

            case 'firmware':
                $items = Firmware::published()->select('slug', 'updated_at')->get()->map(function ($f) {
                    return [
                        'loc' => route('firmware.show', $f->slug),
                        'lastmod' => $f->updated_at?->toIso8601String() ?? now()->toIso8601String(),
                        'changefreq' => 'weekly',
                        'priority' => '0.7',
                    ];
                })->all();
                break;

            case 'flash-files':
                $items = FlashFile::published()->select('slug', 'updated_at')->get()->map(function ($ff) {
                    return [
                        'loc' => route('flash-files.show', $ff->slug),
                        'lastmod' => $ff->updated_at?->toIso8601String() ?? now()->toIso8601String(),
                        'changefreq' => 'weekly',
                        'priority' => '0.7',
                    ];
                })->all();
                break;

            case 'software':
                $items = SoftwareTool::published()->select('slug', 'updated_at')->get()->map(function ($st) {
                    return [
                        'loc' => route('software.show', $st->slug),
                        'lastmod' => $st->updated_at?->toIso8601String() ?? now()->toIso8601String(),
                        'changefreq' => 'monthly',
                        'priority' => '0.6',
                    ];
                })->all();
                break;

            case 'drivers':
                $items = Driver::published()->select('slug', 'updated_at')->get()->map(function ($dr) {
                    return [
                        'loc' => route('drivers.show', $dr->slug),
                        'lastmod' => $dr->updated_at?->toIso8601String() ?? now()->toIso8601String(),
                        'changefreq' => 'monthly',
                        'priority' => '0.6',
                    ];
                })->all();
                break;

            case 'posts':
                $items = Post::published()->select('slug', 'type', 'updated_at')->get()->map(function ($p) {
                    $loc = $p->type === 'tutorial'
                        ? route('tutorials.show', $p->slug)
                        : route('news.show', $p->slug);

                    return [
                        'loc' => $loc,
                        'lastmod' => $p->updated_at?->toIso8601String() ?? now()->toIso8601String(),
                        'changefreq' => 'weekly',
                        'priority' => '0.7',
                    ];
                })->all();
                break;

            case 'pages':
                $staticRoutes = [
                    ['loc' => url('/'), 'priority' => '1.0', 'changefreq' => 'daily'],
                    ['loc' => route('devices.index'), 'priority' => '0.9', 'changefreq' => 'daily'],
                    ['loc' => route('brands.index'), 'priority' => '0.8', 'changefreq' => 'weekly'],
                    ['loc' => route('firmware.index'), 'priority' => '0.8', 'changefreq' => 'daily'],
                    ['loc' => route('flash-files.index'), 'priority' => '0.8', 'changefreq' => 'daily'],
                    ['loc' => route('software.index'), 'priority' => '0.7', 'changefreq' => 'weekly'],
                    ['loc' => route('drivers.index'), 'priority' => '0.7', 'changefreq' => 'weekly'],
                    ['loc' => route('news.index'), 'priority' => '0.8', 'changefreq' => 'daily'],
                    ['loc' => route('tutorials.index'), 'priority' => '0.8', 'changefreq' => 'daily'],
                    ['loc' => route('about'), 'priority' => '0.5', 'changefreq' => 'monthly'],
                    ['loc' => route('contact'), 'priority' => '0.5', 'changefreq' => 'monthly'],
                    ['loc' => route('privacy'), 'priority' => '0.3', 'changefreq' => 'yearly'],
                    ['loc' => route('terms'), 'priority' => '0.3', 'changefreq' => 'yearly'],
                    ['loc' => route('dmca'), 'priority' => '0.3', 'changefreq' => 'yearly'],
                ];

                $dbPages = Page::where('status', 'published')->select('slug', 'updated_at')->get()->map(function ($pg) {
                    return [
                        'loc' => url('/' . ltrim($pg->slug, '/')),
                        'lastmod' => $pg->updated_at?->toIso8601String() ?? now()->toIso8601String(),
                        'changefreq' => 'monthly',
                        'priority' => '0.5',
                    ];
                })->all();

                $items = array_merge(array_map(function ($r) {
                    $r['lastmod'] = now()->toIso8601String();
                    return $r;
                }, $staticRoutes), $dbPages);
                break;

            default:
                return null;
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($items as $item) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($item['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>{$item['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$item['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$item['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';
        return $xml;
    }

    /**
     * Render dynamic robots.txt.
     */
    public function renderRobotsTxt(): string
    {
        $sitemapUrl = url('/sitemap.xml');

        return <<<ROBOTS
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /api/
Disallow: /downloads/mirror/

Sitemap: {$sitemapUrl}
ROBOTS;
    }
}
