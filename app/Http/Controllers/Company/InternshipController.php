<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Internship;
use App\Models\Notification;
use Illuminate\Http\Request;

class InternshipController extends Controller
{
    public function index(Request $request)
    {
        $company = $this->currentCompany();

        $internships = Internship::with([
            'application.student.user',
            'application.studentModel',
            'application.studentModel.school',
            'application.program',
        ])
        ->whereHas('application.program', fn ($query) => $query->where('company_id', $company->id))
        ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
        ->latest()
        ->paginate(15)
        ->withQueryString();

        return view('company.internships.index', compact('internships'));
    }

    public function show(Internship $internship)
    {
        $this->authorizeInternship($internship);

        $internship->load([
            'application.student.user',
            'application.studentModel',
            'application.studentModel.school',
            'application.program',
            'dailyReports',
            'attendances',
            'documents',
            'evaluations',
        ]);

        $stats = [
            'total_reports' => $internship->dailyReports->count(),
            'approved_reports' => $internship->dailyReports->where('status', 'approved')->count(),
            'attendance_pct' => $internship->attendancePercentage(),
            'days_remaining' => now()->diffInDays($internship->end_date, false),
        ];

        return view('company.internships.show', compact('internship', 'stats'));
    }

    public function terminate(Request $request, Internship $internship)
    {
        $this->authorizeInternship($internship);

        abort_if($internship->status !== 'active', 422, 'Hanya magang aktif yang dapat dihentikan.');

        $request->validate([
            'notes' => ['required', 'string', 'min:20', 'max:1000'],
        ]);

        $internship->update([
            'status' => 'terminated',
            'notes' => $request->notes,
        ]);

        $student = $internship->application->student;
        Notification::send(
            $student->user_id,
            'Magang Dihentikan',
            'Program magang Anda telah dihentikan oleh perusahaan. Silakan hubungi pembimbing sekolah.',
            'warning',
            $internship,
            route('student.internship.show')
        );

        if ($internship->supervisor_id) {
            Notification::send(
                $internship->supervisor->user_id,
                'Magang Siswa Dihentikan',
                "Magang {$student->user->name} di {$internship->application->program->company->name} telah dihentikan.",
                'warning',
                $internship
            );
        }

        return redirect()
            ->route('company.internships.show', $internship)
            ->with('success', 'Magang berhasil dihentikan.');
    }

    public function complete(Internship $internship)
    {
        $this->authorizeInternship($internship);

        abort_if($internship->status !== 'active', 422, 'Hanya magang aktif yang dapat ditandai selesai.');

        $internship->update(['status' => 'completed']);

        $student = $internship->application->student;
        Notification::send(
            $student->user_id,
            'Magang Selesai',
            "Selamat! Program magang Anda di {$internship->application->program->company->name} telah selesai.",
            'info',
            $internship,
            route('student.internship.evaluation')
        );

        return redirect()
            ->route('company.internships.show', $internship)
            ->with('success', 'Magang berhasil ditandai selesai.');
    }

    private function authorizeInternship(Internship $internship): void
    {
        abort_if($internship->application->program->company_id !== $this->currentCompany()->id, 403);
    }

    private function currentCompany(): Company
    {
        $company = auth('web')->user()?->company;

        abort_if(!$company, 403, 'Profil perusahaan belum tersedia. Silakan lengkapi profil terlebih dahulu.');

        return $company;
    }
}
