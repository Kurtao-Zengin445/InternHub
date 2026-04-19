<?php

namespace Database\Seeders;

use App\Models\Evaluation;
use App\Models\Internship;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Database\Seeder;

class EvaluationSeeder extends Seeder
{
    public function run(): void
    {
        $internships = Internship::where('status', 'completed')
            ->with('application.program.company', 'supervisor')
            ->get();

        // Skor per siswa dibuat bervariasi agar realistis
        $scoresets = [
            ['discipline' => 88, 'skill' => 85, 'attitude' => 90, 'knowledge' => 82, 'communication' => 87],
            ['discipline' => 92, 'skill' => 90, 'attitude' => 95, 'knowledge' => 88, 'communication' => 91],
            ['discipline' => 78, 'skill' => 80, 'attitude' => 82, 'knowledge' => 75, 'communication' => 79],
            ['discipline' => 85, 'skill' => 88, 'attitude' => 86, 'knowledge' => 84, 'communication' => 83],
            ['discipline' => 90, 'skill' => 92, 'attitude' => 88, 'knowledge' => 91, 'communication' => 89],
            ['discipline' => 76, 'skill' => 78, 'attitude' => 80, 'knowledge' => 72, 'communication' => 77],
        ];

        $strengths = [
            'Siswa menunjukkan kemampuan teknis yang baik dan cepat belajar hal baru.',
            'Sangat disiplin dan selalu hadir tepat waktu. Komunikasi dengan tim berjalan lancar.',
            'Kreativitas dan inisiatif dalam menyelesaikan masalah sangat menonjol.',
            'Kemampuan analisis dan pemecahan masalah di atas rata-rata.',
        ];

        $improvements = [
            'Perlu meningkatkan kepercayaan diri dalam menyampaikan pendapat di depan tim.',
            'Manajemen waktu perlu diperbaiki agar penyelesaian tugas lebih tepat waktu.',
            'Perlu memperdalam pengetahuan teknis sesuai bidang keahlian.',
            'Kemampuan dokumentasi perlu ditingkatkan agar lebih terstruktur.',
        ];

        $count = 0;

        foreach ($internships as $index => $internship) {
            $scores   = $scoresets[$index % count($scoresets)];
            $companyScores = $scoresets[($index + 1) % count($scoresets)];

            // Evaluasi dari pembimbing sekolah
            if ($internship->supervisor) {
                $eval = new Evaluation([
                    'internship_id'       => $internship->id,
                    'evaluator_id'        => $internship->supervisor->user_id,
                    'evaluator_type'      => 'supervisor',
                    'discipline_score'    => $scores['discipline'],
                    'skill_score'         => $scores['skill'],
                    'attitude_score'      => $scores['attitude'],
                    'knowledge_score'     => $scores['knowledge'],
                    'communication_score' => $scores['communication'],
                    'strengths'           => $strengths[$index % count($strengths)],
                    'improvements'        => $improvements[$index % count($improvements)],
                    'notes'               => 'Secara keseluruhan siswa menunjukkan perkembangan yang baik selama masa magang.',
                    'evaluated_at'        => $internship->end_date,
                ]);
                $eval->saveWithCalculation();
                $count++;
            }

            // Evaluasi dari pembimbing perusahaan
            if ($internship->company_supervisor_id) {
                $eval = new Evaluation([
                    'internship_id'       => $internship->id,
                    'evaluator_id'        => $internship->company_supervisor_id,
                    'evaluator_type'      => 'company',
                    'discipline_score'    => $companyScores['discipline'],
                    'skill_score'         => $companyScores['skill'],
                    'attitude_score'      => $companyScores['attitude'],
                    'knowledge_score'     => $companyScores['knowledge'],
                    'communication_score' => $companyScores['communication'],
                    'strengths'           => $strengths[($index + 1) % count($strengths)],
                    'improvements'        => $improvements[($index + 1) % count($improvements)],
                    'notes'               => 'Peserta magang memberikan kontribusi positif bagi tim selama program berlangsung.',
                    'evaluated_at'        => $internship->end_date,
                ]);
                $eval->saveWithCalculation();
                $count++;
            }
        }

        $this->command->info("  EvaluationSeeder: {$count} penilaian dibuat.");
    }
}
