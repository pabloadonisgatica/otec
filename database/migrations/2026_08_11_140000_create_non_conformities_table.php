<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('non_conformities', function (Blueprint $table) {

            $table->id();

            // Ej: NC-2026-001. Se genera automáticamente al crear.
            $table->string('code')->unique();

            $table->string('title');
            $table->text('description');

            // Proceso/área de origen (texto libre por ahora,
            // no existe todavía un catálogo formal de procesos).
            $table->string('process')->nullable();

            // Vínculo opcional con la ejecución donde se originó,
            // cuando aplica (trazabilidad con la operación real).
            $table->foreignId('execution_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('responsible_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('detected_at');

            $table->enum('status', ['open', 'in_progress', 'closed'])
                ->default('open');

            $table->text('notes')->nullable();

            $table->timestamp('closed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('non_conformities');
    }
};
