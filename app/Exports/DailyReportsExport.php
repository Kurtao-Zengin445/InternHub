<?php

namespace App\Exports;

use App\Models\DailyReport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DailyReportsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $internshipId;

    public function __construct($internshipId = null)
    {
        $this->internshipId = $internshipId;
    }

    public function collection()
    {
        $query = DailyReport::with(['internship.application.student.user']);
        if ($this->internshipId) {
            $query->where('internship_id', $this->internshipId);
        }
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Siswa',
            'Status Kehadiran', 
            'Kegiatan',
            'Catatan',
            'Status Approval',
            'Feedback Pembimbing',
            'Dikirim Pada'
        ];
    }

    public function map($report): array
    {
        return [
            optional($report->report_date)->format('d-m-Y'),
            $report->internship->application->student->user->name,
            ucfirst($report->status ?? 'N/A'),
            substr($report->activities, 0, 100) . '...',
            substr($report->notes, 0, 100) . '...',
            ucfirst($report->status),
            substr($report->feedback ?? '', 0, 100) . '...',
            $report->submitted_at?->format('d/m/Y H:i'),
        ];
    }
}

