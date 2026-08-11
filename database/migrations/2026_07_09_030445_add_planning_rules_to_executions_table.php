<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('executions', function (Blueprint $table) {

            $table->time('planning_start_time')
                ->nullable()
                ->after('hours_per_day');

            $table->boolean('exclude_saturdays')
                ->default(false)
                ->after('planning_start_time');

            $table->boolean('exclude_sundays')
                ->default(true)
                ->after('exclude_saturdays');

            $table->boolean('exclude_holidays')
                ->default(true)
                ->after('exclude_sundays');
        });
    }

    public function down(): void
    {
        Schema::table('executions', function (Blueprint $table) {

            $table->dropColumn([
                'planning_start_time',
                'exclude_saturdays',
                'exclude_sundays',
                'exclude_holidays',
            ]);
        });
    }
};