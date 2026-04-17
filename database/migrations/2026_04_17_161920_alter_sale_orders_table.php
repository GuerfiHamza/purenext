<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete()->after('commercial_id');
            $table->string('client_rc')->nullable()->after('client_address');
            $table->string('client_nif')->nullable()->after('client_rc');
            $table->string('client_nis')->nullable()->after('client_nif');
            $table->string('client_ai')->nullable()->after('client_nis');
            $table->enum('client_type', ['particulier', 'societe'])->default('particulier')->after('client_name');
        });
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn(['client_id', 'client_rc', 'client_nif', 'client_nis', 'client_ai', 'client_type']);
        });
    }
};