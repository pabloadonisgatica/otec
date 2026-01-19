<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('companies', function (Blueprint $table) {
        $table->id();

        $table->string('rut')->unique();              // RUT empresa
        $table->string('name');                       // Razón social / nombre
        $table->string('business_name')->nullable();  // Giro (opcional)
        $table->string('email')->nullable();
        $table->string('phone')->nullable();
        $table->string('address')->nullable();

        // Contacto principal
        $table->string('contact_name')->nullable();
        $table->string('contact_email')->nullable();
        $table->string('contact_phone')->nullable();

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
