<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // low_stock_mp, low_stock_pf, production_complete, order_delivered
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // données supplémentaires
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_global')->default(true); // visible par tous
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};