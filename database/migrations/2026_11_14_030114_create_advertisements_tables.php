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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('placement', 50)->index();
            $table->string('type', 30)->default('image'); // image, url, network, custom_html
            $table->string('image_path', 255)->nullable();
            $table->string('image_alt', 255)->nullable();
            $table->string('destination_url', 500)->nullable();
            $table->text('custom_html')->nullable();
            $table->string('campaign_name', 100)->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('priority')->default(1);
            $table->integer('rotation_weight')->default(10);
            $table->boolean('show_desktop')->default(true);
            $table->boolean('show_tablet')->default(true);
            $table->boolean('show_mobile')->default(true);
            $table->boolean('show_light_theme')->default(true);
            $table->boolean('show_dark_theme')->default(true);
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedBigInteger('impressions_count')->default(0);
            $table->unsignedBigInteger('clicks_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['placement', 'is_active']);
        });

        Schema::create('advertisement_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained('advertisements')->cascadeOnDelete();
            $table->string('event_type', 20); // impression, click
            $table->string('placement', 50);
            $table->string('device_category', 20)->default('desktop'); // desktop, tablet, mobile
            $table->string('theme_mode', 20)->default('light'); // light, dark
            $table->string('session_hash', 64)->nullable();
            $table->timestamp('created_at')->nullable()->index();

            $table->index(['advertisement_id', 'event_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement_logs');
        Schema::dropIfExists('advertisements');
    }
};
