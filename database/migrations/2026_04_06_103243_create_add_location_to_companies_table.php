<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('website')
                  ->comment('Koordinat latitude lokasi perusahaan');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude')
                  ->comment('Koordinat longitude lokasi perusahaan');
            $table->integer('allowed_radius')->default(200)->after('longitude')
                  ->comment('Radius presensi yang diizinkan (meter)');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'allowed_radius']);
        });
    }
};