<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_template_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_template_section_id')
                ->constrained('survey_template_sections')
                ->cascadeOnDelete();
            $table->string('label');
            // input | radio | select | textarea
            $table->string('type');
            // Opciones para radio/select, ej: ["3","4","5","6","7"]
            $table->json('options')->nullable();
            $table->boolean('required')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_template_fields');
    }
};
