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
         Schema::create('production_runs', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique();       // ex: LXMS-2026-0001
            $table->foreignId('recipe_id')
                  ->constrained('recipes')
                  ->restrictOnDelete();
            $table->foreignId('recipe_packaging_id')
                  ->constrained('recipe_packaging')
                  ->restrictOnDelete();
            $table->foreignId('operator_id')
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->decimal('input_qty_kg', 12, 3);         // quantité MP entrante en kg
            $table->integer('output_packets_estimated');    // packets calculés par le simulateur
            $table->integer('output_packets_actual')->nullable(); // packets réellement produits
            $table->decimal('loss_actual_percentage', 5, 2)->nullable(); // perte réelle constatée
            $table->enum('status', [
                'simulated',    // simulation faite, pas encore lancé
                'in_progress',  // en cours de production
                'completed',    // terminé
                'cancelled'     // annulé
            ])->default('simulated');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_runs');
    }
};
