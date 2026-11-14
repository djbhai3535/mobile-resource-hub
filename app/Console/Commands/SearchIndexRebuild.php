<?php

namespace App\Console\Commands;

use App\Services\SearchIndexingService;
use Illuminate\Console\Command;

class SearchIndexRebuild extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'search:index-rebuild {--clear : Clear search index before rebuild}';

    /**
     * The console command description.
     */
    protected $description = 'Rebuild search index documents and term inverted index';

    /**
     * Execute the console command.
     */
    public function handle(SearchIndexingService $indexer): int
    {
        $this->info('Starting full search index rebuild across published resources...');

        $stats = $indexer->rebuildAll(function ($type, $count) {
            $this->line("  Indexed {$type}: {$count}");
        });

        $this->newLine();
        $this->table(
            ['Content Type', 'Indexed Documents'],
            [
                ['Brands', $stats['brands']],
                ['Devices', $stats['devices']],
                ['Firmware', $stats['firmware']],
                ['Flash Files', $stats['flash_files']],
                ['Software Tools', $stats['software']],
                ['USB Drivers', $stats['drivers']],
                ['Posts & Tutorials', $stats['posts']],
                ['Static Pages', $stats['pages']],
                ['---', '---'],
                ['Total Documents', $stats['total_documents']],
                ['Total Inverted Terms', $stats['total_terms']],
            ]
        );

        $this->info("Search index successfully synchronized in {$stats['duration_seconds']}s.");

        return Command::SUCCESS;
    }
}
