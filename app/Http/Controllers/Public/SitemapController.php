<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Master Sitemap Index.
     */
    public function index(SeoService $seo): Response
    {
        $content = $seo->renderSitemapIndex();

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex', // Sitemap index itself shouldn't show in search snippets
        ]);
    }

    /**
     * Category Sub-Sitemap.
     */
    public function subSitemap(string $section, SeoService $seo): Response
    {
        $content = $seo->renderSubSitemap($section);

        if ($content === null) {
            abort(404, 'Sitemap section not found.');
        }

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    /**
     * Dynamic robots.txt
     */
    public function robots(SeoService $seo): Response
    {
        $content = $seo->renderRobotsTxt();

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}
