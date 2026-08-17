<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quality_profiles', function (Blueprint $table) {

            // Reemplaza el campo de texto libre "legal_documentation"
            // por un listado real de documentos subidos (mismo patrón
            // que ya usa Relatores: array de [label, path, name, ...]).
            $table->json('legal_documents')->nullable()->after('legal_documentation');
        });
    }

    public function down(): void
    {
        Schema::table('quality_profiles', function (Blueprint $table) {
            $table->dropColumn('legal_documents');
        });
    }
};
