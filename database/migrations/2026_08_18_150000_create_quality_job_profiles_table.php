<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_job_profiles', function (Blueprint $table) {

            $table->id();

            $table->date('profile_date')->nullable();  // Fecha
            $table->string('position_name');            // Nombre del Puesto

            $table->text('functions')->nullable();       // Funciones y Tareas del Puesto
            $table->text('responsibilities')->nullable(); // Responsabilidades

            $table->string('area')->nullable();
            $table->string('reports_to')->nullable();     // Dependencia Directa De
            $table->string('direct_reports')->nullable(); // Personas a su Cargo

            $table->text('education_requirements')->nullable();  // Requisitos Educación
            $table->text('training_requirements')->nullable();   // Requisitos Formación
            $table->text('skills_requirements')->nullable();     // Requisitos Habilidades
            $table->text('experience_requirements')->nullable(); // Requisitos Experiencia

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_job_profiles');
    }
};
