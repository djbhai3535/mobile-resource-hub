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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->restrictOnDelete();
            $table->string('name', 150);
            $table->string('marketing_name', 150)->nullable();
            $table->string('slug', 191)->unique();
            $table->text('product_description')->nullable();
            $table->string('short_description', 500)->nullable();
            $table->string('featured_image', 255)->nullable();
            $table->json('image_gallery')->nullable();
            $table->date('announced_date')->nullable()->index();
            $table->date('release_date')->nullable()->index();
            $table->string('status', 30)->default('draft')->index(); // publication status: draft, published, archived
            $table->string('market_status', 30)->default('available')->index(); // commercial status: upcoming, available, discontinued
            $table->string('os', 100)->nullable();
            $table->string('os_version', 100)->nullable();
            $table->string('device_category', 50)->default('smartphone')->index(); // smartphone, tablet, feature_phone, smartwatch
            $table->string('official_product_url', 255)->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->integer('sort_order')->default(0)->index();
            $table->string('seo_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('canonical_url', 255)->nullable();
            $table->string('og_image', 255)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'release_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
