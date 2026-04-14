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
    Schema::create('finished_goods', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');                 // ex: LUXMIEL Miel Sidre 20g
            $table->foreignId('brand_id')
                  ->constrained('brands')
                  ->restrictOnDelete();
            $table->foreignId('production_run_id')
                  ->nullable()
                  ->constrained('production_runs')
                  ->nullOnDelete();
            $table->string('batch_number');                 // copie du batch_number de la production
            $table->decimal('packet_size_g', 8, 2);        // taille du packet en grammes
            $table->string('packet_label');                 // ex: "20g Standard"
            $table->integer('quantity_in_stock')->default(0); // nombre de packets en stock
            $table->integer('min_stock_alert')->default(0);
            $table->date('production_date');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finished_goods');
    }
};
