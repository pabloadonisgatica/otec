<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // No usamos ->dropColumn() porque el proyecto no tiene doctrine/dbal.
        // Usamos SQL crudo directo.
        DB::statement('ALTER TABLE survey_template_sections DROP COLUMN repeats_per_instructor');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE survey_template_sections ADD COLUMN repeats_per_instructor TINYINT(1) NOT NULL DEFAULT 0 AFTER description');
    }
};
