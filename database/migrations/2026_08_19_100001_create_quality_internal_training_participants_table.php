<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_internal_training_participants', function (Blueprint $table) {

            $table->id();

            $table->foreignId('quality_internal_training_id')
                ->constrained(indexName: 'qi_training_participants_training_id_fk')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('position')->nullable(); // Cargo

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_internal_training_participants');
    }
};
