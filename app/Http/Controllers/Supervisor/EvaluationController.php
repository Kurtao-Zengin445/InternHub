<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Internship;
use App\Models\Notification;
use App\Models\Supervisor;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    /**
     * Daftar siswa yang sudah/belum dinilai.
     */
    public function index()
    {
        $supervisor    = $this->currentSupervisor();
        $internshipIds = Internship::where('supervisor_id', $supervisor->id)->pluck('id');

        $internships = Internship::with([
            'application.student.user',
            'application.program.company',
            'evaluations',
        ])
        ->whereIn('id', $internshipIds)
        ->whereIn('status', ['active', 'completed'])
        ->get()
        ->map(function ($internship) use ($supervisor) {
            $internship->supervisor_evaluation = $internship->supervisorEvaluation();
            return $internship;
        });

        return view('supervisor.evaluations.index', compact('internships'));
    }

    /**
     * Form penilaian siswa.
     */
    public function create(Internship $internship)
    {
        $this->authorizeSupervisor($internship);

        // Cek apakah sudah pernah dinilai
        $existing = Evaluation::where('internship_id', $internship->id)
            ->where('evaluator_id', auth('web')->id())
            ->where('evaluator_type', 'supervisor')
            ->first();

        if ($existing) {
            return redirect()
                ->route('supervisor.evaluations.edit', $existing)
                ->with('info', 'Anda sudah memberikan penilaian. Silakan edit jika perlu diperbarui.');
        }

        $internship->load('application.student.user', 'application.student.school', 'application.program.company');

        return view('supervisor.evaluations.form', compact('internship'));
    }

    /**
     * Simpan penilaian baru.
     */
    public function store(Request $request, Internship $internship)
    {
        $this->authorizeSupervisor($internship);

        $validated = $this->validateEvaluation($request);

        // Cek duplikasi
        $exists = Evaluation::where('internship_id', $internship->id)
            ->where('evaluator_id', auth()->id())
            ->where('evaluator_type', 'supervisor')
            ->exists();

        abort_if($exists, 422, 'Anda sudah memberikan penilaian untuk siswa ini.');

        $evaluation = new Evaluation(array_merge($validated, [
            'internship_id'  => $internship->id,
            'evaluator_id'   => auth()->id(),
            'evaluator_type' => 'supervisor',
        ]));

        $evaluation->saveWithCalculation();

        // Notifikasi ke siswa
        $student = $internship->application->student;
        Notification::send(
            $student->user_id,
            'Penilaian Pembimbing Sekolah',
            'Pembimbing sekolah telah memberikan penilaian akhir magang Anda.',
            'evaluation',
            $evaluation,
            route('student.internship.evaluation')
        );

        return redirect()
            ->route('supervisor.evaluations.index')
            ->with('success', "Penilaian untuk {$student->user->name} berhasil disimpan.");
    }

    /**
     * Form edit penilaian.
     */
    public function edit(Evaluation $evaluation)
    {
        $this->authorizeEvaluation($evaluation);

        $evaluation->load('internship.application.student.user', 'internship.application.student.school', 'internship.application.program.company');

        return view('supervisor.evaluations.form', compact('evaluation'));
    }

    /**
     * Perbarui penilaian.
     */
    public function update(Request $request, Evaluation $evaluation)
    {
        $this->authorizeEvaluation($evaluation);

        $validated = $this->validateEvaluation($request);

        $evaluation->fill($validated)->saveWithCalculation();

        return redirect()
            ->route('supervisor.evaluations.index')
            ->with('success', 'Penilaian berhasil diperbarui.');
    }

    /**
     * Detail penilaian.
     */
    public function show(Evaluation $evaluation)
    {
        $this->authorizeEvaluation($evaluation);

        $evaluation->load('internship.application.student.user', 'internship.application.student.school', 'internship.application.program.company', 'evaluator');

        return view('supervisor.evaluations.show', compact('evaluation'));
    }

    // ─── Private helpers ───────────────────────────────────

    private function validateEvaluation(Request $request): array
    {
        return $request->validate([
            'discipline_score'    => ['required', 'numeric', 'min:0', 'max:100'],
            'skill_score'         => ['required', 'numeric', 'min:0', 'max:100'],
            'attitude_score'      => ['required', 'numeric', 'min:0', 'max:100'],
            'knowledge_score'     => ['required', 'numeric', 'min:0', 'max:100'],
            'communication_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'strengths'           => ['nullable', 'string', 'max:1000'],
            'improvements'        => ['nullable', 'string', 'max:1000'],
            'notes'               => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function authorizeSupervisor(Internship $internship): void
    {
        $supervisorId = $this->currentSupervisor()->id;

        abort_if($internship->supervisor_id !== $supervisorId, 403);
    }

    private function authorizeEvaluation(Evaluation $evaluation): void
    {
        $userId = auth()->guard('web')->id();

        abort_if(
            $evaluation->evaluator_id !== $userId ||
            $evaluation->evaluator_type !== 'supervisor',
            403
        );
    }

    private function currentSupervisor(): Supervisor
    {
        $supervisor = auth()->user()?->supervisor;

        abort_if(!$supervisor, 403, 'Profil pembimbing belum tersedia. Silakan hubungi admin.');

        return $supervisor;
    }
}
