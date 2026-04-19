<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Internship;
use App\Models\InternshipProgram;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->company) {
            return redirect()->route('register.complete');
        }

        $company = $user->company;

        $stats = [
            'total_programs'       => InternshipProgram::where('company_id', $company->id)->count(),
            'open_programs'        => InternshipProgram::where('company_id', $company->id)->where('status', 'open')->count(),
            'pending_applications' => Application::whereHas('program', fn($q) => $q->where('company_id', $company->id))
                                          ->where('status', 'pending')->count(),
            'active_internships'   => Internship::whereHas('application.program', fn($q) => $q->where('company_id', $company->id))
                                          ->where('status', 'active')->count(),
        ];

        // Lamaran terbaru yang belum diproses
        $pendingApplications = Application::with(['student.user', 'student.school', 'program'])
            ->whereHas('program', fn($q) => $q->where('company_id', $company->id))
            ->where('status', 'pending')
            ->latest('applied_at')
            ->take(8)
            ->get();

        // Siswa magang yang sedang aktif
        $activeInternships = Internship::with(['application.student.user', 'application.program'])
            ->whereHas('application.program', fn($q) => $q->where('company_id', $company->id))
            ->where('status', 'active')
            ->get();

        return view('company.dashboard', compact(
            'company',
            'stats',
            'pendingApplications',
            'activeInternships',
        ));
    }
}
