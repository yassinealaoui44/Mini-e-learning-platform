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
    Schema::create('lecons', function (Blueprint $table) {
        // Primary Key
        $table->id('id_lecon');
        
        $table->string('titre');
        $table->text('contenu')->nullable();
        $table->string('video_url')->nullable(); // For your video lectures
        
        // Foreign Key: Links this lesson to a specific course
        $table->foreignId('id_cours')
              ->constrained('cours', 'id_cours')
              ->onDelete('cascade');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecons');
    }
};
