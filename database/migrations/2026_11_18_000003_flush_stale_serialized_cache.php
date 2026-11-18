<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Flush stale serialized PHP objects from the database cache table.
 *
 * Background:
 * The database cache driver (CACHE_STORE=database) stores PHP-serialized values.
 * If Eloquent model objects were cached under old class paths or before schema
 * changes, PHP unserialisation yields __PHP_Incomplete_Class instances. When
 * HomepageService::getOrderedSections() or ThemeService::getCachedThemeCss()
 * reads these back, a TypeError is raised (return type mismatch) → HTTP 500.
 *
 * This migration safely truncates the `cache` table on deployment so stale
 * entries are evicted. The table is immediately repopulated on next request
 * with fresh, correct data. No application data (users, firmware, brands, etc.)
 * is stored in the cache table — only ephemeral cached query results.
 *
 * This migration is idempotent and safe to run on fresh installations
 * (table may already be empty) and existing installations (stale data removed).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Only clear if the cache table exists (guards fresh installs)
        if (Schema::hasTable('cache')) {
            // Use delete() rather than truncate() for broader database compatibility
            // (truncate is not supported in all SQLite versions used in testing).
            // On MySQL production this effectively clears all stale serialized entries.
            DB::table('cache')->delete();
        }

        // Also clear cache_locks table if it exists
        if (Schema::hasTable('cache_locks')) {
            DB::table('cache_locks')->delete();
        }
    }

    public function down(): void
    {
        // Cache data cannot be restored — this is intentional.
        // Cache will be rebuilt on next request.
    }
};
