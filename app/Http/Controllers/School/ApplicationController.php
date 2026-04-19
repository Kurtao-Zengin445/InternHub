<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $school = Auth::user()->schoolProfile;
        
        if (!$school) {
            return redirect()->route('profile.edit');
        }
        
        $query = Application::where('school_id', $school->id)
            ->with(['studentModel.user', 'program.company']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->whereHas('studentModel.user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $applications = $query->latest()->paginate(15);

        return view('school.applications.index', compact('applications'));
    }

    public function show(Application $application)
    {
        $school = Auth::user()->schoolProfile;
        
        if (!$school || $application->school_id !== $school->id) {
            abort(403);
        }

        $application->load(['studentModel.user', 'studentModel.school', 'program.company', 'internship']);

        return view('school.applications.show', compact('application'));
    }

    public function update(Request $request, Application $application)
    {
        $school = Auth::user()->schoolProfile;
        
        if (!$school || $application->school_id !== $school->id) {
            abort(403);
        }

        if (!in_array($application->status, ['pending'])) {
            return back()->with('error', 'Lamaran tidak dapat diubah');
        }

        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
        ]);

        $application->update([
            'status' => $validated['status'],
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('school.applications.show', $application)
            ->with('success', 'Lamaran berhasil diperbarui');
    }
}