<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('executions', function (Blueprint $table) {
            $table->id();

            // Código interno autogenerado (EJ-2026-001)
            $table->string('internal_code')->unique();

            // Relaciones principales
            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            // Snapshots del curso
            $table->string('course_name');
            $table->string('modality');

            // Tipo de evaluación
            $table->enum('evaluation_type', ['percentage', 'grade']);

            // Ubicación (se conectará luego a regions y cities)
            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('place')->nullable();

            // Fecha inicio
            $table->date('start_date');

            // Estado operativo
            $table->enum('status', [
                'planificada',
                'en_ejecucion',
                'finalizada',
                'cancelada'
            ])->default('planificada');

            $table->text('observations')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('executions');
    }
};