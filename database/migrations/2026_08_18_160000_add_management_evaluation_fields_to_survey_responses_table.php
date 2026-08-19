<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survey_responses', function (Blueprint $table) {

            // 'participante' (el flujo que ya existía) | 'gerencia' (nuevo)
            $table->string('evaluator_type')->default('participante')->after('execution_id');

            // Solo para evaluaciones de tipo 'gerencia': a quién se evalúa
            // y quién firma la evaluación.
            $table->foreignId('instructor_id')->nullable()->after('participant_id')
                ->constrained()->nullOnDelete();

            $table->string('evaluator_name')->nullable()->after('instructor_id'); // Firma
        });

        // participant_id era obligatorio — pasa a opcional porque las
        // evaluaciones de Gerencia no tienen un participante asociado.
        // SQL crudo porque el proyecto no tiene doctrine/dbal instalado.
        DB::statement('ALTER TABLE survey_responses MODIFY participant_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('instructor_id');
            $table->dropColumn(['evaluator_type', 'evaluator_name']);
        });

        DB::statement('ALTER TABLE survey_responses MODIFY participant_id BIGINT UNSIGNED NOT NULL');
    }
};
