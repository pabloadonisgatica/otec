<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questionnaire_responses', function (Blueprint $table) {
            // Cuándo se envió por correo el resultado de un instrumento (null = no enviado).
            $table->timestamp('result_emailed_at')->nullable()->after('result');
        });
    }

    public function down(): void
    {
        Schema::table('questionnaire_responses', function (Blueprint $table) {
            $table->dropColumn('result_emailed_at');
        });
    }
};
