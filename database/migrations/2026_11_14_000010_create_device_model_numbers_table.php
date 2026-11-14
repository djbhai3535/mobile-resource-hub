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
        Schema::create('device_model_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->string('model_number', 100)->index();
            $table->string('region', 100)->nullable();
            $table->string('market', 100)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->unique(['device_id', 'model_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_model_numbers');
    }
};
