<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('position')->nullable()->after('permissions'); // Cargo
            $table->date('birth_date')->nullable()->after('position');
            $table->string('gender')->nullable()->after('birth_date'); // Mujer/Hombre/Otro/Prefiero no indicarlo
            $table->string('photo_path')->nullable()->after('gender');

            // Mismo patrón de array de documentos que ya usa Instructor.
            $table->json('documents')->nullable()->after('photo_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['position', 'birth_date', 'gender', 'photo_path', 'documents']);
        });
    }
};
