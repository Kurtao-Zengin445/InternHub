<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SupervisorController extends Controller
{
    public function index(Request $request)
    {
        $supervisors = Supervisor::with(['user', 'school'])
            ->withCount('internships')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('nip', 'like', '%' . $search . '%')
                        ->orWhere('position', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('school', function ($schoolQuery) use ($search) {
                            $schoolQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($request->filled('school_id'), fn($q) => $q->where('school_id', $request->school_id))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $schools = School::orderBy('name')->get();

        return view('admin.supervisors.index', compact('supervisors', 'schools'));
    }

    public function create()
    {
        $schools = School::orderBy('name')->get();
        $availableUsers = User::query()
            ->where('role', 'supervisor')
            ->whereDoesntHave('supervisor')
            ->orderBy('name')
            ->get();

        return view('admin.supervisors.create', compact('schools', 'availableUsers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_id'      => ['required', 'exists:schools,id'],
            'nip'            => ['nullable', 'string', 'max:30', 'unique:supervisors,nip'],
            'position'       => ['nullable', 'string', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'account_mode'   => ['required', Rule::in(['existing', 'new'])],
            'existing_user_id' => [
                'nullable',
                'required_if:account_mode,existing',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'supervisor');
                }),
            ],
            'user_name'      => ['nullable', 'required_if:account_mode,new', 'string', 'max:255'],
            'user_email'     => ['nullable', 'required_if:account_mode,new', 'email', 'unique:users,email'],
            'user_password'  => ['nullable', 'required_if:account_mode,new', 'string', 'min:8'],
        ]);

        DB::transaction(function () use ($validated) {
            if ($validated['account_mode'] === 'existing') {
                $user = User::where('role', 'supervisor')
                    ->whereDoesntHave('supervisor')
                    ->findOrFail($validated['existing_user_id']);
            } else {
                $user = User::create([
                    'name'      => $validated['user_name'],
                    'email'     => $validated['user_email'],
                    'password'  => Hash::make($validated['user_password']),
                    'role'      => 'supervisor',
                    'is_active' => true,
                ]);
            }

            Supervisor::create([
                'user_id'   => $user->id,
                'school_id' => $validated['school_id'],
                'nip'       => $validated['nip'] ?? null,
                'position'  => $validated['position'] ?? null,
                'phone'     => $validated['phone'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.supervisors.index')
            ->with('success', 'Pembimbing berhasil ditambahkan.');
    }

    public function show(Supervisor $supervisor)
    {
        $supervisor->load(['user', 'school', 'internships.application.student.user']);
        $supervisor->loadCount('internships');

        $recentInternships = $supervisor->internships()
            ->with(['application.student.user', 'application.program.company'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.supervisors.show', compact('supervisor', 'recentInternships'));
    }

    public function edit(Supervisor $supervisor)
    {
        $supervisor->load('user');
        $schools = School::orderBy('name')->get();

        return view('admin.supervisors.edit', compact('supervisor', 'schools'));
    }

    public function update(Request $request, Supervisor $supervisor)
    {
        $validated = $request->validate([
            'school_id'   => ['required', 'exists:schools,id'],
            'nip'         => ['nullable', 'string', 'max:30', Rule::unique('supervisors')->ignore($supervisor->id)],
            'position'    => ['nullable', 'string', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'user_name'   => ['required', 'string', 'max:255'],
            'user_email'  => ['required', 'email', Rule::unique('users', 'email')->ignore($supervisor->user_id)],
        ]);

        DB::transaction(function () use ($validated, $supervisor) {
            $supervisor->user->update([
                'name'  => $validated['user_name'],
                'email' => $validated['user_email'],
                'role'  => 'supervisor',
            ]);

            $supervisor->update([
                'school_id' => $validated['school_id'],
                'nip'       => $validated['nip'] ?? null,
                'position'  => $validated['position'] ?? null,
                'phone'     => $validated['phone'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.supervisors.show', $supervisor)
            ->with('success', 'Data pembimbing berhasil diperbarui.');
    }

    public function destroy(Supervisor $supervisor)
    {
        $activeInternships = $supervisor->internships()
            ->where('status', 'active')
            ->count();

        if ($activeInternships > 0) {
            return back()->with('error', 'Tidak dapat menghapus pembimbing karena masih memiliki siswa magang aktif.');
        }

        DB::transaction(function () use ($supervisor) {
            $supervisor->user->delete();
        });

        return redirect()
            ->route('admin.supervisors.index')
            ->with('success', 'Pembimbing berhasil dihapus.');
    }
}
