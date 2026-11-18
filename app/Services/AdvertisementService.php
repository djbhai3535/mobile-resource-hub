<?php

namespace App\Services;

use App\Models\Advertisement;
use App\Models\AdvertisementLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdvertisementService
{
    /**
     * All recognized placement locations.
     */
    public function getPlacements(): array
    {
        return [
            'header_below_nav' => [
                'label' => 'Below Navigation Header',
                'description' => 'Horizontal leader banner displayed across all public pages under navbar.',
                'recommended_size' => '728x90 or responsive',
            ],
            'header_banner' => [
                'label' => 'Header Top Banner',
                'description' => 'Top-of-page alert/promotional banner above header.',
                'recommended_size' => 'Responsive text or banner',
            ],
            'homepage_hero' => [
                'label' => 'Homepage Hero Advertisement',
                'description' => 'Prominent creative placement directly below homepage hero search bar.',
                'recommended_size' => '970x250 or 728x90',
            ],
            'homepage_between_sections' => [
                'label' => 'Homepage Between Content Sections',
                'description' => 'Mid-page break banner on the homepage.',
                'recommended_size' => '728x90 or responsive',
            ],
            'homepage_footer' => [
                'label' => 'Homepage Above Footer',
                'description' => 'Full-width bottom promotional space on homepage.',
                'recommended_size' => '728x90 or 970x90',
            ],
            'sidebar_top' => [
                'label' => 'Sidebar Top Slot',
                'description' => 'First slot in detail page sidebars (firmware, specs, articles).',
                'recommended_size' => '300x250 Medium Rectangle',
            ],
            'sidebar_mid' => [
                'label' => 'Sidebar Middle Slot',
                'description' => 'Mid-sidebar sticky or scrolling unit.',
                'recommended_size' => '300x250 or 300x600 Half Page',
            ],
            'sidebar_bottom' => [
                'label' => 'Sidebar Bottom Slot',
                'description' => 'End of sidebar block before footer.',
                'recommended_size' => '300x250',
            ],
            'article_before' => [
                'label' => 'Before Article Content',
                'description' => 'Top of tech news and repair guides.',
                'recommended_size' => '728x90 or 468x60',
            ],
            'article_between' => [
                'label' => 'Between Article Content',
                'description' => 'In-article native ad break within tutorial steps.',
                'recommended_size' => 'Responsive or 300x250',
            ],
            'article_after' => [
                'label' => 'After Article Content',
                'description' => 'End of tutorial or article before comments.',
                'recommended_size' => '728x90 or 336x280',
            ],
            'firmware_top' => [
                'label' => 'Firmware Detail Top',
                'description' => 'Above stock ROM checksum and technical metadata.',
                'recommended_size' => '728x90 or responsive',
            ],
            'firmware_mid' => [
                'label' => 'Firmware Detail Middle',
                'description' => 'Between firmware changelog and flashing instructions.',
                'recommended_size' => '728x90',
            ],
            'firmware_bottom' => [
                'label' => 'Firmware Detail Bottom',
                'description' => 'Below mirror download buttons.',
                'recommended_size' => '728x90',
            ],
            'download_top' => [
                'label' => 'Download Gateway Above Info',
                'description' => 'Countdown or verification gateway top slot.',
                'recommended_size' => '728x90 or 300x250',
            ],
            'download_bottom' => [
                'label' => 'Download Gateway Below Info',
                'description' => 'Countdown or verification gateway bottom slot.',
                'recommended_size' => '728x90',
            ],
            'global_footer' => [
                'label' => 'Global Footer Advertisement',
                'description' => 'Persistent banner right above footer across all views.',
                'recommended_size' => '728x90 or responsive',
            ],
        ];
    }

    /**
     * Pick the best active advertisement for placement using rotation weights.
     */
    public function getActiveAdForPlacement(string $placement, ?string $deviceType = null, ?string $theme = null): ?Advertisement
    {
        $ads = Advertisement::active()
            ->forPlacement($placement)
            ->get();

        if ($ads->isEmpty()) {
            return null;
        }

        // Filter by device visibility if specified
        if ($deviceType) {
            $ads = $ads->filter(function ($ad) use ($deviceType) {
                if ($deviceType === 'mobile') return $ad->show_mobile;
                if ($deviceType === 'tablet') return $ad->show_tablet;
                return $ad->show_desktop;
            });
        }

        // Filter by theme visibility if specified
        if ($theme) {
            $ads = $ads->filter(function ($ad) use ($theme) {
                if ($theme === 'dark') return $ad->show_dark_theme;
                return $ad->show_light_theme;
            });
        }

        if ($ads->isEmpty()) {
            return null;
        }

        // Weighted random selection
        $totalWeight = $ads->sum('rotation_weight');
        if ($totalWeight <= 0) {
            return $ads->random();
        }

        $random = mt_rand(1, $totalWeight);
        $current = 0;

        foreach ($ads as $ad) {
            $current += $ad->rotation_weight;
            if ($random <= $current) {
                return $ad;
            }
        }

        return $ads->first();
    }

    /**
     * Render HTML markup for a given placement.
     */
    public function render(string $placement, ?string $deviceType = null, ?string $theme = null): string
    {
        try {
            $ad = $this->getActiveAdForPlacement($placement, $deviceType, $theme);

            if (!$ad) {
                return '';
            }

            // Record impression
            $this->recordImpression($ad, $placement, $deviceType ?? 'desktop', $theme ?? 'light');

            $html = '<div class="mrh-ad-slot my-3 text-center" data-placement="' . e($placement) . '" style="min-height: 50px;">';
            $html .= '<div class="small text-muted text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.08em; opacity: 0.7;">Advertisement</div>';

            if ($ad->type === 'custom_html' && !empty($ad->custom_html)) {
                // Super Admin custom snippet
                $html .= '<div class="mrh-ad-custom d-inline-block">' . $ad->custom_html . '</div>';
            } elseif ($ad->image_path) {
                $clickUrl = route('ad.click', $ad->id);
                $imgUrl = asset('storage/' . $ad->image_path);
                $alt = e($ad->image_alt ?: $ad->name);

                $html .= '<a href="' . $clickUrl . '" target="_blank" rel="sponsored noopener noreferrer" class="d-inline-block text-decoration-none">';
                $html .= '<img src="' . $imgUrl . '" alt="' . $alt . '" class="img-fluid rounded border shadow-sm" style="max-height: 250px; object-fit: contain;">';
                $html .= '</a>';
            } elseif ($ad->destination_url) {
                $clickUrl = route('ad.click', $ad->id);
                $html .= '<a href="' . $clickUrl . '" target="_blank" rel="sponsored noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-pill px-4">';
                $html .= e($ad->name) . ' &rarr;';
                $html .= '</a>';
            }

            $html .= '</div>';

            return $html;
        } catch (\Throwable $e) {
            // Advertisement failures must never break public page rendering
            return '';
        }
    }

    /**
     * Record impression count and log entry with session deduplication to prevent inflation.
     */
    public function recordImpression(Advertisement $ad, string $placement, string $device = 'desktop', string $theme = 'light'): void
    {
        try {
            $sessionHash = hash('sha256', request()->ip() . '_mrh_ad_salt');

            // Prevent rapid duplicate impression events within a 60-second window for the same visitor
            $alreadyCounted = AdvertisementLog::where('advertisement_id', $ad->id)
                ->where('event_type', 'impression')
                ->where('session_hash', $sessionHash)
                ->where('created_at', '>=', now()->subSeconds(60))
                ->exists();

            if ($alreadyCounted) {
                return;
            }

            $ad->increment('impressions_count');

            AdvertisementLog::create([
                'advertisement_id' => $ad->id,
                'event_type' => 'impression',
                'placement' => $placement,
                'device_category' => $device,
                'theme_mode' => $theme,
                'session_hash' => $sessionHash,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Analytics write failure must never break public rendering
        }
    }

    /**
     * Record click event and redirect safely with spam prevention.
     */
    public function recordClick(Advertisement $ad, string $device = 'desktop', string $theme = 'light'): void
    {
        try {
            $sessionHash = hash('sha256', request()->ip() . '_mrh_ad_salt');

            // Prevent rapid double-click inflation within 5 seconds from same visitor
            $recentClick = AdvertisementLog::where('advertisement_id', $ad->id)
                ->where('event_type', 'click')
                ->where('session_hash', $sessionHash)
                ->where('created_at', '>=', now()->subSeconds(5))
                ->exists();

            if ($recentClick) {
                return;
            }

            $ad->increment('clicks_count');

            AdvertisementLog::create([
                'advertisement_id' => $ad->id,
                'event_type' => 'click',
                'placement' => $ad->placement,
                'device_category' => $device,
                'theme_mode' => $theme,
                'session_hash' => $sessionHash,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Non-blocking
        }
    }

    /**
     * Validate that an ad destination URL is safe and strictly HTTPS (or internal).
     */
    public function isValidDestinationUrl(?string $url): bool
    {
        if (empty($url)) {
            return true;
        }

        // Disallow dangerous schemes
        $disallowed = ['javascript:', 'data:', 'file:', 'vbscript:'];
        $lower = strtolower(trim($url));

        foreach ($disallowed as $scheme) {
            if (str_starts_with($lower, $scheme)) {
                return false;
            }
        }

        // Must start with https:// or /
        return str_starts_with($lower, 'https://') || str_starts_with($lower, '/');
    }

    /**
     * Analytics Overview for Admin Panel.
     */
    public function getAnalyticsOverview(int $days = 30): array
    {
        $since = now()->subDays($days);

        $totalImpressions = AdvertisementLog::where('created_at', '>=', $since)
            ->where('event_type', 'impression')
            ->count();

        $totalClicks = AdvertisementLog::where('created_at', '>=', $since)
            ->where('event_type', 'click')
            ->count();

        $ctr = $totalImpressions > 0 ? round(($totalClicks / $totalImpressions) * 100, 2) : 0.0;

        $activeCampaigns = Advertisement::active()->count();
        $expiredCampaigns = Advertisement::where('end_date', '<', now())->count();

        $placements = $this->getPlacements();
        $placementStats = [];

        foreach ($placements as $key => $meta) {
            $imp = AdvertisementLog::where('created_at', '>=', $since)
                ->where('placement', $key)
                ->where('event_type', 'impression')
                ->count();

            $clk = AdvertisementLog::where('created_at', '>=', $since)
                ->where('placement', $key)
                ->where('event_type', 'click')
                ->count();

            $pCtr = $imp > 0 ? round(($clk / $imp) * 100, 2) : 0.0;

            if ($imp > 0 || $clk > 0) {
                $placementStats[] = [
                    'key' => $key,
                    'label' => $meta['label'],
                    'impressions' => $imp,
                    'clicks' => $clk,
                    'ctr' => $pCtr,
                ];
            }
        }

        // Top ads
        $topAds = Advertisement::with('creator')
            ->orderByDesc('clicks_count')
            ->take(10)
            ->get();

        return [
            'total_impressions' => $totalImpressions,
            'total_clicks' => $totalClicks,
            'ctr' => $ctr,
            'active_campaigns' => $activeCampaigns,
            'expired_campaigns' => $expiredCampaigns,
            'placement_stats' => $placementStats,
            'top_ads' => $topAds,
        ];
    }
}
