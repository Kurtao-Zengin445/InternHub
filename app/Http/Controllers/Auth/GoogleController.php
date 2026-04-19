<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    private const PENDING_GOOGLE_REGISTRATION = 'pending_google_registration';

    public function redirect(Request $request): RedirectResponse
    {
        $role = $request->input('role', 'student');

        if (!in_array($role, ['student', 'school', 'supervisor', 'company'], true)) {
            $role = 'student';
        }

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            if ($user && $user->hasPendingGoogleRegistration()) {
                $this->deleteIncompleteGoogleUser($user);
            }

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $request->session()->forget(self::PENDING_GOOGLE_REGISTRATION);
        $request->session()->put('google_register_role', $role);

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Login Google gagal diproses. Silakan coba lagi.',
                ]);
        }

        $role = session()->pull('google_register_role', 'student');

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user && $user->hasPendingGoogleRegistration()) {
            $this->deleteIncompleteGoogleUser($user);
            $user = null;
        }

        if (!$user) {
            session([
                self::PENDING_GOOGLE_REGISTRATION => [
                    'name' => $googleUser->getName() ?: Str::before($googleUser->getEmail(), '@'),
                    'email' => $googleUser->getEmail(),
                    'role' => $role,
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ],
            ]);

            return redirect()->route('register.complete');
        }

        if (!$user->is_active) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Akun Anda sedang nonaktif. Silakan hubungi admin.',
                ]);
        }

        $user->forceFill([
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        request()->session()->forget(self::PENDING_GOOGLE_REGISTRATION);
        Auth::guard('web')->login($user, true);

        request()->session()->regenerate();

        if (!$user->hasCompletedRoleProfile()) {
            return redirect()->route('register.complete');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function handleOneTap(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')
                ->userFromToken($request->input('credential'));
        } catch (Throwable) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Login Google gagal diproses. Silakan coba lagi.',
                ]);
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user && $user->hasPendingGoogleRegistration()) {
            $this->deleteIncompleteGoogleUser($user);
            $user = null;
        }

        if (!$user) {
            session([
                self::PENDING_GOOGLE_REGISTRATION => [
                    'name' => $googleUser->getName() ?: Str::before($googleUser->getEmail(), '@'),
                    'email' => $googleUser->getEmail(),
                    'role' => 'student',
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ],
            ]);

            return redirect()->route('register.complete');
        }

        if (!$user->is_active) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Akun Anda sedang nonaktif. Silakan hubungi admin.',
                ]);
        }

        $user->forceFill([
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        request()->session()->forget(self::PENDING_GOOGLE_REGISTRATION);
        Auth::guard('web')->login($user, true);

        request()->session()->regenerate();

        if (!$user->hasCompletedRoleProfile()) {
            return redirect()->route('register.complete');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function deleteIncompleteGoogleUser(User $user): void
    {
        if (!$user->hasPendingGoogleRegistration()) {
            return;
        }

        DB::transaction(function () use ($user) {
            $user->notifications()->delete();
            $user->delete();
        });
    }
}
