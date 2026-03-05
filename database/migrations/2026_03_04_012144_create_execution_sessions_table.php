<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('execution_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('execution_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');

            $table->decimal('hours', 5, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('execution_sessions');
    }
};