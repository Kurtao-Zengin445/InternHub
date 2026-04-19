<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\Supervisor;

class InternshipController extends Controller
{
    /**
     * Daftar semua siswa yang dibimbing (aktif & selesai).
     */
    public function index()
    {
        $supervisor = $this->currentSupervisor();

        $internships = Internship::with([
            'application.student.user',
            'application.program.company',
        ])
        ->where('supervisor_id', $supervisor->id)
        ->latest()
        ->paginate(15);

        return view('supervisor.internships.index', compact('internships'));
    }

    /**
     * Detail satu siswa bimbingan beserta progress-nya.
     */
    public function show(Internship $internship)
    {
        $this->authorizeSupervisor($internship);

        $internship->load([
            'application.student.user',
            'application.program.company',
            'dailyReports',
            'attendance',
            'evaluations',
            'documents',
        ]);

        $stats = [
            'total_reports'    => $internship->dailyReports->count(),
            'approved_reports' => $internship->dailyReports->where('status', 'approved')->count(),
            'pending_reports'  => $internship->dailyReports->where('status', 'submitted')->count(),
            'attendance_pct'   => $internship->attendancePercentage(),
        ];

        return view('supervisor.internships.show', compact('internship', 'stats'));
    }

    // ─── Private helper ────────────────────────────────────

    private function authorizeSupervisor(Internship $internship): void
    {
        $supervisorId = $this->currentSupervisor()->id;
        abort_if($internship->supervisor_id !== $supervisorId, 403);
    }

    private function currentSupervisor(): Supervisor
    {
        $supervisor = auth()->user()?->supervisor;

        abort_if(!$supervisor, 403, 'Profil pembimbing belum tersedia. Silakan hubungi admin.');

        return $supervisor;
    }
}
