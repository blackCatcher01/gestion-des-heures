<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Correctif audit — Priorité Haute
 *
 * La colonne `nombre_sequences` était une colonne générée MySQL
 * (STORED AS nombre_credits * 40), figeant la valeur 40 en dur alors
 * que `parametres_calcul.sequences_par_credit` est censée être
 * paramétrable par l'administrateur. Un changement de ce paramètre
 * ne se répercutait donc jamais sur les cours existants ni nouveaux,
 * créant une divergence entre la grille configurée et les volumes
 * horaires calculés.
 *
 * On supprime la colonne stockée : `nombre_sequences` est désormais
 * calculée à la lecture, dans App\Models\Cours, à partir de la valeur
 * courante de `sequences_par_credit`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cours', function (Blueprint $table) {
            $table->dropColumn('nombre_sequences');
        });
    }

    public function down(): void
    {
        Schema::table('cours', function (Blueprint $table) {
            $table->integer('nombre_sequences')
                ->storedAs('nombre_credits * 40');
        });
    }
};
