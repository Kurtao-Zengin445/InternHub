@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name)

@push('styles')
<style>
.stat-card { transition: all .2s; }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.08) !important; }
.activity-item { padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
.activity-item:last-child { border-bottom: none; }
.avatar-sm {
    width: 34px; height: 34px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; flex-shrink: 0;
}
</style>
@endpush

@section('content')

{{-- ── Stat Cards ───────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#eff6ff;color:#1a56db">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_students'] }}</div>
                <div class="stat-label">Total Siswa</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#d1fae5;color:#065f46">
                <i class="bi bi-building"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_schools'] }}</div>
                <div class="stat-label">Sekolah Terdaftar</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#fef3c7;color:#92400e">
                <i class="bi bi-briefcase-fill"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_companies'] }}</div>
                <div class="stat-label">Perusahaan Mitra</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#ede9fe;color:#4c1d95">
                <i class="bi bi-person-workspace"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['active_internships'] }}</div>
                <div class="stat-label">Magang Aktif</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Row 2 ─────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    {{-- Lamaran terbaru --}}
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-inbox-fill text-primary me-2"></i>Lamaran Terbaru</span>
                <span class="badge bg-danger rounded-pill">{{ $stats['pending_applications'] }} pending</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Program</th>
                                <th>Perusahaan</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentApplications as $app)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm" style="background:#eff6ff;color:#1a56db">
                                            {{ strtoupper(substr($app->student->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-size:13px;font-weight:600">{{ $app->student->user->name }}</div>
                                            <div style="font-size:11px;color:#94a3b8">{{ $app->student->school->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size:13px">{{ Str::limit($app->program->title, 28) }}</td>
                                <td style="font-size:13px">{{ $app->program->company->name }}</td>
                                <td>
                                    @php
                                        $statusMap = [
                                            'pending'  => ['Menunggu', 'warning'],
                                            'accepted' => ['Diterima', 'success'],
                                            'rejected' => ['Ditolak',  'danger'],
                                            'reviewed' => ['Ditinjau', 'info'],
                                            'cancelled'=> ['Dibatalkan','secondary'],
                                        ];
                                        [$label, $color] = $statusMap[$app->status] ?? [$app->status, 'secondary'];
                                    @endphp
                                    <span class="badge-status bg-{{ $color }}-subtle text-{{ $color }}-emphasis">{{ $label }}</span>
                                </td>
                                <td style="font-size:12px;color:#94a3b8">
                                    {{ \Carbon\Carbon::parse($app->applied_at)->diffForHumans() }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4" style="color:#94a3b8;font-size:13px">
                                    Belum ada lamaran masuk.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar kanan --}}
    <div class="col-xl-4 d-flex flex-column gap-3">

        {{-- Program buka --}}
        <div class="card flex-fill">
            <div class="card-header">
                <i class="bi bi-megaphone-fill text-warning me-2"></i>Program Aktif
            </div>
            <div class="card-body p-3">
                @forelse($openPrograms as $program)
                <div class="activity-item d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#0f172a">{{ Str::limit($program->title, 30) }}</div>
                        <div style="font-size:11.5px;color:#94a3b8">{{ $program->company->name }}</div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill" style="font-size:10px;white-space:nowrap">
                        {{ $program->applications_count }} lamar
                    </span>
                </div>
                @empty
                <p class="text-center py-3" style="color:#94a3b8;font-size:13px">Tidak ada program aktif.</p>
                @endforelse
            </div>
        </div>

        {{-- User terbaru --}}
        <div class="card flex-fill">
            <div class="card-header">
                <i class="bi bi-person-plus-fill text-success me-2"></i>Pengguna Baru
            </div>
            <div class="card-body p-3">
                @foreach($recentUsers as $user)
                <div class="activity-item d-flex align-items-center gap-2">
                    <div class="avatar-sm" style="background:#f1f5f9;color:#475569">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="flex-fill">
                        <div style="font-size:12.5px;font-weight:600">{{ $user->name }}</div>
                        <div style="font-size:11px;color:#94a3b8">{{ $user->email }}</div>
                    </div>
                    @php
                        $roleColors = ['student'=>'primary','school'=>'success','supervisor'=>'purple','company'=>'danger'];
                        $roleLabels = ['student'=>'Siswa','school'=>'Sekolah','supervisor'=>'Pembimbing','company'=>'Perusahaan'];
                    @endphp
                    <span class="badge bg-secondary-subtle text-secondary-emphasis" style="font-size:10px">
                        {{ $roleLabels[$user->role] ?? $user->role }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

{{-- ── Quick links ───────────────────────────────── --}}
<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">Akses Cepat</div>
            <div class="card-body d-flex flex-wrap gap-2">
                <a href="{{ route('admin.users.create') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-person-plus me-1"></i>Tambah Pengguna
                </a>
                <a href="{{ route('admin.schools.create') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-building-add me-1"></i>Tambah Sekolah
                </a>
                <a href="{{ route('admin.supervisors.create') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-person-badge me-1"></i>Tambah Pembimbing
                </a>
                <a href="{{ route('admin.companies.create') }}" class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-briefcase me-1"></i>Tambah Perusahaan
                </a>
                <a href="{{ route('admin.users.index') }}?role=student" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-people me-1"></i>Daftar Siswa
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
