<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {

            $table->id();

            $table->foreignId('execution_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('participant_id')
                ->constrained()
                ->cascadeOnDelete();

            // Escala 1.0 - 7.0 (estándar chileno).
            $table->decimal('final_grade', 3, 1)->nullable();

            $table->timestamps();

            $table->unique(
                ['execution_id', 'participant_id'],
                'evaluations_execution_participant_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
