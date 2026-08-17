<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quality_profiles', function (Blueprint $table) {

            // Cada uno guarda una sola imagen vigente (se reemplaza
            // al subir una nueva), no una lista como Documentación Legal.
            $table->string('process_map_title')->nullable()->after('legal_documents');
            $table->string('process_map_path')->nullable()->after('process_map_title');

            $table->string('org_chart_title')->nullable()->after('process_map_path');
            $table->string('org_chart_path')->nullable()->after('org_chart_title');
        });
    }

    public function down(): void
    {
        Schema::table('quality_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'process_map_title',
                'process_map_path',
                'org_chart_title',
                'org_chart_path',
            ]);
        });
    }
};
