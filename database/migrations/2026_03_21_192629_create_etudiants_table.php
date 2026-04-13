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
    Schema::create('etudiants', function (Blueprint $table) {
        $table->unsignedBigInteger('id_utilisateur')->primary();
        $table->string('filiere'); // Added Filiere
        $table->string('niveau');  // Added Niveau
        $table->timestamps();

        $table->foreign('id_utilisateur')->references('id_utilisateur')->on('utilisateurs')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etudiants');
    }
};
