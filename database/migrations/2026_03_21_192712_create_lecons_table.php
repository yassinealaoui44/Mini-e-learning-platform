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
        $table->id();
        $table->string('titre');
        $table->enum('type', ['pdf', 'video']); // Restricts to these two types
        $table->string('file_path'); // Stores path to PDF or Video
        $table->foreignId('cours_id')->constrained('cours')->onDelete('cascade');
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
