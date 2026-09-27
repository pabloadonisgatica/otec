<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Biblioteca de logos adicionales para diplomas (ej: PACCAR, SENCE).
 * El logo principal siempre es el de la OTEC (app_settings.app_logo).
 *
 * Soft delete: un logo quitado de la lista no borra su archivo, porque
 * los diplomas ya emitidos lo siguen usando al volver a descargarse.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diploma_logos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diploma_logos');
    }
};
