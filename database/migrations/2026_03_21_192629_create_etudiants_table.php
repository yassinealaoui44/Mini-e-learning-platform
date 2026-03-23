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
        // DO NOT use $table->id() here!
        
        // We define the column, make it the primary key, 
        // and link it to the utilisateurs table all in one go.
        $table->foreignId('id_utilisateur')
              ->primary() 
              ->constrained('utilisateurs', 'id_utilisateur') 
              ->onDelete('cascade');

        $table->string('cne')->unique()->nullable(); // Student ID
        $table->string('filiere')->nullable();       // e.g., 'IIR'
        $table->string('niveau')->nullable();        // e.g., '3AP'
        
        $table->timestamps();

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
