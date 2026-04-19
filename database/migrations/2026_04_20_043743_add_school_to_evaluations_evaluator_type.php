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
        Schema::table('evaluations', function (Blueprint $table) {
            $table->enum('evaluator_type', ['supervisor', 'company', 'school'])
                  ->comment('supervisor = pembimbing sekolah, company = pembimbing perusahaan, school = sekolah')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->enum('evaluator_type', ['supervisor', 'company'])
                  ->comment('supervisor = pembimbing sekolah, company = pembimbing perusahaan')
                  ->change();
        });
    }
};
