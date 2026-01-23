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
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'region')) {
                $table->string('region')->nullable()->after('address');
            }
            if (!Schema::hasColumn('companies', 'commune')) {
                $table->string('commune')->nullable()->after('region');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'region')) {
                $table->dropColumn('region');
            }
            if (Schema::hasColumn('companies', 'commune')) {
                $table->dropColumn('commune');
            }
        });
    }

};
