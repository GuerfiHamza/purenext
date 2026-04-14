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
     Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->enum('movable_type', ['raw_material', 'finished_good']);
            $table->unsignedBigInteger('movable_id');       // ID de la matière ou du PF concerné
            $table->enum('type', [
                'in',           // entrée (réception fournisseur, ajustement +)
                'out',          // sortie (consommation production, vente)
                'adjustment'    // correction manuelle (inventaire physique)
            ]);
            $table->decimal('quantity', 12, 4);             // toujours positif, le type indique le sens
            $table->enum('unit', ['kg', 'g', 'l', 'ml', 'piece', 'box', 'm2', 'm'])
                  ->default('kg');
            $table->decimal('stock_before', 12, 4);         // snapshot avant mouvement
            $table->decimal('stock_after', 12, 4);          // snapshot après mouvement
            $table->string('reason')->nullable();            // ex: "Lancement production LXMS-2026-0001"
            $table->nullableMorphs('source');               // lien optionnel vers production_run ou sales_order
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();
 
            $table->index(['movable_type', 'movable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
