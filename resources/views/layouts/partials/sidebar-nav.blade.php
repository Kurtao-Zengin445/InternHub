@php $role = auth()->user()->role; @endphp

{{-- ── ADMIN ──────────────────────────────────── --}}
@if($role === 'admin')
    <span class="nav-section-label">Utama</span>
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>

    <span class="nav-section-label">Manajemen</span>
    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="bi bi-people-fill"></i> Pengguna
    </a>
    <a href="{{ route('admin.schools.index') }}" class="sidebar-link {{ request()->routeIs('admin.schools.*') ? 'active' : '' }}">
        <i class="bi bi-building"></i> Sekolah
    </a>
    <a href="{{ route('admin.supervisors.index') }}" class="sidebar-link {{ request()->routeIs('admin.supervisors.*') ? 'active' : '' }}">
        <i class="bi bi-person-badge-fill"></i> Pembimbing
    </a>
    <a href="{{ route('admin.companies.index') }}" class="sidebar-link {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}">
        <i class="bi bi-briefcase-fill"></i> Perusahaan
    </a>
@endif

{{-- ── SISWA ───────────────────────────────────── --}}
@if($role === 'student')
    <span class="nav-section-label">Utama</span>
    <a href="{{ route('student.dashboard') }}" class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>

    <span class="nav-section-label">Magang</span>
    <a href="{{ route('student.applications.index') }}" class="sidebar-link {{ request()->routeIs('student.applications.*') ? 'active' : '' }}">
        <i class="bi bi-send-fill"></i> Lamaran Saya
    </a>
    <a href="{{ route('student.internship.show') }}" class="sidebar-link {{ request()->routeIs('student.internship.show') ? 'active' : '' }}">
        <i class="bi bi-person-workspace"></i> Data Magang
    </a>

    <span class="nav-section-label">Aktivitas</span>
    <a href="{{ route('student.attendance.today') }}" class="sidebar-link {{ request()->routeIs('student.attendance.*') ? 'active' : '' }}">
        <i class="bi bi-calendar-check-fill"></i> Presensi
    </a>
    <a href="{{ route('student.reports.index') }}" class="sidebar-link {{ request()->routeIs('student.reports.*') ? 'active' : '' }}">
        @php $pending = 0; @endphp
        <i class="bi bi-journal-text"></i> Laporan Harian
    </a>

    <span class="nav-section-label">Dokumen</span>
    <a href="{{ route('student.internship.documents') }}" class="sidebar-link {{ request()->routeIs('student.internship.documents') ? 'active' : '' }}">
        <i class="bi bi-folder-fill"></i> Dokumen
    </a>
    <a href="{{ route('student.internship.evaluation') }}" class="sidebar-link {{ request()->routeIs('student.internship.evaluation') ? 'active' : '' }}">
        <i class="bi bi-patch-check-fill"></i> Penilaian
    </a>
@endif

{{-- ── SEKOLAH ─────────────────────────────────── --}}
@if($role === 'school')
    <span class="nav-section-label">Utama</span>
    <a href="{{ route('school.dashboard') }}" class="sidebar-link {{ request()->routeIs('school.dashboard') ? 'active' : '' }}">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>

    <span class="nav-section-label">Manajemen</span>
    <a href="{{ route('school.students.index') }}" class="sidebar-link {{ request()->routeIs('school.students.*') ? 'active' : '' }}">
        <i class="bi bi-people-fill"></i> Siswa
    </a>
    <a href="{{ route('school.applications.index') }}" class="sidebar-link {{ request()->routeIs('school.applications.*') ? 'active' : '' }}">
        <i class="bi bi-send-fill"></i> Lamaran
    </a>
    <a href="{{ route('school.supervisors.index') }}" class="sidebar-link {{ request()->routeIs('school.supervisors.*') ? 'active' : '' }}">
        <i class="bi bi-person-badge-fill"></i> Pembimbing
    </a>
    <a href="{{ route('school.evaluations.index') }}" class="sidebar-link {{ request()->routeIs('school.evaluations.*') ? 'active' : '' }}">
        <i class="bi bi-patch-check-fill"></i> Penilaian Siswa
    </a>
@endif

{{-- ── PEMBIMBING ──────────────────────────────── --}}
@if($role === 'supervisor')
    <span class="nav-section-label">Utama</span>
    <a href="{{ route('supervisor.dashboard') }}" class="sidebar-link {{ request()->routeIs('supervisor.dashboard') ? 'active' : '' }}">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>

    <span class="nav-section-label">Bimbingan</span>
    <a href="{{ route('supervisor.internships.index') }}" class="sidebar-link {{ request()->routeIs('supervisor.internships.*') ? 'active' : '' }}">
        <i class="bi bi-people-fill"></i> Siswa Bimbingan
    </a>
    <a href="{{ route('supervisor.reports.index') }}" class="sidebar-link {{ request()->routeIs('supervisor.reports.*') ? 'active' : '' }}">
        <i class="bi bi-journal-check"></i> Verifikasi Laporan
        @php
            $pendingReports = \App\Models\DailyReport::whereHas('internship', fn($q) =>
                $q->where('supervisor_id', auth()->user()->supervisor->id ?? 0)
            )->where('status', 'submitted')->count();
        @endphp
        @if($pendingReports > 0)
            <span class="badge-count">{{ $pendingReports }}</span>
        @endif
    </a>
    <a href="{{ route('supervisor.documents.index') }}" class="sidebar-link {{ request()->routeIs('supervisor.documents.*') ? 'active' : '' }}">
        <i class="bi bi-folder-check"></i> Verifikasi Dokumen
    </a>
    <a href="{{ route('supervisor.evaluations.index') }}" class="sidebar-link {{ request()->routeIs('supervisor.evaluations.*') ? 'active' : '' }}">
        <i class="bi bi-patch-check-fill"></i> Penilaian
    </a>
@endif

{{-- ── PERUSAHAAN ──────────────────────────────── --}}
@if($role === 'company')
    <span class="nav-section-label">Utama</span>
    <a href="{{ route('company.dashboard') }}" class="sidebar-link {{ request()->routeIs('company.dashboard') ? 'active' : '' }}">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>

    <span class="nav-section-label">Program Magang</span>
    <a href="{{ route('company.programs.index') }}" class="sidebar-link {{ request()->routeIs('company.programs.*') ? 'active' : '' }}">
        <i class="bi bi-briefcase-fill"></i> Program Magang
    </a>
    <a href="{{ route('company.applications.index') }}" class="sidebar-link {{ request()->routeIs('company.applications.*') ? 'active' : '' }}">
        <i class="bi bi-inbox-fill"></i> Lamaran Masuk
        @php
            $pendingApps = \App\Models\Application::whereHas('program', fn($q) =>
                $q->where('company_id', auth()->user()->company->id ?? 0)
            )->where('status', 'pending')->count();
        @endphp
        @if($pendingApps > 0)
            <span class="badge-count">{{ $pendingApps }}</span>
        @endif
    </a>

    <span class="nav-section-label">Peserta</span>
    <a href="{{ route('company.internships.index') }}" class="sidebar-link {{ request()->routeIs('company.internships.*') ? 'active' : '' }}">
        <i class="bi bi-person-workspace"></i> Peserta Magang
    </a>
    <a href="{{ route('company.evaluations.index') }}" class="sidebar-link {{ request()->routeIs('company.evaluations.*') ? 'active' : '' }}">
        <i class="bi bi-patch-check-fill"></i> Penilaian
    </a>
@endif

<span class="nav-section-label">Akun</span>
<a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
    <i class="bi bi-person-circle"></i> Profil Saya
</a>
