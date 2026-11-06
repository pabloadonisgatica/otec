<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * NCH 2728 — 7.2 Procesos relacionados con el cliente.
 * Levantamiento del requerimiento (7.2.1) y su revisión (7.2.2).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_requirement_intakes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();

            // 7.2.1 Determinación del requisito
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 50)->nullable();
            $table->date('requested_at');
            $table->string('title');
            $table->text('details');
            $table->unsignedSmallInteger('participants_estimate')->nullable();
            $table->string('modality', 20)->nullable();
            $table->date('desired_start_date')->nullable();
            $table->boolean('requires_sence')->default(false);
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();

            // 7.2.2 Revisión del requisito
            $table->string('review_status', 20)->default('pending')->index();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->foreignId('budget_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_requirement_intakes');
    }
};
