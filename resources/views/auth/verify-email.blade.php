<x-guest-layout>
    <div class="mb-8">
        <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            Verifikasi Email
        </div>
        <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-950" style="font-family:'Syne',sans-serif;">
            Cek Inbox Anda
        </h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">
            Sebelum mulai menggunakan sistem, verifikasi alamat email Anda melalui tautan yang baru kami kirimkan.
            Jika email belum diterima, Anda bisa mengirim ulang dari halaman ini.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ __('Link verifikasi baru telah dikirim ke alamat email Anda.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between gap-4 border-t border-slate-200 pt-5">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Kirim Ulang Verifikasi') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-sm font-medium text-slate-600 transition hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                {{ __('Keluar') }}
            </button>
        </form>
    </div>
</x-guest-layout>
