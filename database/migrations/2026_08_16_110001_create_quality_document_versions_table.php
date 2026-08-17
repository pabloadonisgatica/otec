<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_document_versions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('quality_document_id')
                ->constrained()
                ->cascadeOnDelete();

            // Correlativo por documento: 1, 2, 3...
            $table->unsignedInteger('version_number');

            $table->string('file_path');
            $table->string('file_name');

            $table->text('observation')->nullable();

            $table->string('uploaded_by')->nullable();
            $table->timestamp('uploaded_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_document_versions');
    }
};
