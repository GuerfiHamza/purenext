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
          Schema::create('production_run_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_run_id')
                  ->constrained('production_runs')
                  ->cascadeOnDelete();
            $table->foreignId('raw_material_id')
                  ->constrained('raw_materials')
                  ->restrictOnDelete();
            $table->decimal('quantity_consumed', 12, 4);    // quantité réellement prélevée du stock
            $table->enum('unit', ['kg', 'g', 'l', 'ml', 'piece', 'box', 'm2', 'm'])
                  ->default('kg');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_run_ingredients');
    }
};
