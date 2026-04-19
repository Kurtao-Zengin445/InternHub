<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Internship;
use App\Models\Notification;
use App\Models\Supervisor;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $supervisor = $this->currentSupervisor();
        $internshipIds = Internship::where('supervisor_id', $supervisor->id)->pluck('id');

        $reports = DailyReport::with(['internship.application.student.user'])
            ->whereIn('internship_id', $internshipIds)
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('internship_id'), fn ($query) => $query->where('internship_id', $request->internship_id))
            ->latest('report_date')
            ->paginate(15)
            ->withQueryString();

        $supervisedInternships = Internship::with('application.student.user')
            ->where('supervisor_id', $supervisor->id)
            ->get();

        return view('supervisor.reports.index', compact('reports', 'supervisedInternships'));
    }

    public function show(DailyReport $dailyReport)
    {
        $this->authorizeReport($dailyReport);

        $dailyReport->load('internship.application.student.user');

        return view('supervisor.reports.show', ['report' => $dailyReport]);
    }

    public function approve(Request $request, DailyReport $dailyReport)
    {
        $this->authorizeReport($dailyReport);

        abort_if($dailyReport->status !== 'submitted', 422, 'Laporan tidak dalam status menunggu persetujuan.');

        $request->validate([
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        $dailyReport->update([
            'status' => 'approved',
            'feedback' => $request->feedback,
        ]);

        $studentUser = $dailyReport->internship->application->student;
        Notification::send(
            $studentUser->id,
            'Laporan Disetujui',
            "Laporan harian tanggal {$dailyReport->report_date->format('d/m/Y')} telah disetujui pembimbing.",
            'approval',
            $dailyReport,
            route('student.reports.show', $dailyReport)
        );

        return back()->with('success', 'Laporan berhasil disetujui.');
    }

    public function requestRevision(Request $request, DailyReport $dailyReport)
    {
        $this->authorizeReport($dailyReport);

        abort_if($dailyReport->status !== 'submitted', 422, 'Laporan tidak dalam status menunggu persetujuan.');

        $request->validate([
            'feedback' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $dailyReport->update([
            'status' => 'revision',
            'feedback' => $request->feedback,
        ]);

        $studentUser = $dailyReport->internship->application->student;
        Notification::send(
            $studentUser->id,
            'Laporan Perlu Direvisi',
            "Laporan harian tanggal {$dailyReport->report_date->format('d/m/Y')} perlu direvisi. Cek catatan pembimbing.",
            'warning',
            $dailyReport,
            route('student.reports.show', $dailyReport)
        );

        return back()->with('success', 'Laporan dikembalikan untuk direvisi.');
    }

    public function bulkApprove(Request $request)
    {
        $supervisor = $this->currentSupervisor();
        $internshipIds = Internship::where('supervisor_id', $supervisor->id)->pluck('id');

        $request->validate([
            'report_ids' => ['required', 'array'],
            'report_ids.*' => ['exists:daily_reports,id'],
        ]);

        $reports = DailyReport::whereIn('id', $request->report_ids)
            ->whereIn('internship_id', $internshipIds)
            ->where('status', 'submitted')
            ->get();

        foreach ($reports as $report) {
            $report->update(['status' => 'approved']);

            $studentUser = $report->internship->application->student;
            Notification::send(
                $studentUser->id,
                'Laporan Disetujui',
                "Laporan harian tanggal {$report->report_date->format('d/m/Y')} telah disetujui.",
                'approval',
                $report,
                route('student.reports.show', $report)
            );
        }

        return back()->with('success', "{$reports->count()} laporan berhasil disetujui.");
    }

    private function authorizeReport(DailyReport $report): void
    {
        $supervisor = $this->currentSupervisor();
        $internshipIds = Internship::where('supervisor_id', $supervisor->id)->pluck('id');

        abort_if(!$internshipIds->contains($report->internship_id), 403);
    }

    private function currentSupervisor(): Supervisor
    {
        $supervisor = auth()->user()?->supervisor;

        abort_if(!$supervisor, 403, 'Profil pembimbing belum tersedia. Silakan hubungi admin.');

        return $supervisor;
    }
}
