<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\InternshipProgram;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'student') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $studentProfile = Auth::user()->student;

        $applications = Application::with(['program.company'])
            ->where('student_id', Auth::id())
            ->latest('applied_at')
            ->paginate(10);

        return view('student.applications.index', compact('applications', 'studentProfile'));
    }

    public function create(Request $request)
    {
        if (Auth::user()->role !== 'student') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $student = Auth::user()->student;

        if (!$student) {
            return redirect()
                ->route('student.dashboard')
                ->with('error', 'Silakan lengkapi profil Anda terlebih dahulu sebelum melamar magang.');
        }

        if ($student->activeInternship()) {
            return redirect()
                ->route('student.dashboard')
                ->with('error', 'Anda sedang dalam masa magang. Tidak dapat mengajukan lamaran baru.');
        }

        $program = null;
        if ($request->filled('program_id')) {
            $program = InternshipProgram::with('company')
                ->where('status', 'open')
                ->findOrFail($request->program_id);
        }

        $programs = InternshipProgram::with('company')
            ->where('status', 'open')
            ->whereDoesntHave('applications', fn ($query) => $query->where('student_id', Auth::id()))
            ->get();

        return view('student.applications.create', compact('program', 'programs'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'student') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $student = Auth::user()->student;

        if (!$student) {
            return redirect()
                ->route('student.dashboard')
                ->with('error', 'Silakan lengkapi profil Anda terlebih dahulu sebelum melamar magang.');
        }

        if ($student->activeInternship()) {
            return back()->with('error', 'Anda sedang dalam masa magang aktif.');
        }

        $validated = $request->validate([
            'internship_program_id' => [
                'required',
                'exists:internship_programs,id',
                function ($attribute, $value, $fail) {
                    $program = InternshipProgram::find($value);

                    if (!$program || !$program->isOpen()) {
                        $fail('Program magang ini sudah tidak menerima pendaftaran.');
                    }

                    if ($program && $program->remainingQuota() <= 0) {
                        $fail('Kuota program magang ini sudah penuh.');
                    }
                },
            ],
            'motivation_letter' => ['required', 'string', 'min:100'],
            'cv_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $alreadyApplied = Application::where('student_id', Auth::id())
            ->where('internship_program_id', $validated['internship_program_id'])
            ->exists();

        if ($alreadyApplied) {
            return back()->with('error', 'Anda sudah pernah melamar program magang ini.');
        }

        $cvPath = null;
        if ($request->hasFile('cv_file')) {
            $cvPath = $request->file('cv_file')->store("cv/{$student->user_id}", 'public');
        }

        try {
            $application = Application::create([
                'student_id' => Auth::id(),
                'internship_program_id' => $validated['internship_program_id'],
                'school_id' => $student->school_id,
                'motivation_letter' => $validated['motivation_letter'],
                'cv_file' => $cvPath,
                'status' => Application::STATUS_PENDING,
                'applied_at' => now(),
            ]);
        } catch (\Exception $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Terjadi kesalahan sistem saat mengirim lamaran: '.$exception->getMessage(),
                ]);
        }

        $program = $application->program;
        Notification::send(
            $program->company->user_id,
            'Lamaran Baru Masuk',
            "{$student->user->name} telah melamar program {$program->title}.",
            'info',
            $application,
            route('company.applications.show', $application)
        );

        return redirect()
            ->route('student.applications.show', $application)
            ->with('success', 'Lamaran berhasil dikirim. Tunggu konfirmasi dari perusahaan.');
    }

    public function show(Application $application)
    {
        if (Auth::user()->role !== 'student') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $this->authorizeStudent($application);

        $application->load(['program.company', 'internship']);

        return view('student.applications.show', compact('application'));
    }

    public function destroy(Application $application)
    {
        if (Auth::user()->role !== 'student') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $this->authorizeStudent($application);

        if (!$application->isPending()) {
            return back()->with('error', 'Lamaran yang sudah diproses tidak dapat dibatalkan.');
        }

        if ($application->cv_file) {
            Storage::disk('public')->delete($application->cv_file);
        }

        $application->update(['status' => Application::STATUS_CANCELLED]);

        return redirect()
            ->route('student.applications.index')
            ->with('success', 'Lamaran berhasil dibatalkan.');
    }

    private function authorizeStudent(Application $application): void
    {
        abort_if($application->student_id !== Auth::id(), 403);
    }
}
