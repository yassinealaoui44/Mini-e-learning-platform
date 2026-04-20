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
    Schema::create('inscriptions', function (Blueprint $table) {
        $table->id('id_inscription');

        // Link to the specific Student (using their unique ID from the etudiants table)
        $table->foreignId('id_etudiant')
              ->constrained('etudiants', 'id_utilisateur')
              ->onDelete('cascade');

        // Link to the Course
        $table->foreignId('id_cours')
              ->constrained('cours', 'id')
              ->onDelete('cascade');

        // Prevent a student from enrolling in the same course twice
        $table->unique(['id_etudiant', 'id_cours']);

        $table->timestamp('date_inscription')->useCurrent();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};
