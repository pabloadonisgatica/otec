<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_representatives', function (Blueprint $table) {

            $table->id();

            $table->string('name');
            $table->string('position')->nullable(); // Cargo

            $table->date('start_date');
            $table->date('end_date')->nullable(); // null = representante actual

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_representatives');
    }
};
