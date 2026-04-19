<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\DailyReport;
use App\Models\Supervisor;

class DashboardController extends Controller
{
    public function index()
    {
        $supervisor = $this->currentSupervisor();

        // Daftar magang yang dibimbing
        $internships = Internship::with(['application.student.user', 'application.program.company'])
            ->where('supervisor_id', $supervisor->id)
            ->where('status', 'active')
            ->get();

        $stats = [
            'active_internships'  => $internships->count(),
            'pending_reports'     => DailyReport::whereIn('internship_id', $internships->pluck('id'))
                                        ->where('status', 'submitted')
                                        ->count(),
            'total_students'      => $internships->count(),
            'evaluated'           => $internships->filter(fn($i) => $i->supervisorEvaluation())->count(),
        ];

        // Laporan yang menunggu verifikasi
        $pendingReports = DailyReport::with(['internship.application.student.user'])
            ->whereIn('internship_id', $internships->pluck('id'))
            ->where('status', 'submitted')
            ->latest('report_date')
            ->take(8)
            ->get();

        return view('supervisor.dashboard', compact('supervisor', 'stats', 'internships', 'pendingReports'));
    }

    private function currentSupervisor(): Supervisor
    {
        $supervisor = auth()->user()?->supervisor;

        abort_if(!$supervisor, 403, 'Profil pembimbing belum tersedia. Silakan hubungi admin.');

        return $supervisor;
    }
}
