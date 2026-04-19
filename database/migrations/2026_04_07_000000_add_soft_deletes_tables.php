<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('internships', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('supervisors', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('internships', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('supervisors', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};