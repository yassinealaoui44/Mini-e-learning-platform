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
        Schema::create('utilisateurs', function (Blueprint $table) {
            // 'id' will be the Primary Key used by Etudiant and Tuteur
            $table->id('id_utilisateur'); 
            
            $table->string('nom');
            $table->string('email')->unique();
            
            // We use 'mot_de_passe' to match your diagram logic
            $table->string('mot_de_passe');
            
            // Standard Laravel timestamps (created_at, updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};