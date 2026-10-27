<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cada respuesta guarda una copia del enunciado y del tipo de la pregunta:
 * si después la pregunta se edita o se elimina, el resultado histórico no cambia.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_response_id')->constrained()->cascadeOnDelete();
            $table->foreignId('questionnaire_question_id')->nullable()->constrained()->nullOnDelete();
            $table->string('question_label', 500);
            $table->string('question_type', 30);
            $table->text('value')->nullable();           // texto, opción, fecha, Sí/No, número
            $table->json('choices')->nullable();         // opción múltiple
            $table->smallInteger('numeric_value')->nullable(); // escalas y NPS (para promedios)

            $table->index('questionnaire_question_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_answers');
    }
};
