<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE allocations ALTER COLUMN score TYPE NUMERIC(5, 2) USING score::numeric');
        DB::statement('ALTER TABLE allocation_runs ALTER COLUMN score TYPE NUMERIC(5, 2) USING score::numeric');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE allocations ALTER COLUMN score TYPE INTEGER USING round(score)::integer');
        DB::statement('ALTER TABLE allocation_runs ALTER COLUMN score TYPE INTEGER USING round(score)::integer');
    }
};
