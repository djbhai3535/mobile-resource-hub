<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\VisitorAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisitorAnalyticsController extends Controller
{
    public function __construct(
        protected VisitorAnalyticsService $analyticsService
    ) {}

    /**
     * Display Visitor Analytics Dashboard.
     */
    public function index(Request $request): View
    {
        $range = $request->query('range', '30d');
        $from = $request->query('from');
        $to = $request->query('to');

        $analytics = $this->analyticsService->getDashboardAnalytics($range, $from, $to);
        $liveStats = $this->analyticsService->getLiveStats();

        return view('admin.analytics.visitors', compact('analytics', 'liveStats', 'range', 'from', 'to'));
    }
}
