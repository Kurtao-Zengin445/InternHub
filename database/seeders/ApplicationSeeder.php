<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\InternshipProgram;
use App\Models\Student;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil program dan siswa berdasarkan data seeder sebelumnya
        $webDev    = InternshipProgram::where('title', 'Magang Web Developer')->first();
        $itSupport = InternshipProgram::where('title', 'Magang IT Support & Networking')->first();
        $finance   = InternshipProgram::where('title', 'Magang Administrasi Keuangan')->first();
        $design    = InternshipProgram::where('title', 'Magang Desain Grafis & Konten Kreatif')->first();

        $budi   = Student::where('nis', '2122001')->first();
        $sari   = Student::where('nis', '2122002')->first();
        $riko   = Student::where('nis', '2122003')->first();
        $nisa   = Student::where('nis', '2122004')->first();
        $dimas  = Student::where('nis', '2122101')->first();
        $fitri  = Student::where('nis', '2122102')->first();
        $galih  = Student::where('nis', '2122201')->first();
        $hana   = Student::where('nis', '2122202')->first();

        $motivasi = 'Saya sangat tertarik untuk mengikuti program magang ini karena sesuai dengan bidang keahlian yang saya pelajari di sekolah. Saya yakin pengalaman ini akan memberikan wawasan praktis yang sangat berharga untuk karier saya ke depan. Saya berkomitmen untuk belajar dengan sungguh-sungguh dan memberikan kontribusi terbaik selama masa magang.';

        $applications = [
            // Web Developer — 3 diterima (sesuai kuota)
            ['student' => $budi,  'program' => $webDev,    'status' => 'accepted', 'applied_at' => '2026-04-10'],
            ['student' => $sari,  'program' => $webDev,    'status' => 'accepted', 'applied_at' => '2026-04-12'],
            ['student' => $riko,  'program' => $webDev,    'status' => 'accepted', 'applied_at' => '2026-04-14'],

            // IT Support — 1 diterima, 1 ditolak
            ['student' => $nisa,  'program' => $itSupport, 'status' => 'accepted', 'applied_at' => '2026-05-11'],
            ['student' => $riko,  'program' => $itSupport, 'status' => 'rejected', 'applied_at' => '2026-05-13',
             'rejection_reason' => 'Kuota program sudah terpenuhi oleh kandidat dengan kualifikasi lebih sesuai.'],

            // Finance — 2 diterima
            ['student' => $dimas, 'program' => $finance,   'status' => 'accepted', 'applied_at' => '2026-05-10'],
            ['student' => $fitri, 'program' => $finance,   'status' => 'accepted', 'applied_at' => '2026-05-11'],

            // Design (open) — 2 pending untuk testing alur seleksi
            ['student' => $galih, 'program' => $design,    'status' => 'pending',  'applied_at' => now()->subDays(3)->format('Y-m-d')],
            ['student' => $hana,  'program' => $design,    'status' => 'pending',  'applied_at' => now()->subDays(1)->format('Y-m-d')],
        ];

        foreach ($applications as $data) {
            Application::create([
                'student_id'            => $data['student']->user_id,
                'internship_program_id' => $data['program']->id,
                'school_id'            => $data['student']->school_id,
                'motivation_letter'    => $motivasi,
                'status'               => $data['status'],
                'rejection_reason'     => $data['rejection_reason'] ?? null,
                'applied_at'           => $data['applied_at'],
                'reviewed_at'          => in_array($data['status'], ['accepted', 'rejected'])
                                           ? date('Y-m-d', strtotime($data['applied_at'] . ' +3 days'))
                                           : null,
            ]);
        }

        $this->command->info('  ApplicationSeeder: ' . count($applications) . ' lamaran dibuat.');
    }
}
