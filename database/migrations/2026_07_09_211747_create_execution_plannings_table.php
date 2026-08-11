<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('execution_plannings', function (Blueprint $table) {

            $table->id();

            $table->foreignId('execution_id')
                ->constrained()
                ->cascadeOnDelete();

            // automatic | manual
            $table->enum('mode', [
                'automatic',
                'manual'
            ])->default('automatic');

            // Fecha desde la que comienza la planificación
            $table->date('start_date');

            // Hora habitual de inicio
            $table->time('start_time');

            // Horas por jornada
            $table->decimal('hours_per_day', 5, 2);

            /*
            |--------------------------------------------------------------------------
            | Días de ejecución
            |--------------------------------------------------------------------------
            | 1=Lunes ... 7=Domingo
            |
            | Se almacenan como JSON:
            | [1,2,3,4,5]
            */
            $table->json('week_days');

            $table->boolean('exclude_holidays')
                ->default(true);

            $table->timestamps();

            $table->unique('execution_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('execution_plannings');
    }
};