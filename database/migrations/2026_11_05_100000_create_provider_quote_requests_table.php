<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * NCH 2728 — 7.4 Compras: solicitud de cotización a proveedores
 * (información de compra) y registro de la respuesta.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_quote_requests', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->foreignId('provider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('execution_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();

            // Solicitud
            $table->date('requested_at');
            $table->date('needed_by')->nullable();
            $table->json('items');
            $table->text('conditions')->nullable();

            // Respuesta del proveedor
            $table->string('status', 20)->default('sent')->index();
            $table->date('responded_at')->nullable();
            $table->unsignedBigInteger('quoted_amount')->nullable(); // CLP
            $table->text('response_notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_quote_requests');
    }
};
