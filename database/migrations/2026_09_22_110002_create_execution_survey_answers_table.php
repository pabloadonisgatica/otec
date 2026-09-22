<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('execution_survey_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('execution_survey_response_id')
                ->constrained('execution_survey_responses')
                ->cascadeOnDelete();
            $table->foreignId('survey_template_field_id')
                ->constrained('survey_template_fields');
            // Solo se llena cuando el campo pertenece a una sección
            // que se repite por relator.
            $table->foreignId('instructor_id')
                ->nullable()
                ->constrained('instructors')
                ->nullOnDelete();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('execution_survey_answers');
    }
};
