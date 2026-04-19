<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\InternshipProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): RedirectResponse|View
    {
        $user = Auth::user();

        if (!$user->student) {
            return redirect()->route('register.complete');
        }

        $student = $user->student;
        $internship = $student->activeInternship();

        $stats = null;
        if ($internship) {
            $stats = [
                'total_reports' => $internship->dailyReports()->count(),
                'approved_reports' => $internship->dailyReports()->where('status', 'approved')->count(),
                'pending_reports' => $internship->dailyReports()->where('status', 'submitted')->count(),
                'attendance_pct' => $internship->attendancePercentage(),
                'days_remaining' => now()->diffInDays($internship->end_date, false),
            ];
        }

        $availablePrograms = null;
        if (!$internship) {
            $availablePrograms = InternshipProgram::with('company')
                ->where('status', 'open')
                ->whereDoesntHave('applications', fn ($query) => $query->where('student_id', $user->id))
                ->latest()
                ->take(5)
                ->get();
        }

        $recentReports = $internship
            ? $internship->dailyReports()->latest('report_date')->take(5)->get()
            : collect();

        return view('student.dashboard', compact(
            'student',
            'internship',
            'stats',
            'availablePrograms',
            'recentReports',
        ));
    }
}
