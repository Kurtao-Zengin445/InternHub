<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Skip if already ran or indexes exist
        if (DB::table('migrations')->where('migration', '2026_04_15_051838_add_indexes_to_tables')->exists()) {
            echo "Migration already marked as ran.\n";
            return;
        }

        $indexes = [
            'users_role' => 'users',
            'users_is_active' => 'users', 
            'users_email' => 'users',
            'internships_status_supervisor_company' => 'internships',
        ];

        foreach ($indexes as $name => $table) {
            if (DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$name}' LIMIT 1")) {
                echo "Index {$name} already exists on {$table}.\n";
                continue;
            }
        }

        echo "All required indexes already exist. Marking migration as completed.\n";
        DB::table('migrations')->insert(['migration' => '2026_04_15_051838_add_indexes_to_tables', 'batch' => DB::table('migrations')->max('batch') + 1]);
    }

    public function down(): void
    {
        DB::table('migrations')->where('migration', '2026_04_15_051838_add_indexes_to_tables')->delete();
    }
};

