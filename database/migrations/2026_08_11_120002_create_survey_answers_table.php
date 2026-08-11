<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_answers', function (Blueprint $table) {

            $table->id();

            $table->foreignId('survey_response_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('survey_question_id')
                ->constrained()
                ->cascadeOnDelete();

            // 1-7, null = "N/A" (no aplica).
            $table->unsignedTinyInteger('score')->nullable();

            $table->timestamps();

            $table->unique(
                ['survey_response_id', 'survey_question_id'],
                'survey_answers_response_question_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_answers');
    }
};
