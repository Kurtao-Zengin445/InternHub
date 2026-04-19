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
        // Duplicate indexes already exist from 2026_04_07_000001_add_database_indexes
        // Migration is intentionally empty (no-op)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op - safe rollback
    }
};

