<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_order_items', function (Blueprint $table) {
            $table->foreignId('packaging_box_id')->nullable()->constrained('packaging_boxes')->nullOnDelete()->after('finished_good_id');
            $table->enum('item_type', ['packet', 'box'])->default('packet')->after('packaging_box_id');
        });
    }

    public function down(): void
    {
        Schema::table('sale_order_items', function (Blueprint $table) {
            $table->dropForeign(['packaging_box_id']);
            $table->dropColumn(['packaging_box_id', 'item_type']);
        });
    }
};