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
        Schema::create('teacher_availabilities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('period_id')
                ->constrained('periods')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('is_available')->default(false);

            $table->timestamps();

            $table->unique(['teacher_id', 'period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_availabilities');
    }
};
