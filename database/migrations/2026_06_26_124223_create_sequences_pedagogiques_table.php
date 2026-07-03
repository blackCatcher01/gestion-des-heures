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
        Schema::create('sequences_pedagogiques', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->integer('numero_ordre');
            $table->text('description')->nullable();

            $table->foreignId('cours_id')
                ->constrained('cours')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sequences_pedagogiques');
    }
};
