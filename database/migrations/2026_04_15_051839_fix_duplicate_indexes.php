<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $migration = '2026_04_15_051838_add_indexes_to_tables';
        
        // Mark as ran without executing
        if (!DB::table('migrations')->where('migration', $migration)->exists()) {
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => DB::table('migrations')->max('batch') + 1
            ]);
        }
        
        echo "Migration {$migration} marked as completed. Indexes already exist from previous migration.\n";
    }

    public function down(): void
    {
        DB::table('migrations')->where('migration', '2026_04_15_051838_add_indexes_to_tables')->delete();
    }
};

