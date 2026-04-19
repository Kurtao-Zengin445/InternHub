<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Internship;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $internships = Internship::where('status', 'completed')->get();
        $totalAttendance = 0;

        foreach ($internships as $internship) {
            $start = Carbon::parse($internship->start_date);
            $end   = Carbon::parse($internship->end_date);

            $current    = $start->copy();
            $dayCounter = 0;

            while ($current->lte($end)) {
                if (!$current->isWeekend()) {
                    // Simulasi kehadiran realistis:
                    // ~88% hadir, ~5% sakit, ~5% izin, ~2% alpha
                    $rand = rand(1, 100);

                    if ($rand <= 88) {
                        // Hadir: jam masuk 07:30–08:15, jam pulang 15:30–16:30
                        $checkIn  = $current->copy()->setTime(7, rand(30, 75) % 60, 0);
                        if (rand(30, 75) > 60) $checkIn->setTime(8, rand(0, 15));
                        $checkOut = $current->copy()->setTime(15, rand(30, 90) % 60, 0);
                        if (rand(30, 90) > 60) $checkOut->setTime(16, rand(0, 30));

                        Attendance::create([
                            'internship_id'   => $internship->id,
                            'attendance_date' => $current->format('Y-m-d'),
                            'check_in'        => $checkIn->format('H:i:s'),
                            'check_out'       => $checkOut->format('H:i:s'),
                            'status'          => 'present',
                        ]);
                    } elseif ($rand <= 93) {
                        Attendance::create([
                            'internship_id'   => $internship->id,
                            'attendance_date' => $current->format('Y-m-d'),
                            'status'          => 'sick',
                            'notes'           => 'Sakit demam, disertai surat keterangan dokter.',
                        ]);
                    } elseif ($rand <= 98) {
                        Attendance::create([
                            'internship_id'   => $internship->id,
                            'attendance_date' => $current->format('Y-m-d'),
                            'status'          => 'permission',
                            'notes'           => 'Keperluan keluarga yang tidak dapat ditunda.',
                        ]);
                    } else {
                        Attendance::create([
                            'internship_id'   => $internship->id,
                            'attendance_date' => $current->format('Y-m-d'),
                            'status'          => 'absent',
                        ]);
                    }

                    $totalAttendance++;
                    $dayCounter++;
                }

                $current->addDay();
            }
        }

        $this->command->info("  AttendanceSeeder: {$totalAttendance} data presensi dibuat.");
    }
}
