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
         Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')
                  ->constrained('recipes')
                  ->cascadeOnDelete();
            $table->foreignId('raw_material_id')
                  ->constrained('raw_materials')
                  ->cascadeOnDelete();
            $table->decimal('quantity', 12, 4);             // quantité par unité de recette de base
            $table->enum('unit', ['kg', 'g', 'l', 'ml', 'piece', 'box', 'm2', 'm'])
                  ->default('kg');
            $table->integer('order')->default(0);           // ordre de traitement (ex: EPICO: coriandre → piment)
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredients');
    }
};
