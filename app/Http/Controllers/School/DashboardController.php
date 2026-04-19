<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Application;
use App\Models\Internship;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $school = $user->schoolProfile;

        if (!$school || !$school->exists()) {
            return redirect()->route('profile.edit');
        }

        $stats = [
            'total_students' => $school->students()->count(),
            'active_internships' => Internship::whereHas('application', function ($query) use ($school) {
                $query->where('school_id', $school->id);
            })->where('status', 'active')->count(),
            'pending_applications' => Application::where('school_id', $school->id)
                ->where('status', 'pending')->count(),
            'total_supervisors' => $school->supervisors()->count(),
        ];

        $recentApplications = Application::where('school_id', $school->id)
            ->with(['studentModel', 'program.company'])
            ->latest()
            ->take(5)
            ->get();

        $activeInternships = Internship::whereHas('application', function ($query) use ($school) {
                $query->where('school_id', $school->id);
            })
            ->with(['studentModel', 'supervisor.user', 'companySupervisor'])
            ->where('status', 'active')
            ->latest()
            ->take(5)
            ->get();

        return view('school.dashboard', compact('stats', 'recentApplications', 'activeInternships'));
    }
}