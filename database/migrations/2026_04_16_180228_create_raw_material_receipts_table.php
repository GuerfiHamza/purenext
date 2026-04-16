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
    Schema::create('raw_material_receipts', function (Blueprint $table) {
        $table->id();
        $table->string('receipt_number')->unique(); // REC-20260416-001
        $table->foreignId('raw_material_id')->constrained()->cascadeOnDelete();
        $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
        $table->string('supplier_name'); // copie du nom (saisie libre ou depuis supplier)
        $table->string('supplier_lot')->nullable(); // N° lot fournisseur
        $table->decimal('quantity', 10, 3);
        $table->string('unit');
        $table->decimal('unit_cost', 10, 2)->nullable();
        $table->date('reception_date');
        $table->date('dluo_date')->nullable();
        $table->decimal('temperature', 5, 2)->nullable();
        $table->decimal('humidity', 5, 2)->nullable();
        $table->enum('visual_check', ['conforme', 'non_conforme'])->default('conforme');
        $table->enum('smell_check', ['conforme', 'non_conforme'])->default('conforme');
        $table->decimal('refractometer_brix', 5, 2)->nullable();
        $table->decimal('refractometer_humidity', 5, 2)->nullable();
        $table->enum('decision', ['accepted', 'refused', 'accepted_reserve'])->default('accepted');
        $table->string('storage_zone')->nullable();
        $table->string('storage_location')->nullable();
        $table->text('notes')->nullable();
        $table->foreignId('received_by')->constrained('users');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_material_receipts');
    }
};
