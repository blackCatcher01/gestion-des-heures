<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Correctif audit — Priorités Haute et Critique
 *
 * 1) (Haute) La contrainte d'unicité `unicite_activite` portait sur
 *    (enseignant_id, cours_id, type_action) sans distinguer l'année
 *    académique. On ajoute `annee_academique_id` (dénormalisé depuis
 *    cours.annee_academique_id à la création de l'activité, voir
 *    App\Models\ActivitePedagogique) et on l'inclut dans la contrainte
 *    d'unicité.
 *
 * 2) (Critique) On ajoute calculation_inputs (JSON) et calculation_hash
 *    (HMAC-SHA256) pour permettre de détecter toute altération directe
 *    du volume_horaire en base via ActivitePedagogique::estIntegre().
 *
 * Remarque technique : l'index unique `unicite_activite` (qui commence
 * par enseignant_id) sert aussi d'index de support à la contrainte de
 * clé étrangère sur enseignant_id. MySQL refuse de le supprimer tant
 * qu'aucun autre index ne couvre cette colonne (erreur 1553) — on crée
 * donc un index dédié sur enseignant_id avant de toucher à l'unique.
 *
 * Chaque étape est protégée par une vérification d'existence, pour que
 * cette migration puisse être rejouée sans erreur même si une tentative
 * précédente s'est arrêtée en cours de route.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activites_pedagogiques', function (Blueprint $table) {
            if (! Schema::hasColumn('activites_pedagogiques', 'annee_academique_id')) {
                $table->foreignId('annee_academique_id')
                    ->nullable()
                    ->after('cours_id')
                    ->constrained('annees_academiques')
                    ->onDelete('restrict');
            }

            if (! Schema::hasColumn('activites_pedagogiques', 'calculation_inputs')) {
                $table->json('calculation_inputs')->nullable()->after('volume_horaire');
            }

            if (! Schema::hasColumn('activites_pedagogiques', 'calculation_hash')) {
                $table->string('calculation_hash', 64)->nullable()->after('calculation_inputs');
            }
        });

        // Rétro-remplissage des activités dont l'année académique
        // n'est pas encore renseignée.
        DB::table('activites_pedagogiques as a')
            ->join('cours as c', 'c.id', '=', 'a.cours_id')
            ->whereNull('a.annee_academique_id')
            ->update([
                'a.annee_academique_id' => DB::raw('c.annee_academique_id'),
            ]);

        // Index de support sur enseignant_id, nécessaire pour pouvoir
        // supprimer l'ancien index unique sans casser la clé étrangère.
        if (! Schema::hasIndex('activites_pedagogiques', 'activites_pedagogiques_enseignant_id_index')) {
            Schema::table('activites_pedagogiques', function (Blueprint $table) {
                $table->index('enseignant_id', 'activites_pedagogiques_enseignant_id_index');
            });
        }

        if (Schema::hasIndex('activites_pedagogiques', 'unicite_activite')) {
            Schema::table('activites_pedagogiques', function (Blueprint $table) {
                $table->dropUnique('unicite_activite');
            });
        }

        if (! Schema::hasIndex('activites_pedagogiques', 'unicite_activite')) {
            Schema::table('activites_pedagogiques', function (Blueprint $table) {
                $table->unique(
                    ['enseignant_id', 'cours_id', 'type_action', 'annee_academique_id'],
                    'unicite_activite'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::table('activites_pedagogiques', function (Blueprint $table) {
            if (Schema::hasIndex('activites_pedagogiques', 'unicite_activite')) {
                $table->dropUnique('unicite_activite');
            }

            $table->unique(
                ['enseignant_id', 'cours_id', 'type_action'],
                'unicite_activite'
            );
        });

        Schema::table('activites_pedagogiques', function (Blueprint $table) {
            if (Schema::hasIndex('activites_pedagogiques', 'activites_pedagogiques_enseignant_id_index')) {
                $table->dropIndex('activites_pedagogiques_enseignant_id_index');
            }

            if (Schema::hasColumn('activites_pedagogiques', 'annee_academique_id')) {
                $table->dropConstrainedForeignId('annee_academique_id');
            }

            $table->dropColumn(['calculation_inputs', 'calculation_hash']);
        });
    }
};
