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
        if (!Schema::hasTable('homepage_sections')) {
            Schema::create('homepage_sections', function (Blueprint $table) {
                $table->id();
                $table->string('section_key', 50)->unique();
                $table->string('title', 255);
                $table->string('subtitle', 255)->nullable();
                $table->boolean('is_enabled')->default(true)->index();
                $table->integer('sort_order')->default(0)->index();
                $table->integer('item_count')->default(8);
                $table->string('display_style', 50)->default('grid'); // grid, carousel, list, compact
                $table->string('background_style', 50)->default('default'); // default, subtle, glass, dark
                $table->string('category_filter', 100)->nullable();
                $table->json('settings')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_sections');
    }
};
