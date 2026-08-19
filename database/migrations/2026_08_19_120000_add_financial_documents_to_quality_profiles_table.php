<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quality_profiles', function (Blueprint $table) {

            // Mismo patrón que legal_documents: lista de documentos,
            // no un único documento versionado.
            $table->json('financial_documents')->nullable()->after('org_chart_path');
        });
    }

    public function down(): void
    {
        Schema::table('quality_profiles', function (Blueprint $table) {
            $table->dropColumn('financial_documents');
        });
    }
};
