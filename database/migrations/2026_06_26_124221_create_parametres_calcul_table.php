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
        Schema::create('parametres_calcul', function (Blueprint $table) {
            $table->id();
            $table->enum('type_action', ['creation', 'mise_a_jour']);
            $table->tinyInteger('niveau_contenu');   // 1, 2 ou 3
            $table->decimal('coefficient', 5, 3);   // ex: 0.750
            $table->integer('sequences_par_credit')->default(40);
            $table->timestamps();

            // Un seul coefficient par combinaison type × niveau
            $table->unique(['type_action', 'niveau_contenu']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametres_calcul');
    }
};
