<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            SchoolSeeder::class,
            CompanySeeder::class,
            SupervisorSeeder::class,
            StudentSeeder::class,
            InternshipProgramSeeder::class,
            ApplicationSeeder::class,
            InternshipSeeder::class,
            DailyReportSeeder::class,
            AttendanceSeeder::class,
            EvaluationSeeder::class,
            DocumentSeeder::class,
            StudentFeaturesSeeder::class,
        ]);

        $this->command->info('✓ Semua seeder berhasil dijalankan.');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin',      'admin@intern.test',      'Password1'],
                ['Sekolah',    'smkn1@intern.test',      'Password1'],
                ['Perusahaan', 'techcorp@intern.test',   'Password1'],
                ['Pembimbing', 'pembimbing@intern.test', 'Password1'],
                ['Siswa',      'siswa@intern.test',      'Password1'],
            ]
        );
    }
}
