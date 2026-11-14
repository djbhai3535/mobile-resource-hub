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
        Schema::create('flash_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->restrictOnDelete();
            $table->foreignId('device_id')->nullable()->constrained('devices')->nullOnDelete();
            $table->string('title', 255);
            $table->string('slug', 191)->unique();
            $table->string('model_number', 100)->index();
            $table->string('chipset', 100)->nullable()->index(); // e.g. MediaTek MT6768, Qualcomm SM7435, Unisoc T606
            $table->string('file_type', 50)->default('stock_rom')->index(); // stock_rom, scatter_firmware, pac_file, qfil_package, da_file, recovery_image, boot_image
            $table->string('android_version', 50)->nullable();
            $table->unsignedBigInteger('file_size_bytes')->nullable();
            $table->string('file_size_formatted', 50)->nullable();
            $table->string('md5_checksum', 64)->nullable();
            $table->string('sha256_checksum', 64)->nullable();
            $table->string('flashing_tool', 255)->nullable(); // SP Flash Tool, Odin, QFIL, ResearchDownload
            $table->text('compatibility_notes')->nullable();
            $table->text('installation_instructions')->nullable();
            $table->string('source_website', 255)->nullable();
            $table->string('official_download_url', 500)->nullable();
            $table->string('distribution_permission', 50)->default('authorized_mirror'); // official_link_only, authorized_mirror, site_hosted
            $table->boolean('is_verified')->default(true);
            $table->string('status', 30)->default('draft')->index(); // draft, published, archived
            $table->unsignedBigInteger('download_requests_count')->default(0);
            $table->string('seo_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('canonical_url', 255)->nullable();
            $table->string('og_image', 255)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'brand_id']);
            $table->index(['model_number', 'file_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_files');
    }
};
