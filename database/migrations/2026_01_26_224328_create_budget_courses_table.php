<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('budget_courses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('budget_id')->constrained('budgets')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->restrictOnDelete();

            // Solo número (no relación con participantes)
            $table->unsignedInteger('participants')->default(0);
            $table->unsignedInteger('hours')->default(0);

            $table->unsignedBigInteger('unit_price')->default(0); // CLP por participante
            $table->unsignedTinyInteger('discount_percent')->nullable(); // 0-100

            // Relator desde BD
            $table->foreignId('instructor_id')->nullable()->constrained('instructors')->nullOnDelete();

            $table->unsignedBigInteger('line_total')->default(0);

            $table->unsignedInteger('sort_order')->nullable();

            $table->timestamps();

            $table->index(['budget_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_courses');
    }
};

