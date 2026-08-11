<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE executions
            CHANGE planned_hours course_hours INT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE executions
            CHANGE course_hours planned_hours INT NULL
        ");
    }
};