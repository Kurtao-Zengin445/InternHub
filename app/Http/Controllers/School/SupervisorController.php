<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SupervisorController extends Controller
{
    public function index()
    {
        $school = Auth::user()->schoolProfile;
        
        if (!$school) {
            return redirect()->route('profile.edit');
        }
        
        $supervisors = $school->supervisors()
            ->with('user')
            ->withCount('internships')
            ->latest()
            ->paginate(15);

        return view('school.supervisors.index', compact('supervisors'));
    }

    public function create()
    {
        return view('school.supervisors.create');
    }

    public function store(Request $request)
    {
        $school = Auth::user()->schoolProfile;

        if (!$school) {
            return redirect()->route('profile.edit');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'nip' => 'required|string|max:50|unique:supervisors,nip',
            'position' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        Supervisor::create([
            'user_id' => $user->id,
            'school_id' => $school->id,
            'nip' => $validated['nip'],
            'position' => $validated['position'],
            'phone' => $validated['phone'],
        ]);

        return redirect()->route('school.supervisors.index')
            ->with('success', 'Pembimbing berhasil ditambahkan');
    }

    public function edit(Supervisor $supervisor)
    {
        $school = Auth::user()->schoolProfile;
        
        if (!$school || $supervisor->school_id !== $school->id) {
            abort(403);
        }

        $supervisor->load('user');

        return view('school.supervisors.edit', compact('supervisor'));
    }

    public function show(Supervisor $supervisor)
    {
        $school = Auth::user()->schoolProfile;
        
        if (!$school || $supervisor->school_id !== $school->id) {
            abort(403);
        }

        $supervisor->load(['user', 'internships.application.studentModel', 'internships.companySupervisor']);

        return view('school.supervisors.show', compact('supervisor'));
    }

    public function update(Request $request, Supervisor $supervisor)
    {
        $school = Auth::user()->schoolProfile;
        
        if (!$school || $supervisor->school_id !== $school->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $supervisor->user_id,
            'password' => 'nullable|string|min:8|confirmed',
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('supervisors')->ignore($supervisor->id)],
            'position' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        $supervisor->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $supervisor->user->update(['password' => bcrypt($validated['password'])]);
        }

        $supervisor->update(array_filter([
            'nip' => $validated['nip'],
            'position' => $validated['position'],
            'phone' => $validated['phone'],
        ]));

        return redirect()->route('school.supervisors.show', $supervisor)
            ->with('success', 'Data pembimbing berhasil diperbarui');
    }

    public function destroy(Supervisor $supervisor)
    {
        $school = Auth::user()->schoolProfile;
        
        if (!$school || $supervisor->school_id !== $school->id) {
            abort(403);
        }

        if ($supervisor->internships()->where('status', 'active')->exists()) {
            return back()->with('error', 'Pembimbing tidak dapat dihapus karena masih membimbing siswa aktif');
        }

        $supervisor->user()->delete();
        $supervisor->delete();

        return redirect()->route('school.supervisors.index')
            ->with('success', 'Pembimbing berhasil dihapus');
    }
}