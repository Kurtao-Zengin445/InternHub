@extends('layouts.app')

@section('title', 'Dashboard Pembimbing')
@section('page-title', 'Dashboard Pembimbing')
@section('page-subtitle', 'Halo, ' . auth()->user()->name)

@section('content')

{{-- Stat cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#eff6ff;color:#1a56db">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['active_internships'] }}</div>
                <div class="stat-label">Siswa Dibimbing</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#fef3c7;color:#92400e">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['pending_reports'] }}</div>
                <div class="stat-label">Laporan Pending</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#d1fae5;color:#065f46">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_students'] }}</div>
                <div class="stat-label">Total Siswa</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#ede9fe;color:#4c1d95">
                <i class="bi bi-patch-check-fill"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['evaluated'] }}</div>
                <div class="stat-label">Sudah Dinilai</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Laporan pending --}}
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-journal-check text-warning me-2"></i>Laporan Menunggu Verifikasi</span>
                <a href="{{ route('supervisor.reports.index') }}?status=submitted" class="btn btn-sm btn-outline-warning">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                @forelse($pendingReports as $report)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div class="avatar-sm" style="background:#eff6ff;color:#1a56db;width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0">
                        {{ strtoupper(substr($report->internship->application->student->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-fill">
                        <div style="font-size:13.5px;font-weight:600;color:#0f172a">
                            {{ $report->internship->application->student->user->name }}
                        </div>
                        <div style="font-size:12px;color:#94a3b8">
                            {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('l, d F Y') }} &bull;
                            {{ Str::limit($report->activity, 40) }}
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('supervisor.reports.show', $report) }}"
                           class="btn btn-sm btn-outline-secondary py-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        <form method="POST" action="{{ route('supervisor.reports.approve', $report) }}">
                            @csrf
                            <button class="btn btn-sm btn-success py-1" title="Setujui">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-5" style="color:#94a3b8">
                    <i class="bi bi-check-circle" style="font-size:36px;display:block;margin-bottom:8px;color:#d1fae5"></i>
                    <span style="font-size:14px">Semua laporan sudah diverifikasi! 🎉</span>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Siswa bimbingan --}}
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people-fill text-primary me-2"></i>Siswa Bimbingan</span>
                <a href="{{ route('supervisor.internships.index') }}" class="btn btn-sm btn-outline-primary">Semua</a>
            </div>
            <div class="card-body p-0">
                @forelse($internships as $internship)
                <a href="{{ route('supervisor.internships.show', $internship) }}"
                   class="d-flex align-items-center gap-3 px-4 py-3 border-bottom text-decoration-none"
                   style="transition:background .15s"
                   onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <div style="width:36px;height:36px;border-radius:9px;background:#eff6ff;color:#1a56db;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0">
                        {{ strtoupper(substr($internship->application->student->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-fill">
                        <div style="font-size:13px;font-weight:600;color:#0f172a">
                            {{ $internship->application->student->user->name }}
                        </div>
                        <div style="font-size:11.5px;color:#94a3b8">
                            {{ Str::limit($internship->application->program->company->name, 24) }}
                        </div>
                    </div>
                    <i class="bi bi-chevron-right" style="color:#cbd5e1;font-size:12px"></i>
                </a>
                @empty
                <div class="text-center py-4" style="color:#94a3b8;font-size:13px">
                    Belum ada siswa bimbingan.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection