<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('non_conformities', function (Blueprint $table) {

            $table->enum('source', [
                'internal_audit',
                'external_audit',
                'complaint',
                'survey',
                'other',
            ])->nullable()->after('code');

            $table->string('origin')->nullable()->after('process');

            $table->enum('nc_type', [
                'major',
                'minor',
                'observation',
            ])->nullable()->after('origin');

            $table->text('objective_evidence')->nullable()->after('description');

            $table->string('normative_reference')->nullable()->after('objective_evidence');

            // Corrección = arreglo inmediato/contención.
            // Distinto de la Acción Correctiva (que ataca la causa raíz).
            $table->text('correction')->nullable()->after('normative_reference');

            $table->text('root_cause')->nullable()->after('correction');
        });
    }

    public function down(): void
    {
        Schema::table('non_conformities', function (Blueprint $table) {
            $table->dropColumn([
                'source',
                'origin',
                'nc_type',
                'objective_evidence',
                'normative_reference',
                'correction',
                'root_cause',
            ]);
        });
    }
};
