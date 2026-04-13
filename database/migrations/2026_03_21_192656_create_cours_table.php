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
        $table->id();
        $table->string('nom');
        $table->string('filiere');
        $table->string('thumbnail')->nullable(); // Stores path to image
        $table->unsignedBigInteger('id_tuteur'); // Owner of the course
        $table->timestamps();

        $table->foreign('id_tuteur')->references('id_utilisateur')->on('tuteurs')->onDelete('cascade');
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
