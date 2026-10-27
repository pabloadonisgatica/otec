<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Encuestas independientes (módulo tipo SurveyMonkey).
 * Distinto de survey_templates / execution_surveys (encuestas de curso por relator).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaires', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30)->default('custom'); // custom | group_evaluation | leadership
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('token', 40)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamp('closes_at')->nullable();

            // Asociación opcional (una sola), para ordenar y filtrar resultados.
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('execution_id')->nullable()->constrained()->nullOnDelete();

            // Datos que se piden a quien responde: {"email": "required", "organization": "optional"}
            $table->json('respondent_fields')->nullable();
            $table->string('banner_path')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaires');
    }
};
