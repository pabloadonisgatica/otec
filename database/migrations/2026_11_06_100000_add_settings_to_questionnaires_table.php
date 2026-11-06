<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Configuración propia de cada instrumento. La usa la encuesta de satisfacción
 * libre: guarda el nombre del relator y una copia congelada de la plantilla.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questionnaires', function (Blueprint $table) {
            $table->json('settings')->nullable()->after('respondent_fields');
        });
    }

    public function down(): void
    {
        Schema::table('questionnaires', function (Blueprint $table) {
            $table->dropColumn('settings');
        });
    }
};
