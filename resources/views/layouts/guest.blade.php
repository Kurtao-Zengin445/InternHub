<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'InternHub - Sistem Manajemen Magang')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('internhub-favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Google Identity Services untuk One Tap Sign In --}}
    @guest
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    @endguest
</head>
<body class="min-h-screen bg-slate-950 text-slate-900 antialiased" style="font-family:'Plus Jakarta Sans',sans-serif;">
    @php
        $guestRouteName = request()->route()?->getName();
        $guestBackUrl = route('home');
        $showGuestBackButton = in_array($guestRouteName, [
            'register',
            'login',
            'password.request',
            'password.reset',
            'verification.notice',
            'register.complete',
        ], true);

        if ($guestRouteName === 'register.complete') {
            $guestBackUrl = route('register');
        } elseif ($guestRouteName === 'login' || $guestRouteName === 'register') {
            $guestBackUrl = route('home');
        } else {
            $guestBackUrl = route('login');
        }
    @endphp

    <div class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(59,130,246,0.20),_transparent_30%),radial-gradient(circle_at_bottom_left,_rgba(245,158,11,0.16),_transparent_28%)]"></div>
        <div class="absolute inset-0 opacity-40" style="background-image:linear-gradient(rgba(255,255,255,0.06) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.06) 1px, transparent 1px);background-size:40px 40px;"></div>

        <div class="relative mx-auto flex min-h-screen w-full max-w-7xl items-center px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid w-full gap-6 overflow-hidden rounded-[32px] border border-white/15 bg-white/10 shadow-2xl backdrop-blur-xl lg:grid-cols-[1.05fr_0.95fr]">
                <div class="hidden min-h-[720px] flex-col justify-between bg-slate-950/75 p-10 text-white lg:flex">
                    <div>
                        <a href="/" class="text-white no-underline">
                            <x-brand-lockup theme="light" size="md" />
                        </a>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-blue-400/30 bg-blue-400/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-blue-200">
                                <span class="h-2 w-2 rounded-full bg-blue-300"></span>
                                Platform Terintegrasi
                            </div>
                            <h1 class="max-w-xl text-5xl font-extrabold leading-tight tracking-tight" style="font-family:'Montserrat',sans-serif;">
                                Akses Magang Lebih Rapi, Cepat, dan Profesional.
                            </h1>
                            <p class="mt-5 max-w-lg text-base leading-7 text-slate-300">
                                Kelola pendaftaran, pelaporan harian, verifikasi dokumen, dan evaluasi siswa dalam satu alur digital yang nyaman dipakai.
                            </p>
                        </div>

                        <div class="grid gap-4">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/15 text-blue-200">
                                        <i class="bi bi-person-check-fill text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-white">Akun untuk setiap peran utama</div>
                                        <div class="text-sm text-slate-300">Siswa, sekolah, pembimbing, dan perusahaan bisa masuk lewat jalur yang sesuai.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-500/15 text-amber-200">
                                        <i class="bi bi-shield-check text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-white">Login aman dan fleksibel</div>
                                        <div class="text-sm text-slate-300">Gunakan email dan password, atau Google untuk pendaftaran siswa yang lebih cepat.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm text-slate-400">
                        <span>InternHub &copy; {{ date('Y') }}</span>
                        <a href="{{ route('home') }}" class="font-medium text-slate-200 transition hover:text-white">Kembali ke landing page</a>
                    </div>
                </div>

                <div class="flex min-h-[720px] items-center bg-white px-5 py-6 sm:px-8 lg:px-10">
                    <div class="mx-auto w-full max-w-xl">
                        @if($showGuestBackButton)
                            <div class="mb-6">
                                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : $guestBackUrl }}"
                                   class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900">
                                    <i class="bi bi-arrow-left"></i>
                                    Kembali
                                </a>
                            </div>
                        @endif

                        <div class="mb-8 flex items-center justify-between lg:hidden">
                            <a href="/" class="text-slate-900 no-underline">
                                <x-brand-lockup theme="dark" size="sm" />
                            </a>
                        </div>

                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
