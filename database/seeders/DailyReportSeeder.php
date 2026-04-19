<?php

namespace Database\Seeders;

use App\Models\DailyReport;
use App\Models\Internship;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DailyReportSeeder extends Seeder
{
    public function run(): void
    {
        $internships = Internship::where('status', 'completed')->get();

        $activities = [
            'Mempelajari struktur proyek dan standar koding perusahaan bersama mentor.',
            'Membuat fitur CRUD untuk modul manajemen data pengguna.',
            'Melakukan debugging pada bug yang ditemukan di modul laporan keuangan.',
            'Mengikuti daily standup meeting dan mendiskusikan progress pekerjaan dengan tim.',
            'Membuat dokumentasi teknis untuk API yang telah dikembangkan.',
            'Melakukan code review bersama senior developer dan menerima masukan perbaikan.',
            'Mengerjakan unit testing untuk memastikan fitur berjalan sesuai spesifikasi.',
            'Membantu tim dalam proses deployment aplikasi ke server staging.',
            'Mempelajari penggunaan version control Git dan alur kerja branching di perusahaan.',
            'Mengikuti sesi training internal tentang keamanan aplikasi web (OWASP Top 10).',
            'Membuat desain mockup antarmuka pengguna menggunakan Figma.',
            'Mengoptimalkan query database yang lambat pada laporan bulanan.',
            'Berpartisipasi dalam sprint planning untuk menentukan fitur yang akan dikerjakan.',
            'Mengintegrasikan API pihak ketiga untuk fitur pembayaran online.',
            'Mempresentasikan hasil kerja minggu ini kepada supervisor.',
        ];

        $problems = [
            null,
            'Mengalami kesulitan dalam memahami logika bisnis pada modul tertentu.',
            'Error pada koneksi database saat menjalankan migrasi di lingkungan lokal.',
            'Kesulitan memahami dokumentasi API pihak ketiga yang kurang lengkap.',
            null,
            'Merge conflict saat menggabungkan branch fitur dengan branch utama.',
            null,
            'Server staging mengalami downtime selama 2 jam di pagi hari.',
        ];

        $solutions = [
            null,
            'Berkonsultasi dengan mentor dan mempelajari dokumentasi sistem yang tersedia.',
            'Mengubah konfigurasi koneksi database dan berhasil menyelesaikan masalah.',
            'Menghubungi tim support penyedia API dan mendapat klarifikasi yang dibutuhkan.',
            null,
            'Menyelesaikan conflict secara manual dengan panduan dari senior developer.',
            null,
            'Menunggu tim infrastruktur menyelesaikan perbaikan server.',
        ];

        $totalReports = 0;

        foreach ($internships as $internship) {
            $start = Carbon::parse($internship->start_date);
            $end   = Carbon::parse($internship->end_date);

            $current = $start->copy();
            $reportIndex = 0;

            while ($current->lte($end)) {
                // Lewati hari Sabtu dan Minggu
                if (!$current->isWeekend()) {
                    $actIdx  = $reportIndex % count($activities);
                    $probIdx = $reportIndex % count($problems);
                    $solIdx  = $reportIndex % count($solutions);

                    DailyReport::create([
                        'internship_id' => $internship->id,
                        'report_date'   => $current->format('Y-m-d'),
                        'activity'      => $activities[$actIdx],
                        'problems'      => $problems[$probIdx],
                        'solutions'     => ($problems[$probIdx] ? $solutions[$solIdx] : null),
                        'status'        => 'approved',
                        'feedback'      => $reportIndex % 5 === 0
                                          ? 'Laporan sudah baik. Pertahankan semangat belajarnya!'
                                          : null,
                        'submitted_at'  => $current->copy()->setTime(17, rand(0, 59)),
                    ]);

                    $totalReports++;
                    $reportIndex++;
                }

                $current->addDay();
            }
        }

        $this->command->info("  DailyReportSeeder: {$totalReports} laporan harian dibuat.");
    }
}
