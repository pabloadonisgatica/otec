<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_records', function (Blueprint $table) {

            $table->id();

            $table->string('name'); // Nombre Registro

            // Sube automáticamente cada vez que se edita el registro.
            $table->unsignedInteger('version')->default(1);

            $table->string('approval_user')->nullable();   // Usuario Aprobación
            $table->date('approval_date')->nullable();     // Fecha Aprobación

            $table->string('protection')->nullable();       // Protección
            $table->string('storage_location')->nullable(); // Almacenamiento
            $table->string('retention_time')->nullable();   // Tiempo Retención
            $table->string('recovery')->nullable();         // Recuperación
            $table->string('final_disposition')->nullable(); // Disposición Final

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_records');
    }
};
