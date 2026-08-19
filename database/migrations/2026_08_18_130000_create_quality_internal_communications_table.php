<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_internal_communications', function (Blueprint $table) {

            $table->id();

            // Array: ['politica','requisitos_norma','objetivos','desempeno']
            $table->json('topics');

            $table->string('channel'); // Reunión, Correo, Diario Mural, etc.
            $table->string('audience'); // A quién (texto libre)

            $table->date('communicated_at');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_internal_communications');
    }
};
