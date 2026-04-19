<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - InternHub</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('internhub-favicon.svg') }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Compiled Assets --}}
    @vite(['resources/css/app.css', 'resources/css/responsive.css'])

    <style>
        :root {
            --primary:       #1a56db;
            --primary-dark:  #1340a8;
            --primary-light: #e8f0fe;
            --accent:        #f59e0b;
            --success:       #10b981;
            --danger:        #ef4444;
            --warning:       #f59e0b;
            --sidebar-w:     260px;
            --sidebar-bg:    #0f172a;
            --sidebar-text:  #94a3b8;
            --sidebar-hover: #1e293b;
            --sidebar-active:#1a56db;
            --topbar-h:      64px;
            --body-bg:       #f1f5f9;
            --card-bg:       #ffffff;
            --font-main:     'Plus Jakarta Sans', sans-serif;
            --font-serif:    'Instrument Serif', serif;
        }

        * { box-sizing: border-box; }

        body {
            font-family: var(--font-main);
            background: var(--body-bg);
            color: #1e293b;
            margin: 0;
            min-height: 100vh;
        }

        /* ── Sidebar ─────────────────────────────── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: transform .3s ease;
            overflow-y: auto;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 24px;
            border-bottom: 1px solid #1e293b;
            text-decoration: none;
        }

        .sidebar-brand .brand-icon {
            width: 36px; height: 36px;
            background: var(--primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            flex-shrink: 0;
        }

        .sidebar-brand .brand-icon svg {
            width: 22px;
            height: 22px;
        }

        .sidebar-brand .brand-name {
            font-size: 18px;
            font-weight: 700;
            color: #f8fafc;
            letter-spacing: -.3px;
        }

        .sidebar-brand .brand-name span {
            color: var(--accent);
        }

        .sidebar-user {
            padding: 16px 24px;
            border-bottom: 1px solid #1e293b;
        }

        .sidebar-user .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #f1f5f9;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user .user-role {
            font-size: 11px;
            color: var(--sidebar-text);
            margin-top: 2px;
        }

        .sidebar-user .role-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .3px;
            text-transform: uppercase;
        }

        .sidebar-nav { padding: 12px 0; flex: 1; }

        .nav-section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #475569;
            padding: 12px 24px 6px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 24px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all .15s ease;
        }

        .sidebar-link i {
            font-size: 16px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-link:hover {
            background: var(--sidebar-hover);
            color: #f1f5f9;
            border-left-color: #334155;
        }

        .sidebar-link.active {
            background: rgba(26, 86, 219, .15);
            color: #93c5fd;
            border-left-color: var(--primary);
        }

        .sidebar-link .badge-count {
            margin-left: auto;
            background: var(--primary);
            color: #fff;
            font-size: 10px;
            padding: 2px 7px;
            border-radius: 10px;
            font-weight: 600;
        }

        .sidebar-footer {
            padding: 16px 24px;
            border-top: 1px solid #1e293b;
        }

        /* ── Topbar ──────────────────────────────── */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: var(--card-bg);
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 16px;
            z-index: 1030;
        }

        .topbar-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            flex: 1;
        }

        .topbar-title-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 0;
        }

        .topbar-back-btn {
            min-width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 14px;
            color: #334155;
            text-decoration: none;
            transition: all .15s ease;
            flex-shrink: 0;
            font-size: 13px;
            font-weight: 600;
        }

        .topbar-back-btn:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        .topbar-back-btn span {
            display: none;
        }

        .topbar-title small {
            display: block;
            font-size: 12px;
            font-weight: 400;
            color: #94a3b8;
        }

        .topbar-actions { display: flex; align-items: center; gap: 8px; }

        .topbar-icon-btn {
            width: 38px; height: 38px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: transparent;
            display: flex; align-items: center; justify-content: center;
            color: #64748b;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: all .15s;
            position: relative;
        }

        .topbar-icon-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .notif-dot {
            position: absolute;
            top: 6px; right: 6px;
            width: 8px; height: 8px;
            background: var(--danger);
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .topbar-avatar {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 14px;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
        }

        .topbar-avatar img,
        .sidebar-user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: inherit;
        }

        .sidebar-user-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-user-avatar {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: rgba(255,255,255,.1);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 700;
            flex-shrink: 0;
            overflow: hidden;
        }

        /* ── Main content ────────────────────────── */
        .main-content {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            padding: 28px;
            min-height: calc(100vh - var(--topbar-h));
        }

        /* ── Cards ───────────────────────────────── */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,.04);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 16px 20px;
            font-weight: 600;
            font-size: 14px;
        }

        /* ── Stat cards ──────────────────────────── */
        .stat-card {
            background: var(--card-bg);
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: box-shadow .2s;
        }

        .stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.07); }

        .stat-icon {
            width: 50px; height: 50px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-value {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1;
        }

        .stat-label {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 4px;
        }

        /* ── Alert / flash ───────────────────────── */
        .flash-message {
            border-radius: 10px;
            font-size: 14px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        /* ── Table ───────────────────────────────── */
        .table th {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: #94a3b8;
            border-bottom: 1px solid #f1f5f9;
            padding: 10px 14px;
        }

        .table td {
            font-size: 13.5px;
            padding: 12px 14px;
            vertical-align: middle;
            border-bottom: 1px solid #f8fafc;
        }

        .table tr:last-child td { border-bottom: none; }

        /* ── Badges ──────────────────────────────── */
        .badge-status {
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
        }

        /* ── Buttons ─────────────────────────────── */
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            font-weight: 600;
            font-size: 13.5px;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
            font-weight: 600;
            font-size: 13.5px;
            border-radius: 8px;
        }

        /* ── Responsive ──────────────────────────── */
        @media (max-width: 991px) {
            :root {
                --sidebar-w: 280px;
            }

            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-w);
            }
            .sidebar.show { transform: translateX(0); }
            .topbar { left: 0; }
            .main-content { margin-left: 0; }

            .sidebar-overlay {
                display: none;
                position: fixed; inset: 0;
                background: rgba(0,0,0,.5);
                z-index: 1039;
            }
            .sidebar-overlay.show { display: block; }

            /* Mobile topbar adjustments */
            .topbar {
                padding: 0 16px;
                height: 56px;
                --topbar-h: 56px;
            }

            .topbar-title {
                font-size: 14px;
            }

            .topbar-title small {
                font-size: 11px;
            }

            .topbar-icon-btn {
                width: 36px;
                height: 36px;
                font-size: 15px;
            }

            .topbar-avatar {
                width: 36px;
                height: 36px;
                font-size: 13px;
            }

            /* Mobile main content */
            .main-content {
                padding: 16px;
                margin-top: 56px;
            }

            /* Mobile cards */
            .stat-card {
                padding: 16px;
                gap: 12px;
            }

            .stat-icon {
                width: 44px;
                height: 44px;
                font-size: 20px;
            }

            .stat-value {
                font-size: 22px;
            }

            .stat-label {
                font-size: 11px;
            }
        }

        @media (max-width: 767px) {
            .main-content {
                padding: 12px;
            }

            .card {
                border-radius: 12px;
            }

            .card-header {
                padding: 14px 16px;
                font-size: 13px;
            }

            .card-body {
                padding: 16px !important;
            }

            /* Mobile table */
            .table-responsive {
                border-radius: 8px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table th,
            .table td {
                padding: 8px 12px;
                font-size: 12px;
                white-space: nowrap;
            }

            /* Mobile buttons */
            .btn {
                padding: 8px 16px;
                font-size: 13px;
            }

            .btn-sm {
                padding: 6px 12px;
                font-size: 12px;
            }

            /* Mobile forms */
            .form-label {
                font-size: 13px !important;
                margin-bottom: 6px;
            }

            .form-control {
                font-size: 14px;
                padding: 10px 12px;
                border-radius: 8px;
            }

            /* Mobile badges */
            .badge {
                font-size: 10px;
                padding: 4px 8px;
            }

            /* Mobile alerts */
            .flash-message {
                font-size: 13px;
                padding: 10px 14px;
                margin-bottom: 16px;
            }
        }

        @media (max-width: 575px) {
            .main-content {
                padding: 8px;
            }

            .card-body {
                padding: 12px !important;
            }

            /* Stack form columns on very small screens */
            .row.g-3 > .col-md-6 {
                margin-bottom: 16px;
            }

            /* Mobile navigation */
            .sidebar-brand {
                padding: 16px 20px;
            }

            .sidebar-brand .brand-name {
                font-size: 16px;
            }

            .sidebar-link {
                padding: 12px 20px;
                font-size: 14px;
            }

            .sidebar-link i {
                font-size: 18px;
                width: 22px;
            }
        }

        @media (min-width: 768px) {
            .topbar-back-btn span {
                display: inline;
            }

            .main-content {
                padding: 24px;
            }
        }

        @media (min-width: 992px) {
            :root {
                --sidebar-w: 260px;
            }

            .sidebar {
                transform: none !important;
            }

            .topbar {
                left: var(--sidebar-w);
                --topbar-h: 64px;
                height: 64px;
            }

            .main-content {
                margin-left: var(--sidebar-w);
                margin-top: 64px;
                padding: 28px;
            }
        }

        @media (min-width: 1200px) {
            .main-content {
                padding: 32px;
            }

            .container-fluid {
                max-width: 1400px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

@php
    $currentRouteName = request()->route()?->getName();
    $backUrl = route('dashboard');
    $showBackButton = false;

    if ($currentRouteName) {
        $showBackButton =
            \Illuminate\Support\Str::endsWith($currentRouteName, ['.show', '.edit', '.create']) ||
            in_array($currentRouteName, ['profile.edit', 'register.complete'], true);

        $segments = explode('.', $currentRouteName);
        $lastSegment = end($segments);

        if (in_array($lastSegment, ['show', 'edit', 'create'], true) && count($segments) >= 2) {
            $indexRoute = implode('.', array_slice($segments, 0, -1)) . '.index';

            if (\Illuminate\Support\Facades\Route::has($indexRoute)) {
                $backUrl = route($indexRoute);
            } elseif (\Illuminate\Support\Facades\Route::has($segments[0] . '.dashboard')) {
                $backUrl = route($segments[0] . '.dashboard');
            }
        } elseif ($currentRouteName === 'profile.edit') {
            $backUrl = route('dashboard');
        } elseif ($currentRouteName === 'register.complete') {
            $backUrl = route('register');
        }
    }
@endphp

{{-- Sidebar overlay (mobile) --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

{{-- ── SIDEBAR ─────────────────────────────────── --}}
<aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="brand-icon"><x-application-logo class="h-5 w-5 text-white" /></div>
        <span class="brand-name">Intern<span>Hub</span></span>
    </a>

    {{-- User info --}}
    <div class="sidebar-user">
        <div class="sidebar-user-header">
            <div class="sidebar-user-avatar">
                @if(auth()->user()->profilePhotoUrl())
                    <img src="{{ auth()->user()->profilePhotoUrl() }}" alt="Foto profil {{ auth()->user()->name }}">
                @else
                    {{ auth()->user()->initials() }}
                @endif
            </div>
            <div class="min-w-0">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role mt-1">
                    @php
                        $roleLabels = [
                            'admin'      => ['label' => 'Administrator', 'bg' => '#fef3c7', 'color' => '#92400e'],
                            'student'    => ['label' => 'Siswa',         'bg' => '#dbeafe', 'color' => '#1e40af'],
                            'school'     => ['label' => 'Sekolah',       'bg' => '#d1fae5', 'color' => '#065f46'],
                            'supervisor' => ['label' => 'Pembimbing',    'bg' => '#ede9fe', 'color' => '#4c1d95'],
                            'company'    => ['label' => 'Perusahaan',    'bg' => '#fee2e2', 'color' => '#991b1b'],
                        ];
                        $role = $roleLabels[auth()->user()->role] ?? ['label' => auth()->user()->role, 'bg' => '#f1f5f9', 'color' => '#475569'];
                    @endphp
                    <span class="role-badge" data-bg="{{ $role['bg'] }}" data-color="{{ $role['color'] }}">
                        {{ $role['label'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">
        @include('layouts.partials.sidebar-nav')
    </nav>

    {{-- Footer --}}
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link w-100 border-0 bg-transparent text-start" style="color:#ef4444">
                <i class="bi bi-box-arrow-left"></i> Keluar
            </button>
        </form>
    </div>
</aside>

{{-- ── TOPBAR ──────────────────────────────────── --}}
<header class="topbar">
    {{-- Mobile toggle --}}
    <button class="topbar-icon-btn d-lg-none" onclick="openSidebar()">
        <i class="bi bi-list"></i>
    </button>

    <div class="topbar-title-wrap">
        <!-- @if($showBackButton)
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : $backUrl }}" class="topbar-back-btn" title="Kembali">
                <i class="bi bi-arrow-left"></i>
                <span>Kembalil</span>
            </a>
        @endif -->

        <div class="topbar-title">
            @yield('page-title', 'Dashboard')
            @hasSection('page-subtitle')
                <small>@yield('page-subtitle')</small>
            @endif
        </div>
    </div>

    <div class="topbar-actions">
        {{-- Notifikasi --}}
        <a href="#" class="topbar-icon-btn" title="Notifikasi">
            <i class="bi bi-bell"></i>
            @php $unread = auth()->user()->notifications()->where('is_read', false)->count(); @endphp
            @if($unread > 0)
                <span class="notif-dot"></span>
            @endif
        </a>

        {{-- Profil dropdown --}}
        <div class="dropdown">
            <div class="topbar-avatar" data-bs-toggle="dropdown" title="{{ auth()->user()->name }}">
                @if(auth()->user()->profilePhotoUrl())
                    <img src="{{ auth()->user()->profilePhotoUrl() }}" alt="Foto profil {{ auth()->user()->name }}">
                @else
                    {{ auth()->user()->initials() }}
                @endif
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border" style="border-radius:12px;min-width:200px;margin-top:8px">
                <li class="px-3 py-2">
                    <div style="font-size:13px;font-weight:600;color:#0f172a">{{ auth()->user()->name }}</div>
                    <div style="font-size:12px;color:#94a3b8">{{ auth()->user()->email }}</div>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item" style="font-size:13.5px">
                        <i class="bi bi-person-circle me-2"></i>Profil Saya
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger" style="font-size:13.5px">
                            <i class="bi bi-box-arrow-left me-2"></i>Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

{{-- ── MAIN CONTENT ─────────────────────────────── --}}
<main class="main-content">

    {{-- Flash messages --}}
    @foreach(['success' => 'success', 'error' => 'danger', 'info' => 'info', 'warning' => 'warning'] as $key => $type)
        @if(session($key))
            <div class="alert alert-{{ $type }} alert-dismissible flash-message" role="alert">
                <i class="bi bi-{{ $type === 'success' ? 'check-circle' : ($type === 'danger' ? 'x-circle' : 'info-circle') }}-fill me-2"></i>
                {{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    @yield('content')
</main>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function openSidebar() {
        document.getElementById('sidebar').classList.add('show');
        document.getElementById('sidebarOverlay').classList.add('show');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('show');
        document.getElementById('sidebarOverlay').classList.remove('show');
    }

    // Apply role badge styles
    document.querySelectorAll('.role-badge').forEach(el => {
        el.style.background = el.dataset.bg;
        el.style.color = el.dataset.color;
    });

    // Auto-dismiss flash setelah 4 detik
    document.querySelectorAll('.flash-message').forEach(el => {
        setTimeout(() => {
            const alert = bootstrap.Alert.getOrCreateInstance(el);
            alert.close();
        }, 4000);
    });
</script>

@stack('scripts')
</body>
</html>
