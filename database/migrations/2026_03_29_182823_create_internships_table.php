<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained()->nullOnDelete()
                  ->comment('Pembimbing dari sekolah');
            $table->unsignedBigInteger('company_supervisor_id')->nullable()
                  ->comment('ID user pembimbing dari perusahaan');
            $table->foreign('company_supervisor_id')->references('id')->on('users')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', [
                'active',       // Sedang berjalan
                'completed',    // Selesai
                'terminated',   // Dihentikan lebih awal
            ])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internships');
    }
};