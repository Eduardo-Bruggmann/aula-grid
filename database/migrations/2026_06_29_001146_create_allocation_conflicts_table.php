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
        Schema::create('allocation_conflicts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('allocation_run_id')
                ->constrained('allocation_runs')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('school_class_id')
                ->constrained('school_classes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('period_id')
                ->nullable()
                ->constrained('periods')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('reason_code');
            $table->text('reason_description');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocation_conflicts');
    }
};
