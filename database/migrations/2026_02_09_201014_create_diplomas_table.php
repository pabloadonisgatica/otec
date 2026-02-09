<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('diplomas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('template_id')
                ->constrained('diploma_templates')
                ->cascadeOnDelete();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->foreignId('participant_id')
                ->constrained('participants')
                ->cascadeOnDelete();

            $table->string('code')->unique();
            $table->string('qr_path')->nullable();

            $table->timestamp('issued_at')->nullable();

            $table->json('snapshot');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diplomas');
    }
};
