<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('execution_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('execution_id')
                ->constrained('executions')
                ->cascadeOnDelete();
            $table->foreignId('survey_template_id')
                ->constrained('survey_templates');
            $table->string('token', 64)->unique();
            $table->timestamps();

            // Una sola encuesta pública activa por ejecución.
            $table->unique('execution_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('execution_surveys');
    }
};
