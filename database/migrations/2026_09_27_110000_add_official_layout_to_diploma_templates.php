<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Diseño oficial de diploma (F38).
 *
 * - Agrega diploma_templates.layout: null = plantilla HTML antigua,
 *   'official' = vista fija resources/views/diplomas/pdf/official.blade.php.
 * - Crea el registro "Diploma oficial", que usan todos los diplomas nuevos.
 * - Las plantillas HTML existentes quedan inactivas (siguen sirviendo
 *   para volver a descargar los diplomas ya emitidos con ellas).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diploma_templates', function (Blueprint $table) {
            $table->string('layout', 30)->nullable()->after('name');
        });

        DB::table('diploma_templates')->update(['is_active' => false]);

        DB::table('diploma_templates')->insert([
            'name' => 'Diploma oficial',
            'layout' => 'official',
            'content_html' => '',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        $official = DB::table('diploma_templates')->where('layout', 'official')->first();

        // Solo se borra si no hay diplomas emitidos con él
        // (la FK de diplomas borraría esos diplomas en cascada).
        if ($official && ! DB::table('diplomas')->where('template_id', $official->id)->exists()) {
            DB::table('diploma_templates')->where('id', $official->id)->delete();
        }

        DB::table('diploma_templates')->whereNull('layout')->update(['is_active' => true]);

        Schema::table('diploma_templates', function (Blueprint $table) {
            $table->dropColumn('layout');
        });
    }
};
