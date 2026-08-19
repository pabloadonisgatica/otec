<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_policies', function (Blueprint $table) {

            $table->id();

            $table->string('title_1')->nullable();   // Título 1
            $table->string('title_2')->nullable();   // Título 2
            $table->text('detail')->nullable();       // Detalle

            $table->string('signature_name')->nullable();     // Nombre Firma
            $table->string('signature_position')->nullable(); // Cargo Firma
            $table->date('approval_date')->nullable();        // Fecha Aprobación

            // Sube automáticamente al editar (mismo patrón que Control de Registros).
            $table->unsignedInteger('version')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_policies');
    }
};
