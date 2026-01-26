<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('course_resources');
    }

    public function down(): void
    {
        Schema::create('course_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            // Si no recuerdas las columnas exactas, esto es suficiente para rollback básico.
            // Si quieres rollback perfecto, después me pegas la migración original y lo dejamos idéntico.
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }
};
