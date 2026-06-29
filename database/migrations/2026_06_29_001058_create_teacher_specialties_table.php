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
        Schema::create('teacher_specialties', function (Blueprint $table) {
            $table->id();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('specialty_id')
                ->constrained('specialties')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedTinyInteger('adherence_score')->default(100);

            $table->timestamps();

            $table->unique(['teacher_id', 'specialty_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_specialties');
    }
};
