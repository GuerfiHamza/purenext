<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_runs', function (Blueprint $table) {
            // Renommer batch_number → lot_number (alias plus métier)
            // On garde batch_number ET on ajoute lot_number séparé
            $table->string('lot_number')->nullable()->after('batch_number');
        });

        // Initialiser lot_number = batch_number pour les runs existants
        DB::statement('UPDATE production_runs SET lot_number = batch_number WHERE lot_number IS NULL');

        Schema::table('production_runs', function (Blueprint $table) {
            $table->string('lot_number')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('production_runs', function (Blueprint $table) {
            $table->dropColumn('lot_number');
        });
    }
};