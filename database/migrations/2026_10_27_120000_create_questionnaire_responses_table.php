<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_id')->constrained()->cascadeOnDelete();

            // Datos de quien responde (solo los que la encuesta pide).
            $table->string('first_name', 150)->nullable();
            $table->string('last_name', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('organization', 150)->nullable();

            // Resultado calculado de los instrumentos cerrados (pasos 5 y 6).
            $table->json('result')->nullable();

            $table->timestamps();

            $table->index(['questionnaire_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_responses');
    }
};
