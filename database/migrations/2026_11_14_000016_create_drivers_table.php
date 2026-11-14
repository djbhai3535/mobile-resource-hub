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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->string('name', 150);
            $table->string('slug', 191)->unique();
            $table->string('driver_version', 50)->nullable();
            $table->string('os', 100)->default('Windows');
            $table->string('windows_versions', 150)->default('Windows 11 / 10 / 8.1 / 7 (32-bit & 64-bit)');
            $table->string('architecture', 100)->default('32-bit & 64-bit');
            $table->date('release_date')->nullable();
            $table->text('supported_devices')->nullable();
            $table->string('file_type', 50)->default('ZIP Package');
            $table->unsignedBigInteger('file_size_bytes')->nullable();
            $table->string('file_size_formatted', 50)->nullable();
            $table->string('md5_checksum', 64)->nullable();
            $table->string('sha256_checksum', 64)->nullable();
            $table->string('official_source', 500)->nullable();
            $table->string('distribution_permission', 50)->default('authorized_mirror');
            $table->text('installation_instructions')->nullable();
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
