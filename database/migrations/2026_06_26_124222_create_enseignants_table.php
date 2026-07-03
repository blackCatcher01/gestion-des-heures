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
        Schema::create('enseignants', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->enum('grade', [
                'assistant',
                'maitre_assistant',
                'maitre_conferences',
                'professeur'
            ]);
            $table->enum('statut', ['permanent', 'vacataire']);
            $table->integer('taux_horaire')->default(0);  // FCFA/heure
            $table->string('telephone', 20)->nullable();

            // Clés étrangères
            $table->foreignId('user_id')
                ->unique()                       // 1 user = 1 enseignant max
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('departement_id')
                ->constrained('departements')
                ->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enseignants');
    }
};
