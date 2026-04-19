<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $profile = $this->profileRelation($user);

        if (!$profile && $user->role !== 'admin') {
            return redirect()
                ->route('register.complete')
                ->with('warning', 'Lengkapi profil terlebih dahulu sebelum mengubah data.');
        }

        return view('profile.edit', [
            'user' => $user,
            'profile' => $profile,
            'roleMeta' => $this->roleMeta($user->role),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $this->profileRelation($user);

        if (!$profile && $user->role !== 'admin') {
            return redirect()->route('register.complete');
        }

        $validated = $this->validateRequest($request, $user);

        DB::transaction(function () use ($request, $user, $profile, $validated) {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            if (($validated['remove_avatar'] ?? false) && $user->avatar) {
                if (!str_starts_with($user->avatar, 'http')) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $userData['avatar'] = null;
            }

            if ($request->hasFile('avatar')) {
                if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            if ($validated['email'] !== $user->email) {
                $userData['email_verified_at'] = null;
            }

            $user->update($userData);

            if ($profile) {
                $profile->update($this->profilePayload($validated, $user->role));
            }
        });

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function validateRequest(Request $request, User $user): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];

        $roleRules = match ($user->role) {
            'student' => [
                'phone' => ['nullable', 'string', 'max:20'],
                'gender' => ['nullable', 'in:male,female'],
                'birth_place' => ['nullable', 'string', 'max:255'],
                'birth_date' => ['nullable', 'date'],
                'address' => ['nullable', 'string'],
            ],
            'school' => [
                'phone' => ['nullable', 'string', 'max:20'],
                'institution_email' => ['nullable', 'email'],
                'principal_name' => ['nullable', 'string', 'max:255'],
                'school_type' => ['nullable', 'string', 'max:50'],
                'address' => ['nullable', 'string'],
            ],
            'supervisor' => [
                'phone' => ['nullable', 'string', 'max:20'],
                'position' => ['nullable', 'string', 'max:255'],
            ],
            'company' => [
                'phone' => ['nullable', 'string', 'max:20'],
                'company_email' => ['nullable', 'email'],
                'contact_person' => ['nullable', 'string', 'max:255'],
                'contact_person_phone' => ['nullable', 'string', 'max:20'],
                'industry' => ['nullable', 'string', 'max:100'],
                'website' => ['nullable', 'url'],
                'address' => ['nullable', 'string'],
                'description' => ['nullable', 'string'],
            ],
            'admin' => [],
            default => [],
        };

        return $request->validate($rules + $roleRules);
    }

    private function profilePayload(array $validated, string $role): array
    {
        return match ($role) {
            'student' => [
                'phone' => $validated['phone'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'birth_place' => $validated['birth_place'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'address' => $validated['address'] ?? null,
            ],
            'school' => [
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['institution_email'] ?? null,
                'principal_name' => $validated['principal_name'] ?? null,
                'school_type' => $validated['school_type'] ?? null,
                'address' => $validated['address'] ?? null,
            ],
            'supervisor' => [
                'phone' => $validated['phone'] ?? null,
                'position' => $validated['position'] ?? null,
            ],
            'company' => [
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['company_email'] ?? null,
                'contact_person' => $validated['contact_person'] ?? null,
                'contact_person_phone' => $validated['contact_person_phone'] ?? null,
                'industry' => $validated['industry'] ?? null,
                'website' => $validated['website'] ?? null,
                'address' => $validated['address'] ?? null,
                'description' => $validated['description'] ?? null,
            ],
            default => [],
        };
    }

    private function profileRelation(User $user): mixed
    {
        return match ($user->role) {
            'student' => $user->student,
            'school' => $user->schoolProfile,
            'supervisor' => $user->supervisor,
            'company' => $user->company,
            default => null,
        };
    }

    private function roleMeta(string $role): array
    {
        return match ($role) {
            'student' => ['label' => 'Siswa', 'icon' => 'bi-mortarboard-fill', 'accent' => '#1d4ed8'],
            'school' => ['label' => 'Sekolah', 'icon' => 'bi-building', 'accent' => '#059669'],
            'supervisor' => ['label' => 'Pembimbing', 'icon' => 'bi-person-workspace', 'accent' => '#d97706'],
            'company' => ['label' => 'Perusahaan', 'icon' => 'bi-briefcase-fill', 'accent' => '#7c3aed'],
            'admin' => ['label' => 'Administrator', 'icon' => 'bi-shield-fill-check', 'accent' => '#b45309'],
            default => ['label' => ucfirst($role), 'icon' => 'bi-person-fill', 'accent' => '#334155'],
        };
    }
}

