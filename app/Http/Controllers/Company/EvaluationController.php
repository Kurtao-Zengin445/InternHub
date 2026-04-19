<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Evaluation;
use App\Models\Internship;
use App\Models\Notification;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index()
    {
        $company = $this->currentCompany();

        $internships = Internship::with([
            'application.student.user',
            'application.student.school',
            'application.program',
            'evaluations',
        ])
        ->whereHas('application.program', fn ($query) => $query->where('company_id', $company->id))
        ->whereIn('status', ['active', 'completed'])
        ->get()
        ->map(function ($internship) {
            $internship->company_evaluation = $internship->companyEvaluation();
            return $internship;
        });

        return view('company.evaluations.index', compact('internships'));
    }

    public function create(Internship $internship)
    {
        $this->authorizeInternship($internship);

        $existing = Evaluation::where('internship_id', $internship->id)
            ->where('evaluator_id', auth()->guard('web')->id())
            ->where('evaluator_type', 'company')
            ->first();

        if ($existing) {
            return redirect()
                ->route('company.evaluations.edit', $existing)
                ->with('info', 'Anda sudah memberikan penilaian. Silakan edit jika perlu diperbarui.');
        }

        $internship->load('application.student.user', 'application.student.school', 'application.program.company');

        return view('company.evaluations.form', compact('internship'));
    }

    public function store(Request $request, Internship $internship)
    {
        $this->authorizeInternship($internship);

        $validated = $this->validateEvaluation($request);

        $exists = Evaluation::where('internship_id', $internship->id)
            ->where('evaluator_id', auth()->guard('web')->id())
            ->where('evaluator_type', 'company')
            ->exists();

        abort_if($exists, 422, 'Anda sudah memberikan penilaian untuk siswa ini.');

        $evaluation = new Evaluation(array_merge($validated, [
            'internship_id' => $internship->id,
            'evaluator_id' => auth()->guard('web')->id(),
            'evaluator_type' => 'company',
        ]));

        $evaluation->saveWithCalculation();

        $student = $internship->application->student;
        Notification::send(
            $student->user_id,
            'Penilaian Perusahaan',
            'Perusahaan telah memberikan penilaian akhir magang Anda.',
            'evaluation',
            $evaluation,
            route('student.internship.evaluation')
        );

        if ($internship->supervisor_id) {
            Notification::send(
                $internship->supervisor->user_id,
                'Perusahaan Telah Memberi Nilai',
                "{$internship->application->program->company->name} telah memberikan penilaian untuk {$student->user->name}.",
                'info',
                $evaluation
            );
        }

        return redirect()
            ->route('company.evaluations.index')
            ->with('success', "Penilaian untuk {$student->user->name} berhasil disimpan.");
    }

    public function show(Evaluation $evaluation)
    {
        $this->authorizeEvaluation($evaluation);

        $evaluation->load('internship.application.student.user', 'internship.application.student.school', 'internship.application.program.company', 'evaluator');

        return view('company.evaluations.show', compact('evaluation'));
    }

    public function edit(Evaluation $evaluation)
    {
        $this->authorizeEvaluation($evaluation);

        $evaluation->load('internship.application.student.user', 'internship.application.student.school', 'internship.application.program.company');

        return view('company.evaluations.form', compact('evaluation'));
    }

    public function update(Request $request, Evaluation $evaluation)
    {
        $this->authorizeEvaluation($evaluation);

        $validated = $this->validateEvaluation($request);

        $evaluation->fill($validated)->saveWithCalculation();

        return redirect()
            ->route('company.evaluations.index')
            ->with('success', 'Penilaian berhasil diperbarui.');
    }

    private function validateEvaluation(Request $request): array
    {
        return $request->validate([
            'discipline_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'skill_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'attitude_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'knowledge_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'communication_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'strengths' => ['nullable', 'string', 'max:1000'],
            'improvements' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function authorizeInternship(Internship $internship): void
    {
        abort_if($internship->application->program->company_id !== $this->currentCompany()->id, 403);
    }

    private function authorizeEvaluation(Evaluation $evaluation): void
    {
        abort_if(
            $evaluation->evaluator_id !== auth()->guard('web')->id() ||
            $evaluation->evaluator_type !== 'company',
            403
        );
    }

    private function currentCompany(): Company
    {
        $company = auth()->guard('web')->user()?->company;

        abort_if(!$company, 403, 'Profil perusahaan belum tersedia. Silakan lengkapi profil terlebih dahulu.');

        return $company;
    }
}
