<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('execution_checklist_items', function (Blueprint $table) {

            $table->id();

            $table->string('section');     // Ej: "1. Chequeo de Diseño"
            $table->string('subsection');  // Ej: "General"
            $table->string('label');

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('execution_checklist_items');
    }
};
