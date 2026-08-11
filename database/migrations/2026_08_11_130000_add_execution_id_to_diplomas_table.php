<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diplomas', function (Blueprint $table) {

            // Un diploma corresponde a haber completado una
            // EJECUCIÓN concreta (fechas, empresa, participantes
            // reales), no solo la definición abstracta del Curso.
            // course_id se mantiene para consultas rápidas, pero
            // ahora se deriva de la ejecución al emitir.
            $table->foreignId('execution_id')
                ->nullable()
                ->after('template_id')
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('diplomas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('execution_id');
        });
    }
};
