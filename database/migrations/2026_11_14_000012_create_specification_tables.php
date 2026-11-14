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
        Schema::create('specification_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 191)->unique();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('specification_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('specification_groups')->cascadeOnDelete();
            $table->string('label', 150);
            $table->string('key', 191)->unique();
            $table->string('unit', 50)->nullable();
            $table->string('field_type', 40)->default('text'); // text, textarea, number, boolean, date, url, select, multivalue
            $table->string('help_text', 255)->nullable();
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('show_on_public')->default(true)->index();
            $table->boolean('is_filterable')->default(false)->index();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('device_specification_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('specification_fields')->cascadeOnDelete();
            $table->text('value')->nullable();
            $table->string('additional_info', 255)->nullable();
            $table->timestamps();

            $table->unique(['device_id', 'field_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_specification_values');
        Schema::dropIfExists('specification_fields');
        Schema::dropIfExists('specification_groups');
    }
};
