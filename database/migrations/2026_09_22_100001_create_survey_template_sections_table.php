<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_template_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_template_id')
                ->constrained('survey_templates')
                ->cascadeOnDelete();
            $table->string('title');
            // Si es true, esta sección se repite una vez por cada relator
            // asignado a la ejecución (ej. "De los Relatores").
            $table->boolean('repeats_per_instructor')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_template_sections');
    }
};
