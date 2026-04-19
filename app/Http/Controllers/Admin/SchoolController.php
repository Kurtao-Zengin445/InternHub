<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $schools = School::with('user')
            ->withCount('students')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('npsn', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.schools.index', compact('schools'));
    }

    public function create()
    {
        return view('admin.schools.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'address'         => ['required', 'string'],
            'phone'           => ['nullable', 'string', 'max:20'],
            'email'           => ['nullable', 'email'],
            'principal_name'  => ['nullable', 'string', 'max:255'],
            'npsn'            => ['nullable', 'string', 'max:20', 'unique:schools,npsn'],
            'school_type'     => ['nullable', 'string', 'max:50'],
            'logo'            => ['nullable', 'image', 'max:2048'],
            // Akun login sekolah
            'user_name'       => ['required', 'string', 'max:255'],
            'user_email'      => ['required', 'email', 'unique:users,email'],
            'user_password'   => ['required', 'string', 'min:8'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Buat akun user untuk sekolah
            $user = User::create([
                'name'     => $validated['user_name'],
                'email'    => $validated['user_email'],
                'password' => Hash::make($validated['user_password']),
                'role'     => 'school',
            ]);

            // Upload logo jika ada
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos/schools', 'public');
            }

            // Buat profil sekolah
            School::create([
                'user_id'        => $user->id,
                'name'           => $validated['name'],
                'address'        => $validated['address'],
                'phone'          => $validated['phone'] ?? null,
                'email'          => $validated['email'] ?? null,
                'principal_name' => $validated['principal_name'] ?? null,
                'npsn'           => $validated['npsn'] ?? null,
                'school_type'    => $validated['school_type'] ?? null,
                'logo'           => $logoPath,
            ]);
        });

        return redirect()
            ->route('admin.schools.index')
            ->with('success', 'Sekolah berhasil ditambahkan.');
    }

    public function show(School $school)
    {
        $school->load('user', 'supervisors.user');
        $school->loadCount(['students', 'applications']);

        $activeInternships = $school->students()
            ->whereHas('internships', fn($q) => $q->where('internships.status', 'active'))
            ->count();

        return view('admin.schools.show', compact('school', 'activeInternships'));
    }

    public function edit(School $school)
    {
        $school->load('user');
        return view('admin.schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'address'        => ['required', 'string'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email'],
            'principal_name' => ['nullable', 'string', 'max:255'],
            'npsn'           => ['nullable', 'string', 'max:20', Rule::unique('schools')->ignore($school->id)],
            'school_type'    => ['nullable', 'string', 'max:50'],
            'logo'           => ['nullable', 'image', 'max:2048'],
            'user_name'      => ['required', 'string', 'max:255'],
            'user_email'     => ['required', 'email', Rule::unique('users', 'email')->ignore($school->user_id)],
        ]);

        DB::transaction(function () use ($validated, $request, $school) {
            // Update akun user
            $school->user->update([
                'name'  => $validated['user_name'],
                'email' => $validated['user_email'],
            ]);

            // Update logo jika ada
            if ($request->hasFile('logo')) {
                $validated['logo'] = $request->file('logo')->store('logos/schools', 'public');
            }

            $school->update([
                'name'           => $validated['name'],
                'address'        => $validated['address'],
                'phone'          => $validated['phone'] ?? null,
                'email'          => $validated['email'] ?? null,
                'principal_name' => $validated['principal_name'] ?? null,
                'npsn'           => $validated['npsn'] ?? null,
                'school_type'    => $validated['school_type'] ?? null,
                'logo'           => $validated['logo'] ?? $school->logo,
            ]);
        });

        return redirect()
            ->route('admin.schools.show', $school)
            ->with('success', 'Data sekolah berhasil diperbarui.');
    }

    public function destroy(School $school)
    {
        // Cek apakah ada siswa yang sedang magang aktif
        $activeCount = $school->students()
            ->whereHas('internships', fn($q) => $q->where('internships.status', 'active'))
            ->count();

        if ($activeCount > 0) {
            return back()->with('error', "Tidak dapat menghapus sekolah karena masih ada {$activeCount} siswa yang sedang magang.");
        }

        DB::transaction(function () use ($school) {
            $school->user->delete(); // cascade → school terhapus juga
        });

        return redirect()
            ->route('admin.schools.index')
            ->with('success', 'Sekolah berhasil dihapus.');
    }
}
