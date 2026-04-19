<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users');
            $table->enum('evaluator_type', ['supervisor', 'company'])
                  ->comment('supervisor = pembimbing sekolah, company = pembimbing perusahaan');

            // Komponen penilaian (skala 0-100)
            $table->decimal('discipline_score', 5, 2)->nullable()->comment('Kedisiplinan');
            $table->decimal('skill_score', 5, 2)->nullable()->comment('Kemampuan teknis');
            $table->decimal('attitude_score', 5, 2)->nullable()->comment('Sikap dan etika');
            $table->decimal('knowledge_score', 5, 2)->nullable()->comment('Pengetahuan');
            $table->decimal('communication_score', 5, 2)->nullable()->comment('Komunikasi');
            $table->decimal('final_score', 5, 2)->nullable()->comment('Nilai akhir (dihitung otomatis)');
            $table->string('grade', 5)->nullable()->comment('A, B, C, D, E');

            $table->text('strengths')->nullable()->comment('Kelebihan peserta');
            $table->text('improvements')->nullable()->comment('Hal yang perlu ditingkatkan');
            $table->text('notes')->nullable();
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();

            // Satu evaluator hanya memberi satu nilai per internship
            $table->unique(['internship_id', 'evaluator_id', 'evaluator_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};