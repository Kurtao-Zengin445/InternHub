<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\InternshipProgram;
use Illuminate\Database\Seeder;

class InternshipProgramSeeder extends Seeder
{
    public function run(): void
    {
        $techcorp   = Company::whereHas('user', fn($q) => $q->where('email', 'techcorp@intern.test'))->first();
        $finance    = Company::whereHas('user', fn($q) => $q->where('email', 'majubersama@intern.test'))->first();
        $media      = Company::whereHas('user', fn($q) => $q->where('email', 'nusantaramedia@intern.test'))->first();

        $programs = [
            // TechCorp — 2 program
            [
                'company_id'         => $techcorp->id,
                'title'              => 'Magang Web Developer',
                'description'        => 'Program magang pengembangan web fullstack menggunakan teknologi modern seperti Laravel, Vue.js, dan MySQL. Peserta akan terlibat langsung dalam proyek nyata perusahaan.',
                'requirements'       => "- Siswa aktif jurusan RPL atau TKJ\n- Memahami dasar HTML, CSS, dan JavaScript\n- Memiliki laptop pribadi\n- Bersedia mengikuti program selama 3 bulan",
                'quota'              => 3,
                'field'              => 'Rekayasa Perangkat Lunak',
                'start_date'         => '2026-04-01',
                'end_date'           => '2026-09-30',
                'registration_start' => '2026-03-01',
                'registration_end'   => '2026-03-15',
                'status'             => 'completed',
            ],
            [
                'company_id'         => $techcorp->id,
                'title'              => 'Magang IT Support & Networking',
                'description'        => 'Program magang di divisi IT Support untuk membantu pengelolaan infrastruktur jaringan, troubleshooting hardware dan software, serta administrasi sistem.',
                'requirements'       => "- Siswa aktif jurusan TKJ\n- Memahami dasar jaringan komputer (TCP/IP, LAN)\n- Memiliki sertifikat Cisco atau MikroTik (nilai tambah)\n- Disiplin dan bertanggung jawab",
                'quota'              => 2,
                'field'              => 'Teknik Komputer & Jaringan',
                'start_date'         => '2026-04-01',
                'end_date'           => '2026-09-30',
                'registration_start' => '2026-03-01',
                'registration_end'   => '2026-03-15',
                'status'             => 'completed',
            ],

            // Finance — 1 program
            [
                'company_id'         => $finance->id,
                'title'              => 'Magang Administrasi Keuangan',
                'description'        => 'Program magang di divisi keuangan untuk membantu pembukuan, administrasi dokumen keuangan, dan rekonsiliasi laporan bulanan.',
                'requirements'       => "- Siswa aktif jurusan Akuntansi atau Keuangan\n- Memahami jurnal akuntansi dasar\n- Menguasai Microsoft Excel\n- Teliti dan rapi dalam bekerja",
                'quota'              => 2,
                'field'              => 'Akuntansi & Keuangan',
                'start_date'         => '2025-07-01',
                'end_date'           => '2025-09-30',
                'registration_start' => '2025-05-01',
                'registration_end'   => '2025-06-15',
                'status'             => 'completed',
            ],

            // Media — 1 program (open, untuk testing alur lamaran)
            [
                'company_id'         => $media->id,
                'title'              => 'Magang Desain Grafis & Konten Kreatif',
                'description'        => 'Program magang di divisi kreatif untuk membantu produksi konten visual, desain materi promosi digital, dan pengelolaan media sosial perusahaan.',
                'requirements'       => "- Siswa aktif jurusan Multimedia atau DKV\n- Menguasai Adobe Photoshop / Illustrator / Canva\n- Memiliki portofolio desain (nilai tambah)\n- Kreatif dan mampu bekerja dalam tim",
                'quota'              => 4,
                'field'              => 'Multimedia & Desain',
                'start_date'         => '2026-04-01',
                'end_date'           => '2026-12-30',
                'registration_start' => '2026-03-01',
                'registration_end'   => '2026-03-15',
                'status'             => 'open',
            ],
        ];

        foreach ($programs as $data) {
            InternshipProgram::create($data);
        }

        $this->command->info('  InternshipProgramSeeder: ' . count($programs) . ' program dibuat (3 completed, 1 open).');
    }
}
