<x-guest-layout>
    <div class="mb-8">
        <div class="inline-flex items-center gap-2 rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-sky-700">
            <span class="h-2 w-2 rounded-full bg-sky-500"></span>
            Pemulihan Akun
        </div>
        <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-950" style="font-family:'Syne',sans-serif;">
            Lupa Password
        </h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">
            Masukkan email akun Anda. Kami akan mengirimkan tautan untuk mengatur password baru.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6 flex items-center justify-between gap-4 border-t border-slate-200 pt-5">
            <a href="{{ route('login') }}" class="text-sm text-slate-600 transition hover:text-slate-900">
                Kembali ke login
            </a>
            <x-primary-button>
                {{ __('Kirim Link Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
