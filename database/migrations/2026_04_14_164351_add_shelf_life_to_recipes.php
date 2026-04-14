<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->unsignedSmallInteger('shelf_life_value')->nullable()->after('loss_percentage');
            $table->enum('shelf_life_unit', ['days', 'months'])->nullable()->after('shelf_life_value');
        });
    }

    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn(['shelf_life_value', 'shelf_life_unit']);
        });
    }
};