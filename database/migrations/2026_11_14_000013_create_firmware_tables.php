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
        if (!Schema::hasTable('firmwares')) {
            Schema::create('firmwares', function (Blueprint $table) {
                $table->id();
                $table->foreignId('brand_id')->constrained('brands')->restrictOnDelete();
                $table->foreignId('device_id')->nullable()->constrained('devices')->nullOnDelete();
                $table->string('title', 255);
                $table->string('slug', 191)->unique();
                $table->string('model_number', 100)->index();
                $table->json('compatible_models')->nullable();
                $table->string('region', 100)->nullable();
                $table->string('csc', 50)->nullable()->index();
                $table->string('android_version', 50)->nullable();
                $table->string('os_version', 100)->nullable();
                $table->string('firmware_version', 100)->nullable();
                $table->string('build_number', 100)->nullable()->index();
                $table->date('security_patch_date')->nullable();
                $table->date('release_date')->nullable()->index();
                $table->string('file_type', 100)->default('Stock ROM');
                $table->string('archive_format', 50)->default('zip');
                $table->unsignedBigInteger('file_size_bytes')->nullable();
                $table->string('file_size_formatted', 50)->nullable();
                $table->string('md5_checksum', 64)->nullable();
                $table->string('sha256_checksum', 64)->nullable();
                $table->text('flashing_instructions')->nullable();
                $table->string('required_tools', 255)->nullable();
                $table->text('prerequisites')->nullable();
                $table->text('warnings')->nullable();
                $table->text('known_issues')->nullable();
                $table->string('source_website', 255)->nullable();
                $table->string('official_download_url', 500)->nullable();
                $table->string('license_type', 100)->default('Official OEM Stock');
                $table->string('distribution_permission', 50)->default('official_link_only'); // official_link_only, authorized_mirror, site_hosted
                $table->text('permission_notes')->nullable();
                $table->boolean('is_verified')->default(true);
                $table->string('status', 30)->default('draft')->index(); // draft, published, archived
                $table->unsignedBigInteger('download_requests_count')->default(0);
                $table->string('seo_title', 255)->nullable();
                $table->text('meta_description')->nullable();
                $table->string('canonical_url', 255)->nullable();
                $table->string('og_image', 255)->nullable();
                $table->softDeletes();
                $table->timestamps();

                $table->index(['status', 'release_date']);
                $table->index(['brand_id', 'model_number']);
            });
        }

        if (!Schema::hasTable('download_mirrors')) {
            Schema::create('download_mirrors', function (Blueprint $table) {
                $table->id();
                $table->morphs('mirrorable'); // mirrorable_type, mirrorable_id
                $table->string('mirror_name', 100); // e.g. "Official High-Speed Mirror", "Google Drive Mirror", "Local Storage"
                $table->string('url', 1000)->nullable();
                $table->string('file_path', 255)->nullable(); // when stored on private local disk
                $table->string('disk', 50)->default('private');
                $table->boolean('is_official')->default(false);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->unsignedBigInteger('download_requests_count')->default(0);
                $table->timestamps();

                $table->index(['mirrorable_type', 'mirrorable_id', 'is_active']);
            });
        }

        if (!Schema::hasTable('download_requests')) {
            Schema::create('download_requests', function (Blueprint $table) {
                $table->id();
                $table->morphs('downloadable');
                $table->foreignId('mirror_id')->nullable()->constrained('download_mirrors')->nullOnDelete();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('status', 30)->default('initiated'); // initiated, redirected, streamed
                $table->timestamp('created_at')->useCurrent();

                $table->index(['downloadable_type', 'downloadable_id', 'created_at'], 'dl_req_dl_type_dl_id_created_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('download_requests')) {
            Schema::table('download_requests', function (Blueprint $table) {
                $table->dropIndex('dl_req_dl_type_dl_id_created_idx');
            });
        }
        Schema::dropIfExists('download_requests');
        Schema::dropIfExists('download_mirrors');
        Schema::dropIfExists('firmwares');
    }
};
