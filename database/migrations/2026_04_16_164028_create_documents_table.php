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
        Schema::create('documents', function (Blueprint $table) {
        $table->id();
        $table->string('type'); // rapport_production | certificat_conformite | fiche_technique
        $table->string('reference')->unique(); // DOC-20260416-001
        $table->nullableMorphs('documentable'); // lié à ProductionRun, Recipe, etc.
        $table->json('data'); // snapshot des données au moment de la génération
        $table->string('file_path')->nullable(); // storage/app/documents/xxx.pdf
        $table->foreignId('generated_by')->constrained('users');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
