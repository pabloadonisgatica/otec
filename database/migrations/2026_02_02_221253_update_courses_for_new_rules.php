<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {

            // --- Agregar folio ---
            if (!Schema::hasColumn('courses', 'folio')) {
                $table->string('folio')->nullable()->after('id');
            }

            // --- Tipo de curso: sence | licitacion | privado ---
            if (!Schema::hasColumn('courses', 'course_type')) {
                $table->string('course_type')->default('privado')->after('sence_code');
            }

            // --- Modalidad múltiple (array JSON) ---
            // reemplaza instruction_modality (string) por instruction_modalities (json)
            if (Schema::hasColumn('courses', 'instruction_modality') && !Schema::hasColumn('courses', 'instruction_modalities')) {
                $table->json('instruction_modalities')->nullable()->after('activity_type');
            } elseif (!Schema::hasColumn('courses', 'instruction_modalities')) {
                $table->json('instruction_modalities')->nullable()->after('activity_type');
            }

            // --- Horas con decimales (ej: 1.50 horas = 1h 30m) ---
            // si hours existe como unsignedSmallInteger, lo cambiamos a decimal
            if (Schema::hasColumn('courses', 'hours')) {
                $table->decimal('hours', 6, 2)->nullable()->change();
            }

            // --- Fecha aprobación SENCE (para calcular caducidad +4 años) ---
            if (!Schema::hasColumn('courses', 'sence_approval_date')) {
                $table->date('sence_approval_date')->nullable()->after('sence_code');
            }

            // --- Sacar campos ya no usados ---
            if (Schema::hasColumn('courses', 'sence_request_date')) {
                $table->dropColumn('sence_request_date');
            }
            if (Schema::hasColumn('courses', 'sence_expiration_date')) {
                $table->dropColumn('sence_expiration_date');
            }
            if (Schema::hasColumn('courses', 'min_hours')) {
                $table->dropColumn('min_hours');
            }
        });

        // Si existía instruction_modality, lo borramos en una segunda pasada (para evitar problemas con change)
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'instruction_modality')) {
                $table->dropColumn('instruction_modality');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {

            if (Schema::hasColumn('courses', 'folio')) {
                $table->dropColumn('folio');
            }

            if (Schema::hasColumn('courses', 'course_type')) {
                $table->dropColumn('course_type');
            }

            if (Schema::hasColumn('courses', 'instruction_modalities')) {
                $table->dropColumn('instruction_modalities');
            }

            // volver a string simple
            if (!Schema::hasColumn('courses', 'instruction_modality')) {
                $table->string('instruction_modality')->nullable();
            }

            // volver hours a entero (si lo necesitas)
            if (Schema::hasColumn('courses', 'hours')) {
                $table->unsignedSmallInteger('hours')->nullable()->change();
            }

            if (Schema::hasColumn('courses', 'sence_approval_date')) {
                $table->dropColumn('sence_approval_date');
            }

            // re-agregar campos antiguos (si existían en tu flujo)
            if (!Schema::hasColumn('courses', 'sence_request_date')) {
                $table->date('sence_request_date')->nullable();
            }
            if (!Schema::hasColumn('courses', 'sence_expiration_date')) {
                $table->date('sence_expiration_date')->nullable();
            }
            if (!Schema::hasColumn('courses', 'min_hours')) {
                $table->unsignedSmallInteger('min_hours')->nullable();
            }
        });
    }
};
