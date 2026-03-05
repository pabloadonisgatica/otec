<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('execution_participant', function (Blueprint $table) {

            $table->foreignId('execution_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('participant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->primary(['execution_id', 'participant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('execution_participant');
    }
};