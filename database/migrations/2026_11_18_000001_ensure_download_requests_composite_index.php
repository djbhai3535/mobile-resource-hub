<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ensure the composite index dl_req_dl_type_dl_id_created_idx exists on download_requests
     * across all production/VPS installations where the table was previously created
     * without this index due to MySQL identifier length limits.
     */
    public function up(): void
    {
        if (Schema::hasTable('download_requests')) {
            $hasIndex = false;

            if (method_exists(Schema::getFacadeRoot(), 'hasIndex')) {
                $hasIndex = Schema::hasIndex('download_requests', 'dl_req_dl_type_dl_id_created_idx');
            } else {
                $indexes = Schema::getIndexes('download_requests');
                foreach ($indexes as $index) {
                    if (($index['name'] ?? '') === 'dl_req_dl_type_dl_id_created_idx') {
                        $hasIndex = true;
                        break;
                    }
                }
            }

            if (!$hasIndex) {
                Schema::table('download_requests', function (Blueprint $table) {
                    $table->index(['downloadable_type', 'downloadable_id', 'created_at'], 'dl_req_dl_type_dl_id_created_idx');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('download_requests')) {
            $hasIndex = false;

            if (method_exists(Schema::getFacadeRoot(), 'hasIndex')) {
                $hasIndex = Schema::hasIndex('download_requests', 'dl_req_dl_type_dl_id_created_idx');
            } else {
                $indexes = Schema::getIndexes('download_requests');
                foreach ($indexes as $index) {
                    if (($index['name'] ?? '') === 'dl_req_dl_type_dl_id_created_idx') {
                        $hasIndex = true;
                        break;
                    }
                }
            }

            if ($hasIndex) {
                Schema::table('download_requests', function (Blueprint $table) {
                    $table->dropIndex('dl_req_dl_type_dl_id_created_idx');
                });
            }
        }
    }
};
