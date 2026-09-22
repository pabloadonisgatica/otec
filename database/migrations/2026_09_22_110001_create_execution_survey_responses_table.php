<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('execution_survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('execution_survey_id')
                ->constrained('execution_surveys')
                ->cascadeOnDelete();
            $table->timestamp('submitted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('execution_survey_responses');
    }
};
