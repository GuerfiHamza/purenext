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
          Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // ex: Miel Sidre, Café Arabica, Capsules alu
            $table->string('sku')->unique()->nullable();     // code interne
            $table->foreignId('brand_id')
                  ->nullable()
                  ->constrained('brands')
                  ->nullOnDelete();                          // null = matière partagée entre marques
            $table->foreignId('supplier_id')
                  ->nullable()
                  ->constrained('suppliers')
                  ->nullOnDelete();
            $table->enum('unit', ['kg', 'g', 'l', 'ml', 'piece', 'box', 'm2', 'm'])
                  ->default('kg');                           // unité de mesure stock
            $table->decimal('quantity_in_stock', 12, 3)->default(0);
            $table->decimal('min_stock_alert', 12, 3)->default(0); // seuil d'alerte bas
            $table->decimal('cost_per_unit', 10, 2)->nullable();   // coût unitaire en DA
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_materials');
    }
};
