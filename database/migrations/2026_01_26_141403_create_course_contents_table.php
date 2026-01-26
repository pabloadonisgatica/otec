<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            $table->text('activity')->nullable();
            $table->text('content')->nullable();

            $table->unsignedSmallInteger('hours_theoretical')->nullable();
            $table->unsignedSmallInteger('hours_practical')->nullable();
            $table->unsignedSmallInteger('hours_elearning')->nullable();

            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_contents');
    }
};
