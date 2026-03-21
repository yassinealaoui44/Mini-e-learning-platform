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
        // 1. Create the link to the base 'utilisateurs' table
        $table->foreignId('id_utilisateur')
              ->constrained('utilisateurs')
              ->onDelete('cascade');

        // 2. Set this foreign key as the Primary Key for this table
        $table->primary('id_utilisateur'); 

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
