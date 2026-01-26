<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('course_resources');
    }

    public function down(): void
    {
        Schema::create('course_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            $table->string('type'); 
            // support_media | participant_material | equipment | consumables

            $table->string('description');
            $table->unsignedInteger('quantity')->nullable();

            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['course_id', 'type']);
        });
    }
};
