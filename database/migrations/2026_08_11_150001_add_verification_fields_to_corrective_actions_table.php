<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('corrective_actions', function (Blueprint $table) {

            // Fecha real en que se implementó (distinta de due_date,
            // que es la fecha en que debía implementarse).
            $table->date('implemented_at')->nullable()->after('due_date');

            $table->date('implementation_verified_at')->nullable()->after('implemented_at');

            $table->string('verifier_name')->nullable()->after('implementation_verified_at');

            $table->date('effectiveness_verified_at')->nullable()->after('verifier_name');

            // null = aún no evaluada.
            $table->boolean('effectiveness_satisfactory')->nullable()->after('effectiveness_verified_at');

            $table->text('effectiveness_notes')->nullable()->after('effectiveness_satisfactory');
        });
    }

    public function down(): void
    {
        Schema::table('corrective_actions', function (Blueprint $table) {
            $table->dropColumn([
                'implemented_at',
                'implementation_verified_at',
                'verifier_name',
                'effectiveness_verified_at',
                'effectiveness_satisfactory',
                'effectiveness_notes',
            ]);
        });
    }
};
