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
        if (!Schema::hasTable('software_tools')) {
            Schema::create('software_tools', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('slug', 191)->unique();
                $table->string('category', 60)->default('flashing_tools')->index(); // flashing_tools, android_utilities, mobile_repair_tools, backup_tools, recovery_utilities, device_management
                $table->string('version', 50)->nullable();
                $table->string('developer', 150)->nullable();
                $table->string('supported_os', 150)->default('Windows 11 / 10 / 8.1 / 7');
                $table->string('supported_architectures', 100)->default('32-bit & 64-bit');
                $table->text('description')->nullable();
                $table->text('changelog')->nullable();
                $table->date('release_date')->nullable();
                $table->string('file_format', 50)->default('zip'); // zip, exe, 7z, msi, tar.gz
                $table->unsignedBigInteger('file_size_bytes')->nullable();
                $table->string('file_size_formatted', 50)->nullable();
                $table->string('md5_checksum', 64)->nullable();
                $table->string('sha256_checksum', 64)->nullable();
                $table->string('license_type', 100)->default('Freeware'); // Freeware, Open Source, OEM Proprietary
                $table->string('distribution_permission', 50)->default('authorized_mirror');
                $table->string('official_website', 500)->nullable();
                $table->string('dependencies', 255)->nullable(); // e.g. .NET Framework 4.8, Visual C++ Redistributable
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

                $table->index(['status', 'category']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software_tools');
    }
};
