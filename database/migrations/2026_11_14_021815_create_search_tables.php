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
        Schema::create('search_documents', function (Blueprint $table) {
            $table->id();
            $table->string('searchable_type', 120);
            $table->unsignedBigInteger('searchable_id');
            $table->string('content_type', 50); // post, page, brand, device, firmware, flash_file, software, driver
            $table->string('title', 255);
            $table->string('slug', 255);
            $table->text('excerpt')->nullable();
            $table->longText('normalized_content');
            $table->string('searchable_url', 500);
            $table->string('model_code', 100)->nullable();
            $table->string('brand_name', 100)->nullable();
            $table->string('publication_status', 30)->default('published');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('source_updated_at')->nullable();
            $table->timestamp('indexed_at')->nullable();
            $table->timestamps();

            $table->unique(['searchable_type', 'searchable_id'], 'search_docs_unique_searchable');
            $table->index(['publication_status', 'content_type'], 'search_docs_status_type_idx');
            $table->index('model_code', 'search_docs_model_code_idx');
            $table->index('brand_name', 'search_docs_brand_name_idx');
            $table->index('title', 'search_docs_title_idx');
            $table->index('published_at', 'search_docs_published_at_idx');
        });

        Schema::create('search_document_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('search_document_id')->constrained('search_documents')->cascadeOnDelete();
            $table->string('normalized_term', 100);
            $table->string('field_name', 50)->default('content'); // model, title, brand, spec, heading, excerpt, tag, body
            $table->integer('weight')->default(10);
            $table->integer('occurrence_count')->default(1);
            $table->timestamps();

            $table->index(['normalized_term', 'search_document_id'], 'search_terms_term_doc_idx');
            $table->index(['search_document_id', 'normalized_term'], 'search_terms_doc_term_idx');
            $table->index(['field_name', 'weight'], 'search_terms_field_weight_idx');
        });

        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->string('query', 255);
            $table->string('normalized_query', 255);
            $table->string('content_type', 50)->default('all');
            $table->integer('results_count')->default(0);
            $table->string('origin', 50)->default('web'); // web, api, autocomplete
            $table->string('session_hash', 64)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('normalized_query', 'search_logs_query_idx');
            $table->index('created_at', 'search_logs_created_at_idx');
            $table->index('results_count', 'search_logs_results_count_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_logs');
        Schema::dropIfExists('search_document_terms');
        Schema::dropIfExists('search_documents');
    }
};
