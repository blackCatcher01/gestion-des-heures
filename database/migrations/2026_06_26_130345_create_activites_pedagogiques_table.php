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
        Schema::create('activites_pedagogiques', function (Blueprint $table) {
            $table->id();
            $table->enum('type_action', ['creation', 'mise_a_jour']);
            $table->tinyInteger('niveau_contenu');      // 1, 2 ou 3
            $table->decimal('volume_horaire', 8, 2);    // calculé par Laravel
            $table->enum('statut', [
                'en_attente', 'valide', 'rejete', 'verrouille'
            ])->default('en_attente');
            $table->timestamp('date_saisie')->useCurrent();
            $table->timestamp('date_validation')->nullable();
            $table->text('commentaire_rejet')->nullable();

            // Clés étrangères
            $table->foreignId('enseignant_id')
                ->constrained('enseignants')
                ->onDelete('restrict');

            $table->foreignId('cours_id')
                ->constrained('cours')
                ->onDelete('restrict');

            $table->foreignId('validateur_id')          // Secrétaire ou Admin
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            // Contrainte d'unicité métier :
            // un enseignant ne peut déclarer qu'UNE activité par cours par type par année
            $table->unique(
                ['enseignant_id', 'cours_id', 'type_action'],
                'unicite_activite'
            );

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activites_pedagogiques');
    }
};
