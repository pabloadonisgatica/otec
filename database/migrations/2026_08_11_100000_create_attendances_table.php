<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {

            $table->id();

            $table->foreignId('execution_session_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('participant_id')
                ->constrained()
                ->cascadeOnDelete();

            // false = ausente (valor por defecto hasta que
            // el relator/administrativo pase la lista).
            $table->boolean('present')->default(false);

            $table->text('observations')->nullable();

            $table->timestamps();

            $table->unique(
                ['execution_session_id', 'participant_id'],
                'attendances_session_participant_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
