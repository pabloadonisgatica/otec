<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_job_profile_procedure', function (Blueprint $table) {

            $table->id();

            $table->foreignId('quality_job_profile_id')
                ->constrained()
                ->cascadeOnDelete();

            // Referencia a quality_documents (type = 'procedimiento')
            $table->foreignId('quality_document_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['quality_job_profile_id', 'quality_document_id'], 'job_profile_procedure_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_job_profile_procedure');
    }
};
