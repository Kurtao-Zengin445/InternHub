<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index('is_active');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->index('school_id');
            $table->index('user_id');
        });

        Schema::table('supervisors', function (Blueprint $table) {
            $table->index('school_id');
            $table->index('user_id');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->index('student_id');
            $table->index('internship_program_id');
            $table->index('school_id');
            $table->index('status');
            $table->index('applied_at');
        });

        Schema::table('internships', function (Blueprint $table) {
            $table->index('application_id');
            $table->index('supervisor_id');
            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });

        Schema::table('daily_reports', function (Blueprint $table) {
            $table->index('internship_id');
            $table->index('report_date');
            $table->index('status');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->index('internship_id');
            $table->index('attendance_date');
            $table->index('status');
        });

        Schema::table('evaluations', function (Blueprint $table) {
            $table->index('internship_id');
            $table->index('evaluator_id');
            $table->index('evaluator_type');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->index('internship_id');
            $table->index('document_type');
            $table->index('status');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('read_at');
        });

        Schema::table('internship_programs', function (Blueprint $table) {
            $table->index('company_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('supervisors', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['internship_program_id']);
            $table->dropIndex(['school_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['applied_at']);
        });

        Schema::table('internships', function (Blueprint $table) {
            $table->dropIndex(['application_id']);
            $table->dropIndex(['supervisor_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['start_date', 'end_date']);
        });

        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropIndex(['internship_id']);
            $table->dropIndex(['report_date']);
            $table->dropIndex(['status']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['internship_id']);
            $table->dropIndex(['attendance_date']);
            $table->dropIndex(['status']);
        });

        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropIndex(['internship_id']);
            $table->dropIndex(['evaluator_id']);
            $table->dropIndex(['evaluator_type']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['internship_id']);
            $table->dropIndex(['document_type']);
            $table->dropIndex(['status']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['read_at']);
        });

        Schema::table('internship_programs', function (Blueprint $table) {
            $table->dropIndex(['company_id']);
            $table->dropIndex(['status']);
        });
    }
};