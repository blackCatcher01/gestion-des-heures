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
        Schema::create('cours', function (Blueprint $table) {
            $table->id();
            $table->string('intitule');
            $table->string('filiere', 50);
            $table->enum('niveau', ['L1', 'L2', 'L3', 'M1', 'M2']);
            $table->tinyInteger('semestre');           // 1 ou 2
            $table->tinyInteger('nombre_credits');
            $table->integer('nombre_sequences')
                ->storedAs('nombre_credits * 40');   // Calculé automatiquement

            $table->foreignId('annee_academique_id')
                ->constrained('annees_academiques')
                ->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cours');
    }
};
