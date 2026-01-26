<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->string('code')->nullable()->unique();
            $table->string('name');
            $table->string('sence_code')->nullable();

            $table->unsignedSmallInteger('hours')->nullable();
            $table->string('modality')->nullable(); // presencial|online|mixto (lo validamos en Request)

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->string('status')->default('draft'); // draft|planned|ongoing|completed|cancelled
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
