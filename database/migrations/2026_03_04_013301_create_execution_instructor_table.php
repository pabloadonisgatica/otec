<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('execution_instructor', function (Blueprint $table) {

            $table->foreignId('execution_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('instructor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->primary(['execution_id', 'instructor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('execution_instructor');
    }
};