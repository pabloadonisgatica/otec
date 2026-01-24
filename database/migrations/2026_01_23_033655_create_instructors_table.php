<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();

            // Identificación
            $table->string('rut')->unique();
            $table->string('name');                 // nombre completo
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            // Info académica / perfil
            $table->string('profession')->nullable(); // profesión/título
            $table->text('bio')->nullable();          // resumen

            // Documentos (por ahora guardamos JSON con rutas)
            $table->json('documents')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructors');
    }
};
