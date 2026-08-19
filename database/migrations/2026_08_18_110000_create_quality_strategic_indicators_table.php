<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_strategic_indicators', function (Blueprint $table) {

            $table->id();

            $table->foreignId('quality_document_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');  // Ej: "Ventas Proyectadas"
            $table->string('value'); // Texto libre — puede ser número, %, etc.

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_strategic_indicators');
    }
};
