<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->enum('type', [
                'info',         // Informasi umum
                'reminder',     // Pengingat (laporan harian, presensi)
                'approval',     // Persetujuan (lamaran, dokumen)
                'evaluation',   // Penilaian
                'warning',      // Peringatan
            ])->default('info');
            $table->boolean('is_read')->default(false);
            $table->string('action_url')->nullable()->comment('URL tindakan yang relevan');

            // Polymorphic: notifiable bisa berupa internship, application, document, dll
            $table->string('notifiable_type')->nullable();
            $table->unsignedBigInteger('notifiable_id')->nullable();
            $table->index(['notifiable_type', 'notifiable_id']);

            $table->timestamp('sent_at')->useCurrent();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};