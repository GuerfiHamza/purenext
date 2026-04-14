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
        Schema::create('recipe_packaging', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')
                  ->constrained('recipes')
                  ->cascadeOnDelete();
            $table->decimal('packet_size_g', 8, 2);         // taille du packet en grammes (ex: 20)
            $table->string('packet_label');                 // ex: "20g Standard - Hôtels"
            $table->string('film_type')->nullable();        // ex: PET/ALU/PE, film rétractable
            $table->decimal('film_width_mm', 6, 1)->nullable();  // largeur du film
            $table->decimal('film_length_mm', 6, 1)->nullable(); // longueur de découpe (ex: 150mm pour LUXMIEL)
            $table->decimal('machine_capacity_per_hour', 10, 2)->nullable(); // packets/heure machine
            $table->boolean('is_default')->default(false);  // option par défaut pour cette recette
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_packaging');
    }
};
