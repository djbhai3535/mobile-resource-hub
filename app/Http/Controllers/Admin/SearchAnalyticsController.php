<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SearchLog;
use App\Services\SearchIndexingService;
use App\Services\SearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchAnalyticsController extends Controller
{
    /**
     * Display Search Analytics Dashboard.
     */
    public function index(Request $request, SearchService $searchService): View
    {
        $days = (int) $request->query('days', 30);
        $overview = $searchService->getAnalyticsOverview($days);

        // Paginated search queries log
        $queryFilter = trim($request->query('q', ''));
        $zeroOnly = $request->boolean('zero_only');
        $typeFilter = $request->query('type', 'all');

        $logsQuery = SearchLog::latest('created_at');

        if (!empty($queryFilter)) {
            $logsQuery->where('query', 'like', "%{$queryFilter}%");
        }

        if ($zeroOnly) {
            $logsQuery->where('results_count', 0);
        }

        if ($typeFilter !== 'all') {
            $logsQuery->where('content_type', $typeFilter);
        }

        $logs = $logsQuery->paginate(20)->withQueryString();

        return view('admin.analytics.search', compact(
            'overview',
            'logs',
            'days',
            'queryFilter',
            'zeroOnly',
            'typeFilter'
        ));
    }

    /**
     * Trigger manual search index rebuild from admin.
     */
    public function rebuild(SearchIndexingService $indexer): RedirectResponse
    {
        $stats = $indexer->rebuildAll();

        return redirect()->route('admin.search-analytics.index')
            ->with('success', "Search index rebuilt successfully! ({$stats['total_documents']} documents, {$stats['total_terms']} terms in {$stats['duration_seconds']}s)");
    }
}
