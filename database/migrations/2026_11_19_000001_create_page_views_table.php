<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('page_views')) {
            Schema::create('page_views', function (Blueprint $table) {
                $table->id();
                $table->string('visitor_hash', 64)->index();
                $table->string('session_id', 64)->nullable()->index();
                $table->string('ip_hash', 64)->nullable()->index();
                $table->string('path', 500);
                $table->string('route_name', 100)->nullable();
                $table->string('referer', 500)->nullable();
                $table->string('device_type', 20)->default('desktop'); // desktop, mobile, tablet
                $table->string('browser', 50)->nullable();
                $table->string('platform', 50)->nullable();
                $table->date('visited_date')->index();
                $table->timestamp('created_at')->index();

                // Composite indexes for fast analytics queries
                $table->index(['visited_date', 'visitor_hash'], 'page_views_date_visitor_idx');
                $table->index(['visited_date', 'path'], 'page_views_date_path_idx');
                $table->index(['created_at', 'visitor_hash'], 'page_views_created_visitor_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
