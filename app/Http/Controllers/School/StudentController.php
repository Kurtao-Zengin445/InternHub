<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
        $school = Auth::user()->schoolProfile;
        
        if (!$school) {
            return redirect()->route('profile.edit');
        }
        
        $students = $school->students()
            ->with('user')
            ->when(request('search'), fn($q, $v) => $q->whereHas('user', fn($q) => $q->where('name', 'like', "%{$v}%")))
            ->when(request('major'), fn($q, $v) => $q->where('major', $v))
            ->when(request('status'), fn($q, $v) => $q->whereHas('internships', fn($q) => 
                $v === 'active' ? $q->where('status', 'active') : $q->whereDoesntHave('internships', fn($q) => $q->where('status', 'active'))
            ))
            ->latest()
            ->paginate(15);

        $majors = $school->students()->distinct()->pluck('major')->filter()->sort();

        return view('school.students.index', compact('students', 'majors'));
    }

    public function show(Student $student)
    {
        $school = Auth::user()->schoolProfile;
        
        if (!$school || $student->school_id !== $school->id) {
            abort(403);
        }

        $student->load(['user', 'applications.program.company', 'internships']);

        $activeInternship = $student->internships->where('status', 'active')->first();

        return view('school.students.show', compact('student', 'activeInternship'));
    }

    public function update(Request $request, Student $student)
    {
        $school = Auth::user()->schoolProfile;
        
        if (!$school || $student->school_id !== $school->id) {
            abort(403);
        }

        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:20', Rule::unique('students')->ignore($student->id)],
            'class' => 'required|string|max:20',
            'major' => 'required|string|max:100',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'birth_place' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $student->update($validated);

        return redirect()->route('school.students.show', $student)
            ->with('success', 'Data siswa berhasil diperbarui');
    }

    public function destroy(Student $student)
    {
        $school = Auth::user()->schoolProfile;
        
        if (!$school || $student->school_id !== $school->id) {
            abort(403);
        }

        if ($student->internships()->where('status', 'active')->exists()) {
            return back()->with('error', 'Siswa tidak dapat dihapus karena masih aktif magang');
        }

        $student->user()->delete();
        $student->delete();

        return redirect()->route('school.students.index')
            ->with('success', 'Siswa berhasil dihapus');
    }
}