<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->date('attendance_date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->enum('status', [
                'present',      // Hadir
                'absent',       // Tidak hadir tanpa keterangan
                'sick',         // Sakit
                'permission',   // Izin
                'holiday',      // Hari libur
            ])->default('present');
            $table->string('check_in_photo')->nullable()->comment('Foto saat check in');
            $table->string('check_out_photo')->nullable()->comment('Foto saat check out');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Satu presensi per hari per internship
            $table->unique(['internship_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};