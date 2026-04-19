<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->date('report_date');
            $table->text('activity')->comment('Kegiatan yang dilakukan');
            $table->text('problems')->nullable()->comment('Kendala yang dihadapi');
            $table->text('solutions')->nullable()->comment('Solusi yang dilakukan');
            $table->string('photo')->nullable()->comment('Foto dokumentasi kegiatan');
            $table->enum('status', [
                'draft',        // Belum dikirim
                'submitted',    // Sudah dikirim, menunggu verifikasi
                'approved',     // Disetujui pembimbing
                'revision',     // Perlu direvisi
            ])->default('draft');
            $table->text('feedback')->nullable()->comment('Catatan dari pembimbing');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            // Satu laporan per hari per internship
            $table->unique(['internship_id', 'report_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};