<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::with('user')
            ->withCount('programs')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('industry', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('industry'), function ($q) use ($request) {
                $q->where('industry', $request->industry);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $industries = Company::distinct()->pluck('industry')->filter()->sort()->values();

        return view('admin.companies.index', compact('companies', 'industries'));
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'address'               => ['required', 'string'],
            'phone'                 => ['nullable', 'string', 'max:20'],
            'email'                 => ['nullable', 'email'],
            'contact_person'        => ['nullable', 'string', 'max:255'],
            'contact_person_phone'  => ['nullable', 'string', 'max:20'],
            'description'           => ['nullable', 'string'],
            'industry'              => ['nullable', 'string', 'max:100'],
            'website'               => ['nullable', 'url'],
            'logo'                  => ['nullable', 'image', 'max:2048'],
            // Akun login perusahaan
            'user_name'             => ['required', 'string', 'max:255'],
            'user_email'            => ['required', 'email', 'unique:users,email'],
            'user_password'         => ['required', 'string', 'min:8'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'name'     => $validated['user_name'],
                'email'    => $validated['user_email'],
                'password' => Hash::make($validated['user_password']),
                'role'     => 'company',
            ]);

            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos/companies', 'public');
            }

            Company::create([
                'user_id'               => $user->id,
                'name'                  => $validated['name'],
                'address'               => $validated['address'],
                'phone'                 => $validated['phone'] ?? null,
                'email'                 => $validated['email'] ?? null,
                'contact_person'        => $validated['contact_person'] ?? null,
                'contact_person_phone'  => $validated['contact_person_phone'] ?? null,
                'description'           => $validated['description'] ?? null,
                'industry'              => $validated['industry'] ?? null,
                'website'               => $validated['website'] ?? null,
                'logo'                  => $logoPath,
            ]);
        });

        return redirect()
            ->route('admin.companies.index')
            ->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    public function show(Company $company)
    {
        $company->load('user')->loadCount(['programs']);

        $programStats = [
            'total'     => $company->programs()->count(),
            'open'      => $company->programs()->where('status', 'open')->count(),
            'completed' => $company->programs()->where('status', 'completed')->count(),
        ];

        $recentPrograms = $company->programs()
            ->withCount('applications')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.companies.show', compact('company', 'programStats', 'recentPrograms'));
    }

    public function edit(Company $company)
    {
        $company->load('user');
        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'address'              => ['required', 'string'],
            'phone'                => ['nullable', 'string', 'max:20'],
            'email'                => ['nullable', 'email'],
            'contact_person'       => ['nullable', 'string', 'max:255'],
            'contact_person_phone' => ['nullable', 'string', 'max:20'],
            'description'          => ['nullable', 'string'],
            'industry'             => ['nullable', 'string', 'max:100'],
            'website'              => ['nullable', 'url'],
            'logo'                 => ['nullable', 'image', 'max:2048'],
            'user_name'            => ['required', 'string', 'max:255'],
            'user_email'           => ['required', 'email', Rule::unique('users', 'email')->ignore($company->user_id)],
        ]);

        DB::transaction(function () use ($validated, $request, $company) {
            $company->user->update([
                'name'  => $validated['user_name'],
                'email' => $validated['user_email'],
            ]);

            if ($request->hasFile('logo')) {
                $validated['logo'] = $request->file('logo')->store('logos/companies', 'public');
            }

            $company->update([
                'name'                 => $validated['name'],
                'address'              => $validated['address'],
                'phone'                => $validated['phone'] ?? null,
                'email'                => $validated['email'] ?? null,
                'contact_person'       => $validated['contact_person'] ?? null,
                'contact_person_phone' => $validated['contact_person_phone'] ?? null,
                'description'          => $validated['description'] ?? null,
                'industry'             => $validated['industry'] ?? null,
                'website'              => $validated['website'] ?? null,
                'logo'                 => $validated['logo'] ?? $company->logo,
            ]);
        });

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('success', 'Data perusahaan berhasil diperbarui.');
    }

    public function destroy(Company $company)
    {
        $activePrograms = $company->programs()
            ->whereIn('status', ['open', 'closed'])
            ->whereHas('applications', fn($q) => $q->where('status', 'accepted'))
            ->count();

        if ($activePrograms > 0) {
            return back()->with('error', 'Tidak dapat menghapus perusahaan karena masih ada program magang yang berjalan.');
        }

        DB::transaction(function () use ($company) {
            $company->user->delete();
        });

        return redirect()
            ->route('admin.companies.index')
            ->with('success', 'Perusahaan berhasil dihapus.');
    }
}
