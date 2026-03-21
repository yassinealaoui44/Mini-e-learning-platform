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
        // 1. Link to the main user account
        $table->foreignId('id_utilisateur')
              ->constrained('utilisateurs')
              ->onDelete('cascade');

        // 2. Set the User ID as the Primary Key
        $table->primary('id_utilisateur');

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
