<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_documents', function (Blueprint $table) {

            $table->id();

            // 'manual_calidad' por ahora. Pensado para reutilizarse
            // después con 'procedimiento', 'documento_externo', etc.
            $table->string('type');

            $table->string('code')->nullable();
            $table->string('name');

            $table->string('reviewer_name')->nullable();
            $table->date('review_date')->nullable();

            $table->string('approver_name')->nullable();
            $table->date('approval_date')->nullable();

            $table->date('next_review_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_documents');
    }
};
