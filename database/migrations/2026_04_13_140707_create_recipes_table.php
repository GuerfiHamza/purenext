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
          Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name');                         // ex: LUXMIEL Miel Sidre Sticks
            $table->foreignId('brand_id')
                  ->constrained('brands')
                  ->cascadeOnDelete();
            $table->string('version')->default('1.0');      // versioning des recettes
            $table->enum('yield_unit', ['packet', 'kg', 'piece', 'box'])->default('packet');
            $table->decimal('yield_qty', 10, 3)->default(1); // quantité produite par recette de base
            $table->decimal('loss_percentage', 5, 2)->default(0); // % perte traitement (ex: 5% pour LUXMIEL)
            $table->text('notes')->nullable();
            $table->json('technical_params')->nullable();   // paramètres techniques libres (temp, humidité...)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
