<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('executions', function (Blueprint $table) {

            $table->integer('planned_hours')
                ->nullable()
                ->after('start_date');

            $table->decimal('hours_per_day', 4, 2)
                ->nullable()
                ->after('planned_hours');

            $table->string('shift')
                ->nullable()
                ->after('hours_per_day');

            $table->date('end_date')
                ->nullable()
                ->after('shift');

        });
    }

    public function down(): void
    {
        Schema::table('executions', function (Blueprint $table) {

            $table->dropColumn([
                'planned_hours',
                'hours_per_day',
                'shift',
                'end_date',
            ]);

        });
    }
};
