<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->enum('document_type', [
                'introduction_letter',  // Surat pengantar dari sekolah
                'acceptance_letter',    // Surat penerimaan dari perusahaan
                'activity_plan',        // Rencana kegiatan magang
                'progress_report',      // Laporan kemajuan
                'final_report',         // Laporan akhir magang
                'certificate',          // Sertifikat magang dari perusahaan
                'other',                // Dokumen lainnya
            ]);
            $table->string('title');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type', 20)->nullable()->comment('pdf, docx, jpg, dll');
            $table->unsignedBigInteger('file_size')->nullable()->comment('Ukuran file dalam bytes');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->enum('status', [
                'pending',      // Menunggu verifikasi
                'approved',     // Disetujui
                'rejected',     // Ditolak
            ])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};