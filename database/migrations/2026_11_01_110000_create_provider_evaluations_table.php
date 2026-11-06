<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * NCH 2728 — 7.4 Compras: evaluación, selección y reevaluación de proveedores.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20); // selection | reevaluation
            $table->date('evaluated_at');
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('scores');
            $table->decimal('score', 5, 2); // porcentaje 0–100
            $table->string('result', 20)->index(); // approved | conditional | rejected
            $table->text('observations')->nullable();
            $table->date('next_evaluation_at')->nullable();
            $table->timestamps();

            $table->index(['provider_id', 'evaluated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_evaluations');
    }
};
