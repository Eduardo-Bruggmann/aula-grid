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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();

            $table->string('registration')->unique();
            $table->string('name');
            $table->string('email')->nullable()->unique();

            $table->foreignId('school_unit_id')
                ->constrained('school_units')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedTinyInteger('max_weekly_periods')->default(7);
            $table->unsignedTinyInteger('max_daily_periods')->default(2);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
