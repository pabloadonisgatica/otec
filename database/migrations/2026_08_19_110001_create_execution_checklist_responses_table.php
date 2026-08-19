<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('execution_checklist_responses', function (Blueprint $table) {

            $table->id();

            $table->foreignId('execution_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('checklist_item_id')
                ->constrained('execution_checklist_items')
                ->cascadeOnDelete();

            // 'realizado' | 'no_realizado' | 'no_aplica' | null (sin responder)
            $table->string('status')->nullable();

            $table->timestamps();

            $table->unique(
                ['execution_id', 'checklist_item_id'],
                'execution_checklist_responses_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('execution_checklist_responses');
    }
};
