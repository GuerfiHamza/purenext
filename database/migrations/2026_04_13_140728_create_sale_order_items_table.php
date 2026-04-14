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
        Schema::create('sale_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')
                  ->constrained('sales_orders')
                  ->cascadeOnDelete();
            $table->foreignId('finished_good_id')
                  ->constrained('finished_goods')
                  ->restrictOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);           // prix unitaire en DA au moment de la commande
            $table->decimal('total_price', 12, 2)
                  ->storedAs('quantity * unit_price');       // colonne calculée
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_order_items');
    }
};
