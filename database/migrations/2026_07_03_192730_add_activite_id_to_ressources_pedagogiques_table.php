<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Association MCD "porter" : une activité pédagogique (0,N) peut porter
     * sur des ressources pédagogiques ; une ressource pédagogique est
     * rattachée à 0 ou 1 activité (0,1).
     */
    public function up(): void
    {
        Schema::table('ressources_pedagogiques', function (Blueprint $table) {
            $table->foreignId('activite_id')
                ->nullable()
                ->after('sequence_id')
                ->constrained('activites_pedagogiques')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ressources_pedagogiques', function (Blueprint $table) {
            $table->dropConstrainedForeignId('activite_id');
        });
    }
};
