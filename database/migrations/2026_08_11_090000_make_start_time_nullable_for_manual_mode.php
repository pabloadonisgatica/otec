<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // En modo manual, la planificación no define una
        // hora de inicio fija; se completa por sesión desde
        // la Agenda. Por eso start_time deja de ser obligatorio
        // tanto en la planificación como en cada sesión.
        //
        // Se usa SQL crudo (en vez de ->change()) porque
        // el proyecto no tiene instalado doctrine/dbal.
        DB::statement(
            'ALTER TABLE execution_plannings MODIFY start_time TIME NULL'
        );

        DB::statement(
            'ALTER TABLE execution_sessions MODIFY start_time TIME NULL'
        );

        DB::statement(
            'ALTER TABLE execution_sessions MODIFY end_time TIME NULL'
        );
    }

    public function down(): void
    {
        DB::statement(
            'ALTER TABLE execution_plannings MODIFY start_time TIME NOT NULL'
        );

        DB::statement(
            'ALTER TABLE execution_sessions MODIFY start_time TIME NOT NULL'
        );

        DB::statement(
            'ALTER TABLE execution_sessions MODIFY end_time TIME NOT NULL'
        );
    }
};
