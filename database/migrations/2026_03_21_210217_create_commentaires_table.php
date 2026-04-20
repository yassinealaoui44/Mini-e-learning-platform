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
        Schema::create('commentaires', function (Blueprint $table) {
            $table->id('id_commentaire');
            $table->text('contenu');

            // Link to the Lesson
            $table->foreignId('id_lecon')
                  ->constrained('lecons', 'id')
                  ->onDelete('cascade');

            // Link to the Student
            $table->foreignId('id_etudiant')
                  ->constrained('etudiants', 'id_utilisateur')
                  ->onDelete('cascade');

            $table->timestamp('date_commentaire')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commentaires');
    }
};
