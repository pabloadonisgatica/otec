<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quality_documents', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });

        Schema::table('quality_document_versions', function (Blueprint $table) {
            // Alternativa a subir archivo: enlace externo
            // (Google Drive, etc.).
            $table->string('external_url')->nullable()->after('file_name');
        });

        // file_path / file_name pasan a ser opcionales (por si la
        // versión es un enlace externo en vez de un archivo subido).
        // SQL crudo en vez de ->change() porque el proyecto no
        // tiene instalado doctrine/dbal.
        DB::statement('ALTER TABLE quality_document_versions MODIFY file_path VARCHAR(255) NULL');
        DB::statement('ALTER TABLE quality_document_versions MODIFY file_name VARCHAR(255) NULL');
    }

    public function down(): void
    {
        Schema::table('quality_documents', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('quality_document_versions', function (Blueprint $table) {
            $table->dropColumn('external_url');
        });

        DB::statement('ALTER TABLE quality_document_versions MODIFY file_path VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE quality_document_versions MODIFY file_name VARCHAR(255) NOT NULL');
    }
};
