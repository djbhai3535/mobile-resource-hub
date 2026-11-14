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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 191)->unique();
            $table->string('logo', 255)->nullable();
            $table->string('short_description', 500)->nullable();
            $table->text('full_description')->nullable();
            $table->string('website_url', 255)->nullable();
            $table->string('country_of_origin', 100)->nullable();
            $table->string('status', 30)->default('published')->index(); // published, draft, archived
            $table->string('seo_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('canonical_url', 255)->nullable();
            $table->string('og_image', 255)->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
