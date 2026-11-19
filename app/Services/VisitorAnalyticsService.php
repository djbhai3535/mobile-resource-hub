<?php

namespace App\Services;

use App\Models\PageView;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VisitorAnalyticsService
{
    /**
     * Known bot/crawler patterns to exclude from genuine visitor analytics.
     */
    protected const BOT_PATTERNS = [
        'bot', 'crawl', 'spider', 'slurp', 'mediapartners', 'lighthouse',
        'curl', 'wget', 'python', 'postman', 'insomnia', 'headless',
        'uptime', 'pingdom', 'facebookexternalhit', 'whatsapp', 'telegrambot',
        'yandex', 'baidu', 'duckduckbot', 'semrush', 'ahrefs', 'dotbot',
    ];

    /**
     * Record a genuine public page view.
     */
    public function recordPageView(Request $request): ?PageView
    {
        // 1. Exclude non-GET requests
        if (!$request->isMethod('GET')) {
            return null;
        }

        // 2. Exclude bots, spiders and automated scripts
        $userAgent = (string) $request->userAgent();
        if ($this->isBot($userAgent)) {
            return null;
        }

        // 3. Exclude admin routes, API endpoints and internal assets
        $path = '/' . ltrim($request->path(), '/');
        if (
            Str::startsWith($path, ['/admin', '/api', '/storage', '/build', '/_debugbar', '/favicon', '/up']) ||
            $request->is('admin*')
        ) {
            return null;
        }

        // 4. Exclude authenticated admin users
        if (Auth::check()) {
            $user = Auth::user();
            if (method_exists($user, 'hasRole') && ($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('editor'))) {
                return null;
            }
        }

        // 5. Generate privacy-conscious salted hashes (never store raw IP address)
        $ip = $request->ip() ?: '127.0.0.1';
        $appKey = config('app.key') ?: 'mrh-default-salt-key';
        $ipHash = hash('sha256', $ip . $appKey);
        $visitorHash = hash('sha256', $ip . '|' . $userAgent . '|' . $request->header('Accept-Language', '') . '|' . $appKey);
        $sessionId = $request->hasSession() ? substr($request->session()->getId(), 0, 64) : null;

        // 6. Rapid debounce: prevent counting repeated refreshes within 5 seconds as new views
        $debounceKey = 'pv_debounce_' . $visitorHash . '_' . md5($path);
        if (Cache::has($debounceKey)) {
            return null;
        }
        Cache::put($debounceKey, 1, 5); // 5 seconds debounce

        // 7. Parse client metadata
        $deviceType = $this->detectDeviceType($userAgent);
        $browser = $this->detectBrowser($userAgent);
        $platform = $this->detectPlatform($userAgent);
        $now = Carbon::now();
        $today = $now->toDateString();

        // 8. Create page view record
        try {
            $pageView = PageView::create([
                'visitor_hash' => $visitorHash,
                'session_id' => $sessionId,
                'ip_hash' => $ipHash,
                'path' => Str::limit($path, 490),
                'route_name' => $request->route() ? $request->route()->getName() : null,
                'referer' => Str::limit($request->header('referer'), 490),
                'device_type' => $deviceType,
                'browser' => $browser,
                'platform' => $platform,
                'visited_date' => $today,
                'created_at' => $now,
            ]);

            // Track active visitor presence in cache (5-minute sliding window)
            $this->markVisitorActive($visitorHash);

            return $pageView;
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    /**
     * Mark visitor as active in sliding window.
     */
    protected function markVisitorActive(string $visitorHash): void
    {
        $activeKey = 'active_vis_' . $visitorHash;
        Cache::put($activeKey, now()->timestamp, 300); // 5 minutes
    }

    /**
     * Get aggregate live stats for homepage hero & live counter.
     */
    public function getLiveStats(): array
    {
        return Cache::remember('analytics_live_stats_v2', 30, function () {
            $today = Carbon::today()->toDateString();
            $firstDayOfMonth = Carbon::now()->startOfMonth()->toDateString();
            $fiveMinutesAgo = Carbon::now()->subMinutes(5);

            // Active visitors (last 5 minutes)
            $liveVisitors = PageView::where('created_at', '>=', $fiveMinutesAgo)
                ->distinct('visitor_hash')
                ->count('visitor_hash');

            // Today's unique visitors
            $todayVisitors = PageView::where('visited_date', $today)
                ->distinct('visitor_hash')
                ->count('visitor_hash');

            // Monthly unique visitors
            $monthlyVisitors = PageView::where('visited_date', '>=', $firstDayOfMonth)
                ->distinct('visitor_hash')
                ->count('visitor_hash');

            // Total unique visitors overall
            $totalVisitors = PageView::distinct('visitor_hash')
                ->count('visitor_hash');

            // Total page views
            $todayViews = PageView::where('visited_date', $today)->count();
            $totalViews = PageView::count();

            return [
                'live_visitors' => max($liveVisitors, 1), // At least current viewer
                'today_visitors' => max($todayVisitors, 1),
                'monthly_visitors' => max($monthlyVisitors, 1),
                'total_visitors' => max($totalVisitors, 1),
                'today_views' => max($todayViews, 1),
                'total_views' => max($totalViews, 1),
            ];
        });
    }

    /**
     * Get comprehensive analytics overview for Admin Panel with date filters.
     */
    public function getDashboardAnalytics(string $range = '30d', ?string $from = null, ?string $to = null): array
    {
        $dates = $this->resolveDateRange($range, $from, $to);
        $startDate = $dates['start'];
        $endDate = $dates['end'];

        $cacheKey = 'admin_analytics_' . $range . '_' . $startDate->toDateString() . '_' . $endDate->toDateString();

        return Cache::remember($cacheKey, 60, function () use ($startDate, $endDate, $range) {
            $query = PageView::whereDate('visited_date', '>=', $startDate->toDateString())
                ->whereDate('visited_date', '<=', $endDate->toDateString());

            // Summary metrics
            $totalPageViews = (clone $query)->count();
            $totalUniqueVisitors = (clone $query)->distinct('visitor_hash')->count('visitor_hash');

            $today = Carbon::today()->toDateString();
            $firstDayOfMonth = Carbon::now()->startOfMonth()->toDateString();
            $fiveMinutesAgo = Carbon::now()->subMinutes(5);

            $todayUnique = PageView::where('visited_date', $today)->distinct('visitor_hash')->count('visitor_hash');
            $monthlyUnique = PageView::where('visited_date', '>=', $firstDayOfMonth)->distinct('visitor_hash')->count('visitor_hash');
            $activeNow = PageView::where('created_at', '>=', $fiveMinutesAgo)->distinct('visitor_hash')->count('visitor_hash');

            // Daily traffic trend
            $dailyTrend = [];
            $current = $startDate->copy();
            $trendRecords = (clone $query)
                ->select(
                    'visited_date',
                    DB::raw('COUNT(id) as total_views'),
                    DB::raw('COUNT(DISTINCT visitor_hash) as unique_visitors')
                )
                ->groupBy('visited_date')
                ->orderBy('visited_date')
                ->get()
                ->keyBy(fn($r) => Carbon::parse($r->visited_date)->toDateString());

            while ($current->lte($endDate)) {
                $dStr = $current->toDateString();
                $rec = $trendRecords->get($dStr);

                $dailyTrend[] = [
                    'date' => $dStr,
                    'label' => $current->format('M d'),
                    'views' => $rec ? (int) $rec->total_views : 0,
                    'visitors' => $rec ? (int) $rec->unique_visitors : 0,
                ];
                $current->addDay();
            }

            // Top viewed pages
            $topPages = (clone $query)
                ->select('path', DB::raw('COUNT(id) as views'), DB::raw('COUNT(DISTINCT visitor_hash) as visitors'))
                ->groupBy('path')
                ->orderByDesc('views')
                ->take(8)
                ->get()
                ->map(function ($row) use ($totalPageViews) {
                    $pct = $totalPageViews > 0 ? round(($row->views / $totalPageViews) * 100, 1) : 0;
                    return [
                        'path' => $row->path,
                        'views' => (int) $row->views,
                        'visitors' => (int) $row->visitors,
                        'percentage' => $pct,
                    ];
                })
                ->toArray();

            // Device breakdown
            $deviceBreakdown = (clone $query)
                ->select('device_type', DB::raw('COUNT(id) as count'))
                ->groupBy('device_type')
                ->get()
                ->pluck('count', 'device_type')
                ->toArray();

            // Browser breakdown
            $browserBreakdown = (clone $query)
                ->select('browser', DB::raw('COUNT(id) as count'))
                ->groupBy('browser')
                ->orderByDesc('count')
                ->take(5)
                ->get()
                ->pluck('count', 'browser')
                ->toArray();

            return [
                'range' => $range,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'total_page_views' => $totalPageViews,
                'total_unique_visitors' => $totalUniqueVisitors,
                'today_unique_visitors' => max($todayUnique, 1),
                'monthly_unique_visitors' => max($monthlyUnique, 1),
                'active_now' => max($activeNow, 1),
                'avg_views_per_visitor' => $totalUniqueVisitors > 0 ? round($totalPageViews / $totalUniqueVisitors, 2) : 1,
                'daily_trend' => $dailyTrend,
                'top_pages' => $topPages,
                'device_breakdown' => $deviceBreakdown,
                'browser_breakdown' => $browserBreakdown,
            ];
        });
    }

    /**
     * Resolve date range from string or custom dates.
     */
    protected function resolveDateRange(string $range, ?string $from = null, ?string $to = null): array
    {
        $today = Carbon::today();

        return match ($range) {
            'today' => [
                'start' => $today->copy(),
                'end' => $today->copy(),
            ],
            '7d' => [
                'start' => $today->copy()->subDays(6),
                'end' => $today->copy(),
            ],
            '90d' => [
                'start' => $today->copy()->subDays(89),
                'end' => $today->copy(),
            ],
            'custom' => [
                'start' => $from ? Carbon::parse($from)->startOfDay() : $today->copy()->subDays(29),
                'end' => $to ? Carbon::parse($to)->endOfDay() : $today->copy(),
            ],
            default => [ // 30d
                'start' => $today->copy()->subDays(29),
                'end' => $today->copy(),
            ],
        };
    }

    /**
     * Check if user agent matches known crawlers/bots.
     */
    protected function isBot(string $userAgent): bool
    {
        if (empty($userAgent)) {
            return true;
        }

        $ua = strtolower($userAgent);
        foreach (self::BOT_PATTERNS as $bot) {
            if (str_contains($ua, $bot)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect device type (desktop, mobile, tablet).
     */
    protected function detectDeviceType(string $userAgent): string
    {
        $ua = strtolower($userAgent);
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad') || (str_contains($ua, 'android') && !str_contains($ua, 'mobile'))) {
            return 'tablet';
        }
        if (str_contains($ua, 'mobile') || str_contains($ua, 'iphone') || str_contains($ua, 'android') || str_contains($ua, 'blackberry')) {
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * Detect browser name.
     */
    protected function detectBrowser(string $userAgent): string
    {
        $ua = strtolower($userAgent);
        if (str_contains($ua, 'edg/')) return 'Edge';
        if (str_contains($ua, 'chrome/') && !str_contains($ua, 'edg/')) return 'Chrome';
        if (str_contains($ua, 'safari/') && !str_contains($ua, 'chrome/')) return 'Safari';
        if (str_contains($ua, 'firefox/')) return 'Firefox';
        if (str_contains($ua, 'opera') || str_contains($ua, 'opr/')) return 'Opera';
        return 'Other';
    }

    /**
     * Detect operating system platform.
     */
    protected function detectPlatform(string $userAgent): string
    {
        $ua = strtolower($userAgent);
        if (str_contains($ua, 'windows')) return 'Windows';
        if (str_contains($ua, 'macintosh') || str_contains($ua, 'mac os x')) return 'macOS';
        if (str_contains($ua, 'android')) return 'Android';
        if (str_contains($ua, 'iphone') || str_contains($ua, 'ipad')) return 'iOS';
        if (str_contains($ua, 'linux')) return 'Linux';
        return 'Other';
    }
}
