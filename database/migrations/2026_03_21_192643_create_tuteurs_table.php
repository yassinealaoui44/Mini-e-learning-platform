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
    Schema::create('tuteurs', function (Blueprint $table) {
        // We tell Laravel: "Look in 'utilisateurs' for the column 'id_utilisateur'"
        $table->foreignId('id_utilisateur')
              ->constrained('utilisateurs', 'id_utilisateur') 
              ->onDelete('cascade');

        // Set the User ID as the Primary Key
        $table->primary('id_utilisateur');
        $table->string('matricule')->unique()->nullable();
        $table->string('specialite')->nullable(); // e.g., 'Cloud Computing'
        $table->text('biographie')->nullable();
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuteurs');
    }
};
