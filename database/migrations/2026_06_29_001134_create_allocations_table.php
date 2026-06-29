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
        Schema::create('allocations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('allocation_run_id')
                ->constrained('allocation_runs')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('school_class_id')
                ->constrained('school_classes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('period_id')
                ->constrained('periods')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('status')->default('generated');
            $table->integer('score')->default(0);

            $table->timestamps();

            $table->unique(['allocation_run_id', 'teacher_id', 'period_id']);
            $table->unique(['allocation_run_id', 'school_class_id', 'period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocations');
    }
};
