<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Datos manuales del Informe de la ejecución (los que no existen en
 * el sistema): orden de compra, factura, invitados, observaciones y
 * sugerencias. El resto del informe se calcula en vivo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('execution_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('execution_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('purchase_order')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('guests')->nullable();
            $table->text('observations')->nullable();
            $table->text('suggestions')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('execution_reports');
    }
};
