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
        if (!Schema::hasTable('themes')) {
            Schema::create('themes', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('slug', 100)->unique();
                $table->boolean('is_active')->default(false)->index();
                $table->unsignedInteger('version')->default(1);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('theme_settings')) {
            Schema::create('theme_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('theme_id')->constrained('themes')->cascadeOnDelete();
                $table->boolean('is_published')->default(false)->index(); // false = draft, true = active published
                $table->json('branding')->nullable();
                $table->json('global_colors')->nullable();
                $table->json('light_palette')->nullable();
                $table->json('dark_palette')->nullable();
                $table->json('typography')->nullable();
                $table->json('layout')->nullable();
                $table->json('glassmorphism')->nullable();
                $table->json('buttons')->nullable();
                $table->json('animations')->nullable();
                $table->timestamps();

                $table->unique(['theme_id', 'is_published']);
            });
        }

        if (!Schema::hasTable('theme_versions')) {
            Schema::create('theme_versions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('theme_id')->constrained('themes')->cascadeOnDelete();
                $table->unsignedInteger('version_number');
                $table->json('settings_snapshot');
                $table->string('change_summary', 255)->nullable();
                $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('published_at')->nullable();
                $table->timestamps();

                $table->index(['theme_id', 'version_number']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_versions');
        Schema::dropIfExists('theme_settings');
        Schema::dropIfExists('themes');
    }
};
