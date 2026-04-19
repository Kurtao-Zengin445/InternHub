<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Check-in geolocation & selfie
            $table->decimal('checkin_latitude', 10, 8)->nullable()->after('check_in_photo');
            $table->decimal('checkin_longitude', 11, 8)->nullable()->after('checkin_latitude');
            $table->string('checkin_address')->nullable()->after('checkin_longitude')
                  ->comment('Alamat hasil reverse geocoding');

            // Check-out geolocation & selfie
            $table->decimal('checkout_latitude', 10, 8)->nullable()->after('check_out_photo');
            $table->decimal('checkout_longitude', 11, 8)->nullable()->after('checkout_latitude');
            $table->string('checkout_address')->nullable()->after('checkout_longitude');

            // Validasi jarak dari lokasi perusahaan (meter)
            $table->integer('checkin_distance')->nullable()->after('checkout_address')
                  ->comment('Jarak dalam meter dari lokasi perusahaan saat check-in');
            $table->integer('checkout_distance')->nullable()
                  ->comment('Jarak dalam meter dari lokasi perusahaan saat check-out');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'checkin_latitude', 'checkin_longitude', 'checkin_address',
                'checkout_latitude', 'checkout_longitude', 'checkout_address',
                'checkin_distance', 'checkout_distance',
            ]);
        });
    }
};