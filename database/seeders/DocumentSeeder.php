<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Internship;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $internships = Internship::where('status', 'completed')
            ->with('application.studentModel', 'application.studentModel.user')
            ->get();

        $count = 0;

        foreach ($internships as $internship) {
            $student = $internship->application->studentModel;
            $studentUserId = $student?->user_id;
            
            if (!$studentUserId) {
                continue;
            }
            
            $studentName   = str_replace(' ', '_', strtolower($student->user->name ?? 'student'));

            $documents = [
                [
                    'document_type' => 'introduction_letter',
                    'title'         => 'Surat Pengantar Magang dari Sekolah',
                    'file_name'     => "surat_pengantar_{$studentName}.pdf",
                    'file_type'     => 'pdf',
                    'file_size'     => rand(150000, 400000),
                    'status'        => 'approved',
                    'uploaded_by'   => $studentUserId,
                ],
                [
                    'document_type' => 'acceptance_letter',
                    'title'         => 'Surat Penerimaan Magang dari Perusahaan',
                    'file_name'     => "surat_penerimaan_{$studentName}.pdf",
                    'file_type'     => 'pdf',
                    'file_size'     => rand(100000, 300000),
                    'status'        => 'approved',
                    'uploaded_by'   => $studentUserId,
                ],
                [
                    'document_type' => 'final_report',
                    'title'         => 'Laporan Akhir Magang',
                    'file_name'     => "laporan_akhir_{$studentName}.pdf",
                    'file_type'     => 'pdf',
                    'file_size'     => rand(500000, 2000000),
                    'status'        => 'approved',
                    'uploaded_by'   => $studentUserId,
                ],
                [
                    'document_type' => 'certificate',
                    'title'         => 'Sertifikat Magang',
                    'file_name'     => "sertifikat_{$studentName}.pdf",
                    'file_type'     => 'pdf',
                    'file_size'     => rand(200000, 600000),
                    'status'        => 'approved',
                    'uploaded_by'   => $internship->company_supervisor_id ?? $studentUserId,
                ],
            ];

            foreach ($documents as $doc) {
                Document::create(array_merge($doc, [
                    'internship_id' => $internship->id,
                    'file_path'     => "documents/{$internship->id}/{$doc['file_name']}",
                    'uploaded_at'   => $internship->end_date,
                ]));
                $count++;
            }
        }

        $this->command->info("  DocumentSeeder: {$count} dokumen dibuat.");
    }
}
