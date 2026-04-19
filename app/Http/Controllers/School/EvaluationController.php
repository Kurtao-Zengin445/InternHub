<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    /**
     * Display a listing of evaluations.
     */
    public function index()
    {
        $school = Auth::user()->schoolProfile;

        $evaluations = Evaluation::whereHas('internship.student.school', function ($query) use ($school) {
            $query->where('schools.id', $school->id);
        })
        ->where('evaluator_type', Evaluation::TYPE_SCHOOL)
        ->with(['internship.student', 'internship.program.company'])
        ->latest('evaluated_at')
        ->paginate(15);

        return view('school.evaluations.index', compact('evaluations'));
    }

    /**
     * Show the form for creating a new evaluation.
     */
    public function create(Internship $internship)
    {
        $school = Auth::user()->schoolProfile;

        // Pastikan internship milik siswa dari sekolah ini
        if ($internship->student->school_id !== $school->id) {
            abort(403, 'Anda tidak memiliki akses ke internship ini.');
        }

        // Pastikan internship aktif
        if ($internship->status !== 'active') {
            return redirect()->route('school.internships.show', $internship)
                ->with('error', 'Penilaian hanya dapat dilakukan untuk internship yang sedang aktif.');
        }

        // Cek apakah sudah ada penilaian dari sekolah
        $existingEvaluation = Evaluation::where('internship_id', $internship->id)
            ->where('evaluator_type', Evaluation::TYPE_SCHOOL)
            ->first();

        if ($existingEvaluation) {
            return redirect()->route('school.evaluations.show', $existingEvaluation)
                ->with('info', 'Penilaian untuk internship ini sudah ada.');
        }

        return view('school.evaluations.create', compact('internship'));
    }

    /**
     * Store a newly created evaluation.
     */
    public function store(Request $request, Internship $internship)
    {
        $school = Auth::user()->schoolProfile;

        // Validasi akses
        if ($internship->student->school_id !== $school->id) {
            abort(403, 'Anda tidak memiliki akses ke internship ini.');
        }

        $validated = $request->validate([
            'discipline_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'skill_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'attitude_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'knowledge_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'communication_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'strengths' => ['nullable', 'string', 'max:1000'],
            'improvements' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Cek duplikasi
        $existingEvaluation = Evaluation::where('internship_id', $internship->id)
            ->where('evaluator_type', Evaluation::TYPE_SCHOOL)
            ->first();

        if ($existingEvaluation) {
            return redirect()->route('school.evaluations.show', $existingEvaluation)
                ->with('error', 'Penilaian untuk internship ini sudah ada.');
        }

        $evaluation = new Evaluation([
            'internship_id' => $internship->id,
            'evaluator_id' => Auth::id(),
            'evaluator_type' => Evaluation::TYPE_SCHOOL,
            'evaluated_at' => now(),
            ...$validated
        ]);

        $evaluation->saveWithCalculation();

        return redirect()->route('school.evaluations.show', $evaluation)
            ->with('success', 'Penilaian siswa berhasil disimpan.');
    }

    /**
     * Display the specified evaluation.
     */
    public function show(Evaluation $evaluation)
    {
        $school = Auth::user()->schoolProfile;

        // Pastikan evaluation milik siswa dari sekolah ini
        if ($evaluation->internship->student->school_id !== $school->id) {
            abort(403, 'Anda tidak memiliki akses ke penilaian ini.');
        }

        // Pastikan evaluation dari sekolah
        if (!$evaluation->isSchoolEvaluation()) {
            abort(403, 'Penilaian ini bukan dari sekolah.');
        }

        return view('school.evaluations.show', compact('evaluation'));
    }

    /**
     * Show the form for editing the evaluation.
     */
    public function edit(Evaluation $evaluation)
    {
        $school = Auth::user()->schoolProfile;

        // Validasi akses
        if ($evaluation->internship->student->school_id !== $school->id) {
            abort(403, 'Anda tidak memiliki akses ke penilaian ini.');
        }

        if (!$evaluation->isSchoolEvaluation()) {
            abort(403, 'Penilaian ini bukan dari sekolah.');
        }

        return view('school.evaluations.edit', compact('evaluation'));
    }

    /**
     * Update the specified evaluation.
     */
    public function update(Request $request, Evaluation $evaluation)
    {
        $school = Auth::user()->schoolProfile;

        // Validasi akses
        if ($evaluation->internship->student->school_id !== $school->id) {
            abort(403, 'Anda tidak memiliki akses ke penilaian ini.');
        }

        if (!$evaluation->isSchoolEvaluation()) {
            abort(403, 'Penilaian ini bukan dari sekolah.');
        }

        $validated = $request->validate([
            'discipline_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'skill_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'attitude_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'knowledge_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'communication_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'strengths' => ['nullable', 'string', 'max:1000'],
            'improvements' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $evaluation->update($validated);
        $evaluation->saveWithCalculation();

        return redirect()->route('school.evaluations.show', $evaluation)
            ->with('success', 'Penilaian siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified evaluation.
     */
    public function destroy(Evaluation $evaluation)
    {
        $school = Auth::user()->schoolProfile;

        // Validasi akses
        if ($evaluation->internship->student->school_id !== $school->id) {
            abort(403, 'Anda tidak memiliki akses ke penilaian ini.');
        }

        if (!$evaluation->isSchoolEvaluation()) {
            abort(403, 'Penilaian ini bukan dari sekolah.');
        }

        $evaluation->delete();

        return redirect()->route('school.evaluations.index')
            ->with('success', 'Penilaian siswa berhasil dihapus.');
    }
}
