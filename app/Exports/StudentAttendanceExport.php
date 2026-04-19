<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StudentAttendanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $internshipId;

    public function __construct($internshipId)
    {
        $this->internshipId = $internshipId;
    }

    public function collection()
    {
        return Attendance::where('internship_id', $this->internshipId)
            ->orderBy('attendance_date', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Hari',
            'Check In',
            'Check Out',
            'Durasi',
            'Status',
            'Catatan',
            'Lokasi Check In',
            'Jarak Check In',
            'Lokasi Check Out',
            'Jarak Check Out'
        ];
    }

    public function map($attendance): array
    {
        return [
            $this->collection()->search($attendance) + 1,
            $attendance->attendance_date?->format('d-m-Y'),
            $attendance->attendance_date?->translatedFormat('l'),
            $attendance->check_in?->format('H:i:s'),
            $attendance->check_out?->format('H:i:s'),
            $attendance->duration() ?? '',
            ucfirst($attendance->status),
            $attendance->notes ?? '',
            $attendance->checkin_address ?? '',
            $attendance->checkin_distance ? round($attendance->checkin_distance, 0) . ' m' : '',
            $attendance->checkout_address ?? '',
            $attendance->checkout_distance ? round($attendance->checkout_distance, 0) . ' m' : ''
        ];
    }
}

