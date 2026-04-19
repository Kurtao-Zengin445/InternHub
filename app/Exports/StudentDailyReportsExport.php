<?php

namespace App\Exports;

use App\Models\DailyReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StudentDailyReportsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $internshipId;

    public function __construct($internshipId)
    {
        $this->internshipId = $internshipId;
    }

    public function collection()
    {
        return DailyReport::where('internship_id', $this->internshipId)
            ->with('internship.application.student.user')
            ->orderBy('report_date', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Laporan',
            'Hari',
            'Kegiatan',
            'Masalah',
            'Solusi',
            'Foto',
            'Status',
            'Feedback',
            'Dikirim Pada'
        ];
    }

    public function map($report): array
    {
        return [
            $this->collection()->search($report) + 1,
            $report->report_date?->format('d-m-Y'),
            $report->report_date?->translatedFormat('l'),
            $report->activity,
            $report->problems ?? '',
            $report->solutions ?? '',
            $report->photo ? 'Ada' : 'Tidak',
            $report->getStatusLabelAttribute(),
            $report->feedback ?? '',
            $report->submitted_at?->format('d/m/Y H:i') ?? ''
        ];
    }
}

