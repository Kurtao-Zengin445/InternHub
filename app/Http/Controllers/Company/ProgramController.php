<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\InternshipProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $company = $this->currentCompany();

        $programs = InternshipProgram::where('company_id', $company->id)
            ->withCount(['applications', 'acceptedApplications'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('company.programs.index', compact('programs'));
    }

    public function create()
    {
        $this->currentCompany();

        return view('company.programs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:50'],
            'requirements' => ['nullable', 'string'],
            'quota' => ['required', 'integer', 'min:1', 'max:100'],
            'field' => ['nullable', 'string', 'max:100'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'registration_start' => ['required', 'date', 'before_or_equal:start_date'],
            'registration_end' => ['required', 'date', 'after:registration_start', 'before_or_equal:start_date'],
            'status' => ['required', Rule::in(['draft', 'open'])],
        ]);

        $company = $this->currentCompany();

        InternshipProgram::create(array_merge($validated, [
            'company_id' => $company->id,
        ]));

        return redirect()
            ->route('company.programs.index')
            ->with('success', 'Program magang berhasil dibuat.');
    }

    public function show(InternshipProgram $program)
    {
        $this->authorizeProgram($program);

        $program->load('company');
        $program->loadCount(['applications', 'acceptedApplications']);

        $applicationStats = [
            'pending' => $program->applications()->where('status', 'pending')->count(),
            'accepted' => $program->applications()->where('status', 'accepted')->count(),
            'rejected' => $program->applications()->where('status', 'rejected')->count(),
        ];

        $recentApplications = $program->applications()
            ->with('student.user', 'student.school')
            ->latest('applied_at')
            ->take(5)
            ->get();

        return view('company.programs.show', compact('program', 'applicationStats', 'recentApplications'));
    }

    public function edit(InternshipProgram $program)
    {
        $this->authorizeProgram($program);

        abort_if($program->status === 'completed', 403, 'Program yang sudah selesai tidak dapat diedit.');

        return view('company.programs.edit', compact('program'));
    }

    public function update(Request $request, InternshipProgram $program)
    {
        $this->authorizeProgram($program);

        abort_if($program->status === 'completed', 403, 'Program yang sudah selesai tidak dapat diubah.');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:50'],
            'requirements' => ['nullable', 'string'],
            'quota' => ['required', 'integer', 'min:1', 'max:100'],
            'field' => ['nullable', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'registration_start' => ['required', 'date', 'before_or_equal:start_date'],
            'registration_end' => ['required', 'date', 'after:registration_start', 'before_or_equal:start_date'],
            'status' => ['required', Rule::in(['draft', 'open', 'closed'])],
        ]);

        $acceptedCount = $program->acceptedApplications()->count();
        if ($validated['quota'] < $acceptedCount) {
            return back()
                ->withInput()
                ->withErrors(['quota' => "Kuota tidak boleh kurang dari jumlah peserta yang sudah diterima ({$acceptedCount} orang)."]); 
        }

        $program->update($validated);

        return redirect()
            ->route('company.programs.show', $program)
            ->with('success', 'Program magang berhasil diperbarui.');
    }

    public function destroy(InternshipProgram $program)
    {
        $this->authorizeProgram($program);

        if ($program->acceptedApplications()->count() > 0) {
            return back()->with('error', 'Program tidak dapat dihapus karena sudah ada peserta yang diterima.');
        }

        $program->delete();

        return redirect()
            ->route('company.programs.index')
            ->with('success', 'Program magang berhasil dihapus.');
    }

    public function close(InternshipProgram $program)
    {
        $this->authorizeProgram($program);

        abort_if($program->status !== 'open', 422, 'Program tidak dalam status open.');

        $program->update(['status' => 'closed']);

        return back()->with('success', 'Pendaftaran program berhasil ditutup.');
    }

    private function authorizeProgram(InternshipProgram $program): void
    {
        abort_if($program->company_id !== $this->currentCompany()->id, 403);
    }

    private function currentCompany(): Company
    {
        $company = Auth::user()?->company;

        abort_if(!$company, 403, 'Profil perusahaan belum tersedia. Silakan lengkapi profil terlebih dahulu.');

        return $company;
    }
}
