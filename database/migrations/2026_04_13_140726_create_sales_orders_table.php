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
         Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();       // ex: CMD-2026-0001
            $table->string('client_name');
            $table->string('client_phone')->nullable();
            $table->string('client_email')->nullable();
            $table->text('client_address')->nullable();
            $table->date('order_date');
            $table->date('delivery_date')->nullable();
            $table->enum('status', [
                'pending',      // en attente
                'confirmed',    // confirmée
                'preparing',    // en préparation
                'shipped',      // expédiée
                'delivered',    // livrée
                'cancelled'     // annulée
            ])->default('pending');
            $table->decimal('total_amount', 12, 2)->default(0); // en DA
            $table->foreignId('commercial_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
