<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
Schema::create('budgets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    $table->string('internal_name');
    $table->date('budget_date')->nullable();
    $table->string('status')->default('draft');
    $table->string('course_code')->nullable();
    $table->unsignedBigInteger('total_amount')->default(0);
    $table->text('observations')->nullable();
    $table->text('includes')->nullable();
    $table->text('excludes')->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
    $table->index(['company_id', 'status']);
});

    }

    public function down(): void
    {
        Schema::dropIfExists('budget_courses');
    }
};

