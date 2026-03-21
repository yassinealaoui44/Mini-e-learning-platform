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
    Schema::create('cours', function (Blueprint $table) {
        // Primary Key (matching your diagram's naming)
        $table->id('id_cours'); 
        
        $table->string('titre');
        $table->text('description')->nullable();
        $table->string('statut')->default('brouillon'); // e.g., published, draft
        
        // Foreign Key: Links to the 'id_utilisateur' in the tuteurs table
        $table->foreignId('id_tuteur')
              ->constrained('tuteurs', 'id_utilisateur')
              ->onDelete('cascade');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cours');
    }
};
