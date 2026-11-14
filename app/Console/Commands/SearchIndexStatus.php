<?php

namespace App\Console\Commands;

use App\Models\SearchDocument;
use App\Models\SearchDocumentTerm;
use App\Models\SearchLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SearchIndexStatus extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'search:index-status';

    /**
     * The console command description.
     */
    protected $description = 'Display search index statistics, term volume, and search activity';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Mobile Resource Hub — Search Index Status Overview');
        $this->newLine();

        $typeCounts = SearchDocument::select('content_type', 'publication_status', DB::raw('count(*) as aggregate'))
            ->groupBy('content_type', 'publication_status')
            ->get();

        $tableData = [];
        foreach ($typeCounts as $row) {
            $tableData[] = [
                $row->content_type,
                $row->publication_status,
                $row->aggregate,
            ];
        }

        $this->table(['Content Type', 'Publication Status', 'Documents Count'], $tableData);

        $totalDocs = SearchDocument::count();
        $totalTerms = SearchDocumentTerm::count();
        $totalSearches = SearchLog::count();
        $zeroSearches = SearchLog::where('results_count', 0)->count();

        $this->line("<comment>Total Indexed Documents:</comment> {$totalDocs}");
        $this->line("<comment>Total Inverted Terms:</comment> {$totalTerms}");
        $this->line("<comment>Total Recorded Searches:</comment> {$totalSearches} ({$zeroSearches} zero-result queries)");

        return Command::SUCCESS;
    }
}
