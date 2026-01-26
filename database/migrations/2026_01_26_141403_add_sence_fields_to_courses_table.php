<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {

            // Identificación / modalidad / tipo
            $table->string('activity_type')->nullable()->after('name'); // curso|seminario
            $table->string('instruction_modality')->nullable()->after('activity_type'); 
            // presencial|elearning_sync|elearning_async|distance_self

            // Reglas / números
            $table->decimal('attendance_percentage', 5, 2)->nullable()->after('instruction_modality'); // ej 50.00
            $table->decimal('min_grade', 5, 2)->nullable()->after('attendance_percentage'); // ej 4.00 / 5.50
            $table->unsignedSmallInteger('min_hours')->nullable()->after('min_grade'); // horas mínimas
            $table->unsignedInteger('participants_count')->nullable()->after('min_hours'); // N° participantes (real)

            // Textos largos
            $table->text('technical_foundation')->nullable()->after('notes');
            $table->text('target_population')->nullable()->after('technical_foundation');
            $table->text('general_objectives')->nullable()->after('target_population');
            $table->text('teaching_methodology')->nullable()->after('general_objectives');

            // Recursos / evaluación / infraestructura
            $table->text('evaluation')->nullable()->after('teaching_methodology');
            $table->text('infrastructure')->nullable()->after('evaluation');

            // Costos / vigencias
            $table->unsignedInteger('value_per_participant')->nullable()->after('infrastructure');
            $table->date('sence_request_date')->nullable()->after('value_per_participant');
            $table->date('sence_expiration_date')->nullable()->after('sence_request_date');

            // Diploma (módulo futuro)
            $table->unsignedBigInteger('diploma_id')->nullable()->after('sence_expiration_date');
            // Sin FK por ahora. La agregamos cuando exista tabla diplomas.

            // Índices útiles
            $table->index(['activity_type']);
            $table->index(['instruction_modality']);
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['activity_type']);
            $table->dropIndex(['instruction_modality']);

            $table->dropColumn([
                'activity_type',
                'instruction_modality',
                'attendance_percentage',
                'min_grade',
                'min_hours',
                'participants_count',
                'technical_foundation',
                'target_population',
                'general_objectives',
                'teaching_methodology',
                'evaluation',
                'infrastructure',
                'value_per_participant',
                'sence_request_date',
                'sence_expiration_date',
                'diploma_id',
            ]);
        });
    }
};
