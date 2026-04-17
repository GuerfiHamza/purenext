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
        Schema::create('box_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('packaging_box_id')->constrained('packaging_boxes')->cascadeOnDelete();
            $table->unsignedInteger('quantity_in_stock')->default(0);
            $table->unsignedInteger('min_stock_alert')->default(10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('box_stocks');
    }
};
