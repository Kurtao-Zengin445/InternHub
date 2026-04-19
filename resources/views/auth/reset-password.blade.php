<x-guest-layout>
    <div class="mb-8">
        <div class="inline-flex items-center gap-2 rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-violet-700">
            <span class="h-2 w-2 rounded-full bg-violet-500"></span>
            Password Baru
        </div>
        <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-950" style="font-family:'Syne',sans-serif;">
            Atur Ulang Password
        </h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">
            Masukkan password baru untuk melanjutkan akses ke akun Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="grid gap-5">
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />

            <x-text-input id="password_confirmation" class="mt-2 block w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        </div>

        <div class="mt-6 flex items-center justify-between gap-4 border-t border-slate-200 pt-5">
            <a href="{{ route('login') }}" class="text-sm text-slate-600 transition hover:text-slate-900">
                Kembali ke login
            </a>
            <x-primary-button>
                {{ __('Simpan Password Baru') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
