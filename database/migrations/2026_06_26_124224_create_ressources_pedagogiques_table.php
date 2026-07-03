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
        Schema::create('ressources_pedagogiques', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->enum('type', [
                'pdf', 'video', 'quiz', 'interactif', 'evaluation'
            ]);
            $table->string('url_moodle')->nullable();

            $table->foreignId('sequence_id')
                ->constrained('sequences_pedagogiques')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ressources_pedagogiques');
    }
};
