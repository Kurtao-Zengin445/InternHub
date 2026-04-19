<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Helpers\StudentInternshipHelper;
use App\Models\DailyReport;
use App\Models\Internship;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DailyReportController extends Controller
{
    use StudentInternshipHelper;

    public function index()
    {
        $internship = $this->getActiveInternship();

        if (!$internship) {
            return view('student.reports.index', [
                'internship' => null,
                'reports' => null,
                'hasActiveInternship' => false,
            ]);
        }

        $query = DailyReport::where('internship_id', $internship->id);

        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->whereDate('report_date', 'like', "%$search%")
                  ->orWhere('activity', 'like', "%$search%");
            });
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $reports = $query->latest('report_date')
            ->paginate(15)
            ->appends(request()->query());

        return view('student.reports.index', compact('internship', 'reports'))
            ->with(['hasActiveInternship' => true, 'internship' => $internship]);
    }

    public function export()
    {
        $internship = $this->getActiveInternship();
        if (!$internship) {
            return redirect()->route('student.reports.index')
                ->with('error', 'Anda belum memiliki magang aktif.');
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\StudentDailyReportsExport($internship->id),
            'Laporan-Harian-' . $internship->id . '-' . now()->format('d-m-Y') . '.xlsx'
        );
    }

    public function create()
    {
        $internship = $this->getActiveInternship();

        if (!$internship) {
            return redirect()->route('student.reports.index')
                ->with('error', 'Anda belum memiliki magang aktif.');
        }

        $todayReport = DailyReport::where('internship_id', $internship->id)
            ->whereDate('report_date', today())
            ->first();

        if ($todayReport) {
            return redirect()
                ->route('student.reports.show', $todayReport)
                ->with('info', 'Anda sudah membuat laporan untuk hari ini.');
        }

        if (today()->lt($internship->start_date) || today()->gt($internship->end_date)) {
            return back()->with('error', 'Laporan hanya dapat dibuat dalam rentang tanggal magang.');
        }

        return view('student.reports.create', compact('internship'));
    }

    public function store(Request $request)
    {
        $internship = $this->requireActiveInternshipForWrite();

        if (!$internship) {
            return redirect()->route('student.reports.index')
                ->with('error', 'Anda belum memiliki magang aktif.');
        }

        $validated = $request->validate([
            'report_date' => [
                'required',
                'date',
                'before_or_equal:today',
                "after_or_equal:{$internship->start_date->format('Y-m-d')}",
            ],
            'activity'  => ['required', 'string', 'min:20'],
            'problems'  => ['nullable', 'string'],
            'solutions' => ['nullable', 'string'],
            'photo'     => ['nullable', 'image', 'max:3072'],
        ]);

        $exists = DailyReport::where('internship_id', $internship->id)
            ->whereDate('report_date', $validated['report_date'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Laporan untuk tanggal tersebut sudah ada.');
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')
                ->store("reports/{$internship->id}", 'public');
        }

        $isSend = $request->boolean('send');

        $report = DailyReport::create([
            'internship_id' => $internship->id,
            'report_date'   => $validated['report_date'],
            'activity'      => $validated['activity'],
            'problems'      => $validated['problems'] ?? null,
            'solutions'     => $validated['solutions'] ?? null,
            'photo'         => $photoPath,
            'status'        => $isSend ? 'submitted' : 'draft',
            'submitted_at'  => $isSend ? now() : null,
        ]);

        if ($isSend && $internship->supervisor_id) {
            $studentName = $internship->application->student->name ?? 'Siswa';
            
            Notification::send(
                $internship->supervisor->user_id,
                'Laporan Harian Baru',
                "{$studentName} mengirimkan laporan harian tanggal {$report->report_date->format('d/m/Y')}.",
                'reminder',
                $report,
                route('supervisor.reports.show', $report)
            );
        }

        $message = $isSend ? 'Laporan berhasil dikirim ke pembimbing.' : 'Laporan disimpan sebagai draft.';

        return redirect()
            ->route('student.reports.show', $report)
            ->with('success', $message);
    }

    public function show(DailyReport $dailyReport)
    {
        $internship = $this->getActiveInternship();

        if (!$internship || $dailyReport->internship_id !== $internship->id) {
            abort(403);
        }

        return view('student.reports.show', ['report' => $dailyReport]);
    }

    public function edit(DailyReport $dailyReport)
    {
        $internship = $this->getActiveInternship();

        if (!$internship || $dailyReport->internship_id !== $internship->id) {
            abort(403);
        }

        if (!in_array($dailyReport->status, ['draft', 'revision'])) {
            return back()->with('error', 'Laporan yang sudah dikirim tidak dapat diedit.');
        }

        return view('student.reports.edit', ['report' => $dailyReport]);
    }

    public function update(Request $request, DailyReport $dailyReport)
    {
        $internship = $this->getActiveInternship();

        if (!$internship || $dailyReport->internship_id !== $internship->id) {
            abort(403);
        }

        if (!in_array($dailyReport->status, ['draft', 'revision'])) {
            return back()->with('error', 'Laporan yang sudah dikirim tidak dapat diubah.');
        }

        $internship = $dailyReport->internship;

        $validated = $request->validate([
            'activity'  => ['required', 'string', 'min:20'],
            'problems'  => ['nullable', 'string'],
            'solutions' => ['nullable', 'string'],
            'photo'     => ['nullable', 'image', 'max:3072'],
            'keep_photo'=> ['nullable', 'in:0,1'],
        ]);

        if ($request->input('keep_photo', '1') === '0' && $dailyReport->photo && !$request->hasFile('photo')) {
            Storage::disk('public')->delete($dailyReport->photo);
            $validated['photo'] = null;
        }

        if ($request->hasFile('photo')) {
            if ($dailyReport->photo) {
                Storage::disk('public')->delete($dailyReport->photo);
            }

            $validated['photo'] = $request->file('photo')
                ->store("reports/{$internship->id}", 'public');
        } elseif (!array_key_exists('photo', $validated)) {
            unset($validated['photo']);
        }

        $isSend = $request->boolean('send');

        $dailyReport->update([
            'activity'     => $validated['activity'],
            'problems'     => $validated['problems'] ?? null,
            'solutions'    => $validated['solutions'] ?? null,
            'photo'        => $validated['photo'] ?? $dailyReport->photo,
            'status'       => $isSend ? 'submitted' : 'draft',
            'submitted_at' => $isSend ? now() : $dailyReport->submitted_at,
            'feedback'     => $isSend ? null : $dailyReport->feedback,
        ]);

        if ($isSend && $internship->supervisor_id) {
            $studentName = $internship->application->student->name ?? 'Siswa';
            
            Notification::send(
                $internship->supervisor->user_id,
                'Laporan Harian Diperbarui',
                "{$studentName} memperbarui laporan tanggal {$dailyReport->report_date->format('d/m/Y')}.",
                'reminder',
                $dailyReport,
                route('supervisor.reports.show', $dailyReport)
            );
        }

        $message = $isSend ? 'Laporan berhasil dikirim ulang.' : 'Draft laporan berhasil diperbarui.';

        return redirect()
            ->route('student.reports.show', $dailyReport)
            ->with('success', $message);
    }

    public function destroy(DailyReport $dailyReport)
    {
        $internship = $this->getActiveInternship();

        if (!$internship || $dailyReport->internship_id !== $internship->id) {
            abort(403);
        }

        if ($dailyReport->status !== 'draft') {
            return back()->with('error', 'Hanya laporan berstatus draft yang dapat dihapus.');
        }

        if ($dailyReport->photo) {
            Storage::disk('public')->delete($dailyReport->photo);
        }

        $dailyReport->delete();

        return redirect()
            ->route('student.reports.index')
            ->with('success', 'Draft laporan berhasil dihapus.');
    }
}
