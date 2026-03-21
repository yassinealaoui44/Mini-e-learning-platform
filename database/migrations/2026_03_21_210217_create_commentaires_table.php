public function up(): void
{
    Schema::create('commentaires', function (Blueprint $table) {
        $table->id('id_commentaire');
        $table->text('contenu');

        // Link to the Lesson being commented on
        $table->foreignId('id_lecon')
              ->constrained('lecons', 'id_lecon')
              ->onDelete('cascade');

        // Link to the Student who wrote the comment
        $table->foreignId('id_etudiant')
              ->constrained('etudiants', 'id_utilisateur')
              ->onDelete('cascade');

        $table->timestamp('date_commentaire')->useCurrent();
        $table->timestamps();
    });
}