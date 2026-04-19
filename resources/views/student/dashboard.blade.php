@extends('layouts.app')

@section('title', 'Dashboard Siswa')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Halo, ' . auth()->user()->name . ' 👋')

@push('styles')
<style>
    .progress-ring {
        transform: rotate(-90deg);
    }

    .progress-ring-track {
        fill: none;
        stroke: #f1f5f9;
        stroke-width: 6;
    }

    .progress-ring-fill {
        fill: none;
        stroke: #1a56db;
        stroke-width: 6;
        stroke-linecap: round;
        transition: stroke-dashoffset .6s ease;
    }

    .program-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px;
        transition: all .2s;
    }

    .program-card:hover {
        border-color: #bfdbfe;
        box-shadow: 0 6px 20px rgba(26, 86, 219, .1);
        transform: translateY(-2px);
    }

    .timeline-item {
        position: relative;
        padding-left: 28px;
        padding-bottom: 20px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 20px;
        bottom: 0;
        width: 2px;
        background: #f1f5f9;
    }

    .timeline-item:last-child::before {
        display: none;
    }

    .timeline-dot {
        position: absolute;
        left: 0;
        top: 4px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px currentColor;
    }
</style>
@endpush

@section('content')

@if($internship)
{{-- ════ SEDANG MAGANG ════ --}}

{{-- Banner status magang --}}
<div class="alert border-0 mb-4 d-flex align-items-center gap-3"
    style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:14px;padding:20px 24px">
    <div style="width:48px;height:48px;background:#1a56db;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;flex-shrink:0">
        <i class="bi bi-person-workspace"></i>
    </div>
    <div class="flex-fill">
        <div style="font-size:15px;font-weight:700;color:#1e40af">
            {{ $internship->application->program->title }}
        </div>
        <div style="font-size:13px;color:#3b82f6">
            {{ $internship->application->program->company->name }} &bull;
            {{ \Carbon\Carbon::parse($internship->start_date)->format('d M Y') }} –
            {{ \Carbon\Carbon::parse($internship->end_date)->format('d M Y') }}
        </div>
    </div>
    @if($stats['days_remaining'] > 0)
    <div class="text-end">
        <div style="font-size:22px;font-weight:800;color:#1a56db;font-family:'Plus Jakarta Sans',sans-serif">
            {{ $stats['days_remaining'] }}
        </div>
        <div style="font-size:11px;color:#3b82f6">hari lagi</div>
    </div>
    @else
    <span class="badge bg-success px-3 py-2" style="font-size:12px">Selesai</span>
    @endif
</div>

{{-- Stat cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#d1fae5;color:#065f46">
                <i class="bi bi-journal-check"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['approved_reports'] }}</div>
                <div class="stat-label">Laporan Disetujui</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#fef3c7;color:#92400e">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['pending_reports'] }}</div>
                <div class="stat-label">Menunggu Verifikasi</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#eff6ff;color:#1a56db">
                <i class="bi bi-journal-text"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_reports'] }}</div>
                <div class="stat-label">Total Laporan</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#ede9fe;color:#4c1d95">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['attendance_pct'] }}%</div>
                <div class="stat-label">Kehadiran</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Laporan terbaru --}}
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-journal-text text-primary me-2"></i>Laporan Harian Terbaru</span>
                <a href="{{ route('student.reports.index') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kegiatan</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentReports as $report)
                            <tr>
                                <td style="font-size:13px;white-space:nowrap">
                                    {{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}
                                </td>
                                <td style="font-size:13px">{{ Str::limit($report->activity, 50) }}</td>
                                <td>
                                    @php
                                    $map = ['draft'=>['Draft','secondary'],'submitted'=>['Menunggu','warning'],'approved'=>['Disetujui','success'],'revision'=>['Revisi','danger']];
                                    [$lbl,$clr] = $map[$report->status] ?? [$report->status,'secondary'];
                                    @endphp
                                    <span class="badge-status bg-{{ $clr }}-subtle text-{{ $clr }}-emphasis">{{ $lbl }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('student.reports.show', $report) }}" class="btn btn-sm btn-outline-secondary py-0">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4" style="color:#94a3b8;font-size:13px">
                                    Belum ada laporan. <a href="{{ route('student.reports.create') }}">Buat sekarang</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Aksi cepat + info magang --}}
    <div class="col-xl-4 d-flex flex-column gap-3">

        {{-- Aksi hari ini --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Aksi Hari Ini</div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('student.attendance.today') }}" class="btn btn-primary w-100">
                    <i class="bi bi-calendar-check me-2"></i>Presensi Sekarang
                </a>
                <a href="{{ route('student.reports.create') }}" class="btn btn-outline-primary w-100">
                    <i class="bi bi-pencil-square me-2"></i>Buat Laporan Harian
                </a>
                <a href="{{ route('student.internship.documents') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-folder-plus me-2"></i>Upload Dokumen
                </a>
            </div>
        </div>

        {{-- Info pembimbing --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-person-badge-fill text-success me-2"></i>Pembimbing</div>
            <div class="card-body">
                @if($internship->supervisor)
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="avatar-sm" style="background:#d1fae5;color:#065f46;width:40px;height:40px;font-size:15px">
                        {{ strtoupper(substr($internship->supervisor->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:600">{{ $internship->supervisor->user->name }}</div>
                        <div style="font-size:11.5px;color:#94a3b8">{{ $internship->supervisor->position }}</div>
                        <div style="font-size:11.5px;color:#94a3b8">{{ $internship->supervisor->school->name }}</div>
                    </div>
                </div>
                @endif
                @if($internship->companySupervisor)
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-sm" style="background:#fef3c7;color:#92400e;width:40px;height:40px;font-size:15px">
                        {{ strtoupper(substr($internship->companySupervisor->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:600">{{ $internship->companySupervisor->name }}</div>
                        <div style="font-size:11.5px;color:#94a3b8">Pembimbing Perusahaan</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

@else
{{-- ════ BELUM MAGANG ════ --}}

{{-- Banner ajakan --}}
<div class="mb-4 p-4 rounded-3 border text-center"
    style="background:linear-gradient(135deg,#f8fafc,#eff6ff);border-color:#dbeafe !important">
    <div style="font-size:48px;margin-bottom:12px">🚀</div>
    <h5 style="font-weight:700;color:#0f172a">Belum ada magang aktif</h5>
    <p style="color:#64748b;font-size:14px;margin-bottom:20px">
        Temukan program magang yang sesuai dengan bidang keahlianmu dan ajukan lamaran sekarang!
    </p>
    <a href="{{ route('student.applications.create') }}" class="btn btn-primary px-4">
        <i class="bi bi-send-fill me-2"></i>Cari & Lamar Program Magang
    </a>
</div>

{{-- Program tersedia --}}
@if($availablePrograms && $availablePrograms->count())
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-megaphone-fill text-warning me-2"></i>Program Magang Tersedia</span>
        <a href="{{ route('student.applications.create') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($availablePrograms as $program)
            <div class="col-md-6">
                <div class="program-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div style="font-size:14px;font-weight:700;color:#0f172a">
                            {{ Str::limit($program->title, 36) }}
                        </div>
                        <span class="badge bg-success-subtle text-success-emphasis ms-2" style="font-size:10px;white-space:nowrap">
                            {{ $program->remainingQuota() }} slot
                        </span>
                    </div>
                    <div style="font-size:12.5px;color:#64748b;margin-bottom:12px">
                        <i class="bi bi-building me-1"></i>{{ $program->company->name }}<br>
                        <i class="bi bi-calendar me-1"></i>
                        {{ \Carbon\Carbon::parse($program->start_date)->format('d M') }} –
                        {{ \Carbon\Carbon::parse($program->end_date)->format('d M Y') }}
                    </div>
                    <a href="{{ route('student.applications.create', ['program_id' => $program->id]) }}"
                        class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-send me-1"></i>Lamar Sekarang
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@endif

@endsection