<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_responses', function (Blueprint $table) {

            $table->id();

            $table->foreignId('execution_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('participant_id')
                ->constrained()
                ->cascadeOnDelete();

            // Identifica el link personalizado enviado por correo.
            // No requiere login: el token ES la autenticación.
            $table->string('token', 64)->unique();

            $table->text('suggestions')->nullable();

            // null = todavía no ha respondido.
            $table->timestamp('submitted_at')->nullable();

            $table->timestamp('invited_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['execution_id', 'participant_id'],
                'survey_responses_execution_participant_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};
