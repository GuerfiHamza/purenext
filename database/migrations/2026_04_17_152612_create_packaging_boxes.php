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
    Schema::create('packaging_boxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finished_good_id')->constrained('finished_goods')->cascadeOnDelete();
            $table->string('name');           // ex: "Boite 10 capsules"
            $table->unsignedInteger('units_per_box'); // ex: 10 ou 20
            $table->string('label')->nullable();      // ex: "BTB-10"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packaging_boxes');
    }
};
