<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Exports\DailyReportsExport;
use App\Models\DailyReport;
use App\Models\Internship;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $supervisor = auth()->user()->supervisor;

        $query = DailyReport::with(['internship.application.student.user'])
            ->whereHas('internship', fn($q) => $q->where('supervisor_id', $supervisor->id));

        $internshipId = $request->get('internship_id');
        if ($internshipId) {
            $query->where('internship_id', $internshipId);
        }

        $status = $request->get('status');
        if ($status) {
            $query->where('status', $status);
        }

        $reports = $query->latest()->paginate(15);

        $internships = Internship::where('supervisor_id', $supervisor->id)->get(['id', 'notes']);

        return view('supervisor.reports.index', compact('reports', 'internships'));
    }

    public function show(DailyReport $dailyReport)
    {
        abort_if($dailyReport->internship->supervisor_id !== auth()->user()->supervisor->id, 403);

        $dailyReport->load(['internship.application.student.user']);

        return view('supervisor.reports.show', compact('dailyReport'));
    }

    public function approve(DailyReport $dailyReport)
    {
        abort_if($dailyReport->internship->supervisor_id !== auth()->user()->supervisor->id, 403);

        $dailyReport->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        \App\Jobs\SendReportNotification::dispatch($dailyReport->id, 'approved');

        return back()->with('success', 'Laporan berhasil di-approve & notifikasi terkirim.');
    }

    public function reject(Request $request, DailyReport $dailyReport)
    {
        abort_if($dailyReport->internship->supervisor_id !== auth()->user()->supervisor->id, 403);

        $request->validate([
            'feedback' => 'required|string|min:10'
        ]);

        $dailyReport->update([
            'status' => 'rejected',
            'feedback' => $request->feedback,
            'approved_at' => null,
        ]);

        \App\Jobs\SendReportNotification::dispatch($dailyReport->id, 'rejected');

        return back()->with('success', 'Laporan ditolak & feedback terkirim ke siswa.');
    }

    public function export(Request $request)
    {
        $internshipId = $request->get('internship_id');

        $filename = 'laporan-magang-' . date('Y-m-d');
        if ($internshipId) {
            $filename .= '-internship-' . $internshipId;
        }

        return Excel::download(new DailyReportsExport($internshipId), $filename . '.xlsx');
    }
}

