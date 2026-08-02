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
        Schema::table('allocation_conflicts', function (Blueprint $table) {
            if (! Schema::hasColumn('allocation_conflicts', 'status')) {
                $table->string('status')->default('open')->after('reason_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('allocation_conflicts', function (Blueprint $table) {
            if (Schema::hasColumn('allocation_conflicts', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
