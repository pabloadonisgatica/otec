<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_participant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();

            $table->string('enrollment_status')->default('enrolled'); 
            // enrolled|withdrawn|completed (base simple)

            $table->timestamps();

            $table->unique(['course_id', 'participant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_participant');
    }
};
