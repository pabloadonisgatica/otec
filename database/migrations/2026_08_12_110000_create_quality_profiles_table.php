<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_profiles', function (Blueprint $table) {

            $table->id();

            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->text('scope')->nullable();

            $table->date('last_audit_date')->nullable();
            $table->text('legal_documentation')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_profiles');
    }
};
