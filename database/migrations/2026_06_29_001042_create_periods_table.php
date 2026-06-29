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
        Schema::create('periods', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique(); // P1, P2, P3...
            $table->unsignedTinyInteger('weekday'); // 1 = segunda, 5 = sexta
            $table->string('shift'); // morning, afternoon, night
            $table->string('description');
            $table->unsignedTinyInteger('sort_order')->unique();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periods');
    }
};
