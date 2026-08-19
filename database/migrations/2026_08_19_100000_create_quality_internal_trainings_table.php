<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_internal_trainings', function (Blueprint $table) {

            $table->id();

            $table->date('activity_date');
            $table->string('activity_name');
            $table->text('objective')->nullable();
            $table->string('instructor_name')->nullable(); // Nombre del Relator
            $table->unsignedInteger('hours')->nullable();

            $table->boolean('objective_met')->nullable(); // Cumplimiento de Objetivo (Sí/No)
            $table->text('compliance_description')->nullable();
            $table->text('additional_actions')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_internal_trainings');
    }
};
