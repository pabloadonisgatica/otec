<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('execution_plannings', function (Blueprint $table) {

            // morning | afternoon
            // Se agrega después de "mode" porque acompaña
            // la definición de la hora de inicio (start_time).
            $table->enum('shift', [
                'morning',
                'afternoon',
            ])
                ->nullable()
                ->after('mode');
        });
    }

    public function down(): void
    {
        Schema::table('execution_plannings', function (Blueprint $table) {
            $table->dropColumn('shift');
        });
    }
};
