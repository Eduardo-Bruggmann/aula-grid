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
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('school_unit_id')
                ->constrained('school_units')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedTinyInteger('required_periods')->default(7);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['name', 'school_unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
