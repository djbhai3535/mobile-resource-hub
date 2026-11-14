<?php

namespace App\Services;

use App\Models\SearchDocument;
use App\Models\SearchDocumentTerm;
use App\Models\SearchLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SearchService
{
    public function __construct(
        protected SearchIndexingService $indexingService
    ) {}

    /**
     * Execute full global search across indexed content.
     */
    public function search(
        string $query,
        string $type = 'all',
        int $perPage = 15,
        int $page = 1,
        string $origin = 'web',
        ?string $sessionHash = null
    ): array {
        $trimmed = trim($query);
        $normalized = $this->indexingService->normalizeText($trimmed);

        if (empty($trimmed)) {
            return [
                'query' => '',
                'type' => $type,
                'paginator' => new LengthAwarePaginator([], 0, $perPage, $page, [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ]),
                'tab_counts' => $this->getEmptyTabCounts(),
                'total_results' => 0,
            ];
        }

        $tokens = $this->indexingService->tokenize($trimmed);
        if (empty($tokens)) {
            $tokens = [$normalized];
        }

        // Calculate candidate documents and scores
        $docScores = $this->calculateCandidateScores($trimmed, $normalized, $tokens, $type);

        $totalResults = count($docScores);

        // Calculate tab counts across all categories for this query
        $tabCounts = $this->calculateCategoryCounts($trimmed, $normalized, $tokens);

        // Paginate IDs
        $pageIds = array_slice(array_keys($docScores), ($page - 1) * $perPage, $perPage);

        // Retrieve hydrated models
        $documents = collect();
        if (!empty($pageIds)) {
            $hydrated = SearchDocument::published()
                ->whereIn('id', $pageIds)
                ->get()
                ->keyBy('id');

            foreach ($pageIds as $id) {
                if ($doc = $hydrated->get($id)) {
                    $doc->search_score = $docScores[$id];
                    $doc->highlighted_title = $this->highlightTerms($doc->title, $tokens);
                    $doc->highlighted_snippet = $this->generateSnippet($doc, $tokens, $trimmed);
                    $documents->push($doc);
                }
            }
        }

        $paginator = new LengthAwarePaginator(
            $documents,
            $totalResults,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        // Log search analytics
        $this->logSearch($trimmed, $normalized, $type, $totalResults, $origin, $sessionHash);

        return [
            'query' => $trimmed,
            'type' => $type,
            'paginator' => $paginator,
            'tab_counts' => $tabCounts,
            'total_results' => $totalResults,
        ];
    }

    /**
     * Compute candidate IDs and relevance scores.
     */
    protected function calculateCandidateScores(
        string $rawQuery,
        string $normalizedQuery,
        array $tokens,
        string $type = 'all'
    ): array {
        $scores = [];

        // 1. Inverted index term score aggregation
        $termQuery = SearchDocumentTerm::query()
            ->select('search_document_id', DB::raw('SUM(weight * occurrence_count) as term_score'))
            ->whereIn('normalized_term', $tokens)
            ->groupBy('search_document_id');

        $termMatches = $termQuery->get();
        foreach ($termMatches as $row) {
            $scores[$row->search_document_id] = ($scores[$row->search_document_id] ?? 0) + (int) $row->term_score;
        }

        // 2. Exact Title, Exact Model & Exact Phrase boosts directly on search_documents
        $exactBoostQuery = SearchDocument::published();
        if ($type !== 'all') {
            $exactBoostQuery->forType($type);
        }

        // Search for title matches, model matches, or normalized_content phrase occurrences
        $candidates = $exactBoostQuery->where(function ($q) use ($rawQuery, $normalizedQuery, $tokens, $scores) {
            $q->where('title', 'like', "%{$rawQuery}%")
                ->orWhere('model_code', 'like', "%{$rawQuery}%")
                ->orWhere('normalized_content', 'like', "%{$normalizedQuery}%");

            if (!empty($scores)) {
                $q->orWhereIn('id', array_keys($scores));
            }
        })->get();

        $finalScores = [];

        foreach ($candidates as $doc) {
            $id = $doc->id;
            $score = $scores[$id] ?? 10; // baseline if matched content phrase

            $lowerTitle = mb_strtolower($doc->title, 'UTF-8');
            $lowerModel = mb_strtolower($doc->model_code ?? '', 'UTF-8');

            // Exact match boosts
            if ($lowerTitle === $normalizedQuery) {
                $score += 500;
            } elseif (str_starts_with($lowerTitle, $normalizedQuery)) {
                $score += 250;
            } elseif (str_contains($lowerTitle, $normalizedQuery)) {
                $score += 150;
            }

            if (!empty($lowerModel)) {
                if ($lowerModel === $normalizedQuery) {
                    $score += 600;
                } elseif (str_contains($lowerModel, $normalizedQuery)) {
                    $score += 300;
                }
            }

            // Substring bonus in normalized content
            if (str_contains($doc->normalized_content, $normalizedQuery)) {
                $score += 80;
            }

            // Multiple token match bonus
            $matchedTokens = 0;
            foreach ($tokens as $token) {
                if (str_contains($doc->normalized_content, $token)) {
                    $matchedTokens++;
                }
            }
            if ($matchedTokens === count($tokens) && count($tokens) > 1) {
                $score += 100; // All tokens present
            }

            $finalScores[$id] = $score;
        }

        // Filter by type if necessary (if candidate IDs from scores table didn't filter by type)
        if ($type !== 'all') {
            $validIds = SearchDocument::published()
                ->where('content_type', $type)
                ->whereIn('id', array_keys($finalScores))
                ->pluck('id')
                ->all();

            $filtered = [];
            foreach ($validIds as $id) {
                $filtered[$id] = $finalScores[$id];
            }
            $finalScores = $filtered;
        }

        // Sort by score descending
        arsort($finalScores);

        return $finalScores;
    }

    /**
     * Calculate tab count badges for each category.
     */
    protected function calculateCategoryCounts(string $rawQuery, string $normalizedQuery, array $tokens): array
    {
        $allScores = $this->calculateCandidateScores($rawQuery, $normalizedQuery, $tokens, 'all');
        $allDocIds = array_keys($allScores);

        if (empty($allDocIds)) {
            return $this->getEmptyTabCounts();
        }

        $typeCounts = SearchDocument::published()
            ->whereIn('id', $allDocIds)
            ->select('content_type', DB::raw('count(*) as aggregate'))
            ->groupBy('content_type')
            ->pluck('aggregate', 'content_type')
            ->all();

        return [
            'all' => count($allDocIds),
            'device' => $typeCounts['device'] ?? 0,
            'firmware' => $typeCounts['firmware'] ?? 0,
            'flash_file' => $typeCounts['flash_file'] ?? 0,
            'software' => $typeCounts['software'] ?? 0,
            'driver' => $typeCounts['driver'] ?? 0,
            'post' => $typeCounts['post'] ?? 0,
            'tutorial' => $typeCounts['tutorial'] ?? 0,
            'brand' => $typeCounts['brand'] ?? 0,
            'page' => $typeCounts['page'] ?? 0,
        ];
    }

    /**
     * Empty tab counts template.
     */
    protected function getEmptyTabCounts(): array
    {
        return [
            'all' => 0,
            'device' => 0,
            'firmware' => 0,
            'flash_file' => 0,
            'software' => 0,
            'driver' => 0,
            'post' => 0,
            'tutorial' => 0,
            'brand' => 0,
            'page' => 0,
        ];
    }

    /**
     * Provide fast grouped suggestions for live autocomplete.
     */
    public function suggest(string $query, int $limit = 8): array
    {
        $trimmed = trim($query);
        if (mb_strlen($trimmed) < 2) {
            return [
                'devices' => [],
                'firmware' => [],
                'flash_files' => [],
                'software' => [],
                'drivers' => [],
                'posts' => [],
            ];
        }

        $normalized = $this->indexingService->normalizeText($trimmed);
        $tokens = $this->indexingService->tokenize($trimmed);

        $results = [
            'devices' => [],
            'firmware' => [],
            'flash_files' => [],
            'software' => [],
            'drivers' => [],
            'posts' => [],
        ];

        // Query documents matching prefix or like
        $candidates = SearchDocument::published()
            ->where(function ($q) use ($trimmed, $normalized, $tokens) {
                $q->where('title', 'like', "%{$trimmed}%")
                    ->orWhere('model_code', 'like', "%{$trimmed}%")
                    ->orWhere('brand_name', 'like', "%{$trimmed}%");

                if (!empty($tokens)) {
                    $q->orWhereHas('terms', function ($tq) use ($tokens) {
                        $tq->whereIn('normalized_term', $tokens);
                    });
                }
            })
            ->limit(30)
            ->get();

        foreach ($candidates as $doc) {
            $item = [
                'title' => $doc->title,
                'name' => $doc->title,
                'slug' => $doc->slug,
                'url' => $doc->searchable_url,
                'model_code' => $doc->model_code,
                'model_number' => $doc->model_code,
                'brand_name' => $doc->brand_name,
                'excerpt' => Str::limit($doc->excerpt, 80),
            ];

            switch ($doc->content_type) {
                case 'device':
                    if (count($results['devices']) < $limit) {
                        $results['devices'][] = $item;
                    }
                    break;
                case 'firmware':
                    if (count($results['firmware']) < $limit) {
                        $results['firmware'][] = $item;
                    }
                    break;
                case 'flash_file':
                    if (count($results['flash_files']) < $limit) {
                        $results['flash_files'][] = $item;
                    }
                    break;
                case 'software':
                    if (count($results['software']) < $limit) {
                        $results['software'][] = $item;
                    }
                    break;
                case 'driver':
                    if (count($results['drivers']) < $limit) {
                        $results['drivers'][] = $item;
                    }
                    break;
                case 'post':
                case 'tutorial':
                    if (count($results['posts']) < $limit) {
                        $results['posts'][] = $item;
                    }
                    break;
            }
        }

        return $results;
    }

    /**
     * Highlight terms in text safely with HTML escaping.
     */
    public function highlightTerms(string $text, array $tokens): string
    {
        $escaped = e($text);
        if (empty($tokens)) {
            return $escaped;
        }

        $escapedTokens = array_map(function ($token) {
            return preg_quote(e($token), '/');
        }, $tokens);

        $pattern = '/(' . implode('|', $escapedTokens) . ')/iu';

        return preg_replace($pattern, '<mark class="bg-warning-subtle text-dark fw-semibold px-1 rounded">$1</mark>', $escaped);
    }

    /**
     * Generate contextual snippet centered around matching query terms.
     */
    protected function generateSnippet(SearchDocument $doc, array $tokens, string $rawQuery): string
    {
        $text = $doc->excerpt ?: strip_tags($doc->normalized_content);

        // Find position of query or first token
        $pos = false;
        foreach (array_merge([$rawQuery], $tokens) as $t) {
            $p = mb_stripos($text, $t, 0, 'UTF-8');
            if ($p !== false) {
                $pos = $p;
                break;
            }
        }

        if ($pos === false || $pos < 80) {
            $snippet = Str::limit($text, 220);
        } else {
            $start = max(0, $pos - 70);
            $length = 220;
            $snippet = '...' . mb_substr($text, $start, $length, 'UTF-8') . '...';
        }

        return $this->highlightTerms($snippet, $tokens);
    }

    /**
     * Privacy-conscious search logging.
     */
    protected function logSearch(
        string $rawQuery,
        string $normalizedQuery,
        string $type,
        int $resultsCount,
        string $origin,
        ?string $sessionHash
    ): void {
        try {
            SearchLog::create([
                'query' => Str::limit($rawQuery, 250),
                'normalized_query' => Str::limit($normalizedQuery, 250),
                'content_type' => $type,
                'results_count' => $resultsCount,
                'origin' => $origin,
                'session_hash' => $sessionHash ?: hash('sha256', request()->ip() . '_mrh_search_salt'),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Logging must never break public search response
        }
    }

    /**
     * Analytics Overview for Admin Panel.
     */
    public function getAnalyticsOverview(int $days = 30): array
    {
        $since = now()->subDays($days);

        $totalSearches = SearchLog::where('created_at', '>=', $since)->count();
        $uniqueQueries = SearchLog::where('created_at', '>=', $since)->distinct('normalized_query')->count('normalized_query');
        $zeroResultCount = SearchLog::where('created_at', '>=', $since)->where('results_count', 0)->count();

        $zeroResultRate = $totalSearches > 0 ? round(($zeroResultCount / $totalSearches) * 100, 1) : 0;

        $topQueries = SearchLog::where('created_at', '>=', $since)
            ->select('normalized_query', DB::raw('count(*) as search_count'), DB::raw('avg(results_count) as avg_results'))
            ->groupBy('normalized_query')
            ->orderByDesc('search_count')
            ->limit(10)
            ->get();

        $zeroResultQueries = SearchLog::where('created_at', '>=', $since)
            ->where('results_count', 0)
            ->select('normalized_query', DB::raw('count(*) as zero_count'))
            ->groupBy('normalized_query')
            ->orderByDesc('zero_count')
            ->limit(10)
            ->get();

        $originBreakdown = SearchLog::where('created_at', '>=', $since)
            ->select('origin', DB::raw('count(*) as count'))
            ->groupBy('origin')
            ->pluck('count', 'origin')
            ->all();

        return [
            'total_searches' => $totalSearches,
            'unique_queries' => $uniqueQueries,
            'zero_result_count' => $zeroResultCount,
            'zero_result_rate' => $zeroResultRate,
            'top_queries' => $topQueries,
            'zero_result_queries' => $zeroResultQueries,
            'origin_breakdown' => $originBreakdown,
        ];
    }
}
