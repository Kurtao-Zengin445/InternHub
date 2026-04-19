<x-guest-layout>
    <div class="mb-8">
        <div class="inline-flex items-center gap-2 rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-rose-700">
            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
            Konfirmasi
        </div>
        <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-950" style="font-family:'Syne',sans-serif;">
            Konfirmasi Password
        </h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">
            Area ini memerlukan verifikasi tambahan. Masukkan password Anda untuk melanjutkan.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="mt-2 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-6 flex justify-end border-t border-slate-200 pt-5">
            <x-primary-button>
                {{ __('Konfirmasi') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
