<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_id')->constrained()->cascadeOnDelete();
            $table->foreignId('questionnaire_section_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30);
            $table->string('label', 500);
            $table->string('help', 1000)->nullable();
            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable();   // opciones de las preguntas de selección
            $table->json('settings')->nullable();  // {"min_label": "...", "max_label": "..."} en escalas
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_questions');
    }
};
