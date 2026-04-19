<x-guest-layout>
    <div class="mb-8">
        <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-blue-700">
            <span class="h-2 w-2 rounded-full bg-blue-600"></span>
            Masuk
        </div>
        <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-950" style="font-family:'Syne',sans-serif;">
            Selamat Datang Kembali
        </h2>
        <p class="mt-2 text-sm leading-6 text-slate-600">
            Masuk ke akun Anda untuk melanjutkan pengelolaan program magang.
        </p>
    </div>

    <x-auth-session-status class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" :status="session('status')" />

    <div class="space-y-3">
        {{-- Google One Tap Prompt --}}
        <div
          id="g_id_onload"
          data-client_id="{{ config('services.google.client_id') }}"
          data-login_uri="{{ route('google.onetap') }}"
          data-auto_select="true"
          data-cancel_on_tap_outside="false"
          data-context="signin"
          data-use_fedcm_for_prompt="true"
          data-itp_support="true"
          data-_token="{{ csrf_token() }}"
        ></div>

        <a href="{{ route('google.redirect') }}"
           class="inline-flex w-full items-center justify-center gap-3 rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="h-5 w-5" aria-hidden="true">
                <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.6 32.7 29.2 36 24c-6.6 0-12-5.4-12-12S17.4 12 24 12c3 0 5.8 1.1 7.9 3l5.7-5.7C34.1 6.1 29.3 4 24 4c-7.7 0-14.3 4.3-17.7 10.7Z"/>
                <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15 18.9 12 24 12c3 0 5.8 1.1 7.9 3l5.7-5.7C34.1 6.1 29.3 4 24 4c-7.7 0-14.3 4.3-17.7 10.7Z"/>
                <path fill="#4CAF50" d="M24 44c5.2 0 10-2 13.5-5.2l-6.2-5.2c-2 1.5-4.5 2.4-7.3 2.4-5.2 0-9.6-3.3-11.2-8l-6.5 5C9.6 39.5 16.3 44 24 44Z"/>
                <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.2 4.2-4 5.6l6.2 5.2C36.9 38.5 44 33 44 24c0-1.3-.1-2.4-.4-3.5Z"/>
            </svg>
            <span>{{ __('Masuk dengan Google') }}</span>
        </a>

        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center text-xs uppercase">
                <span class="bg-white px-3 text-slate-400">{{ __('Atau lanjut dengan email') }}</span>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}" class="mt-6">
        @csrf

        <div class="space-y-5">
        <div>
            <x-input-label for="email" class="text-sm font-semibold text-slate-700" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between gap-3">
                <x-input-label for="password" class="text-sm font-semibold text-slate-700" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-blue-600 transition hover:text-blue-700" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <div class="pt-3">
            <x-primary-button class="inline-flex min-h-[54px] w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus:bg-slate-800 active:bg-slate-900">
                {{ __('Masuk') }}
            </x-primary-button>
        </div>
        </div>

        <div class="mt-8 border-t border-slate-200 pt-6 pb-1 text-center">
            <p class="text-sm text-slate-600">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-500">
                    Daftar di sini
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
