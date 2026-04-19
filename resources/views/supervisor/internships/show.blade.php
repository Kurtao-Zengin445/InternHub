@extends('layouts.app')

@section('title', 'Detail Siswa Bimbingan')
@section('page-title', 'Detail Siswa Bimbingan')
@section('page-subtitle', $internship->application->student->user->name . ' — ' . $internship->application->program->company->name)

@section('content')

@php
    $student         = $internship->application->student;
    $program         = $internship->application->program;
    $pct             = $internship->attendancePercentage();
    $totalReports    = $internship->dailyReports->count();
    $approvedReports = $internship->dailyReports->where('status', 'approved')->count();
    $pendingReports  = $internship->dailyReports->where('status', 'submitted')->count();
    $daysRemaining   = now()->diffInDays($internship->end_date, false);
@endphp

<div class="row g-3">

{{-- Kolom kiri: info siswa --}}
<div class="col-xl-4 d-flex flex-column gap-3">

    {{-- Profil --}}
    <div class="card">
        <div class="card-body text-center" style="padding:28px">
            <div style="width:64px;height:64px;border-radius:16px;background:#eff6ff;color:#1a56db;font-size:26px;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                {{ strtoupper(substr($student->user->name, 0, 1)) }}
            </div>
            <div style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:4px">
                {{ $student->user->name }}
            </div>
            <div style="font-size:13px;color:#64748b;margin-bottom:14px">
                {{ $student->nis }} · {{ $student->class }}
            </div>
            <div class="d-flex flex-column gap-2 text-start" style="font-size:13px">
                <div class="d-flex justify-content-between">
                    <span style="color:#94a3b8">Jurusan</span>
                    <span style="font-weight:500;text-align:right;max-width:160px">{{ $student->major ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span style="color:#94a3b8">Sekolah</span>
                    <span style="font-weight:500;text-align:right;max-width:160px">{{ $student->school->name }}</span>
                </div>
                @if($student->phone)
                <div class="d-flex justify-content-between">
                    <span style="color:#94a3b8">Telepon</span>
                    <span style="font-weight:500">{{ $student->phone }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Info magang --}}
    <div class="card">
        <div class="card-header"><i class="bi bi-briefcase-fill text-warning me-2"></i>Info Magang</div>
        <div class="card-body" style="font-size:13px;padding:16px 20px">
            <div style="font-weight:600;color:#0f172a;margin-bottom:6px">
                {{ $program->company->name }}
            </div>
            <div style="color:#64748b;margin-bottom:12px;font-size:12.5px">
                {{ $program->title }}
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span style="color:#94a3b8">Mulai</span>
                <span style="font-weight:500">{{ \Carbon\Carbon::parse($internship->start_date)->format('d M Y') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span style="color:#94a3b8">Selesai</span>
                <span style="font-weight:500">{{ \Carbon\Carbon::parse($internship->end_date)->format('d M Y') }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span style="color:#94a3b8">Status</span>
                @php $smap=['active'=>['Aktif','success'],'completed'=>['Selesai','primary'],'terminated'=>['Dihentikan','danger']]; [$sl,$sc]=$smap[$internship->status]??[$internship->status,'secondary']; @endphp
                <span class="badge-status bg-{{ $sc }}-subtle text-{{ $sc }}-emphasis">{{ $sl }}</span>
            </div>
            @if($internship->status === 'active' && $daysRemaining > 0)
            <div class="mt-3 p-2 rounded-2 text-center" style="background:#eff6ff;font-size:12px;color:#1e40af">
                <strong>{{ $daysRemaining }}</strong> hari lagi selesai
            </div>
            @endif
        </div>
    </div>

    {{-- Aksi cepat --}}
    <div class="card">
        <div class="card-header"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Aksi Cepat</div>
        <div class="card-body d-flex flex-column gap-2" style="padding:16px 20px">
            @if($pendingReports > 0)
            <a href="{{ route('supervisor.reports.index') }}?internship_id={{ $internship->id }}&status=submitted"
               class="btn btn-warning">
                <i class="bi bi-journal-check me-2"></i>
                Verifikasi Laporan
                <span class="badge bg-dark ms-1">{{ $pendingReports }}</span>
            </a>
            @endif

            @php $eval = $internship->supervisorEvaluation(); @endphp
            @if($eval)
                <a href="{{ route('supervisor.evaluations.show', $eval) }}" class="btn btn-outline-primary">
                    <i class="bi bi-patch-check me-2"></i>Lihat Penilaian
                </a>
                <a href="{{ route('supervisor.evaluations.edit', $eval) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-pencil me-2"></i>Edit Penilaian
                </a>
            @else
                <a href="{{ route('supervisor.evaluations.create', $internship) }}" class="btn btn-primary">
                    <i class="bi bi-patch-check-fill me-2"></i>Beri Penilaian
                </a>
            @endif

            <a href="{{ route('supervisor.reports.index') }}?internship_id={{ $internship->id }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-journal-text me-2"></i>Semua Laporan
            </a>
        </div>
    </div>

</div>

{{-- Kolom kanan: statistik & data --}}
<div class="col-xl-8 d-flex flex-column gap-3">

    {{-- Stat cards --}}
    <div class="row g-3">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#d1fae5;color:#065f46">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $pct }}%</div>
                    <div class="stat-label">Kehadiran</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#eff6ff;color:#1a56db">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $approvedReports }}</div>
                    <div class="stat-label">Laporan OK</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fef3c7;color:#92400e">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $pendingReports }}</div>
                    <div class="stat-label">Menunggu</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fee2e2;color:#991b1b">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $internship->dailyReports->where('status','revision')->count() }}</div>
                    <div class="stat-label">Perlu Revisi</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Laporan terbaru --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-journal-text text-primary me-2"></i>Laporan Harian Terbaru</span>
            <a href="{{ route('supervisor.reports.index') }}?internship_id={{ $internship->id }}"
               class="btn btn-sm btn-outline-primary">Lihat Semua</a>
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
                        @forelse($internship->dailyReports->sortByDesc('report_date')->take(7) as $report)
                        <tr>
                            <td style="font-size:13px;white-space:nowrap;font-weight:600">
                                {{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}
                            </td>
                            <td style="font-size:13px">{{ Str::limit($report->activity, 55) }}</td>
                            <td>
                                @php $rmap=['draft'=>['Draft','secondary'],'submitted'=>['Menunggu','warning'],'approved'=>['Disetujui','success'],'revision'=>['Revisi','danger']]; [$rl,$rc]=$rmap[$report->status]??[$report->status,'secondary']; @endphp
                                <span class="badge-status bg-{{ $rc }}-subtle text-{{ $rc }}-emphasis">{{ $rl }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('supervisor.reports.show', $report) }}"
                                       class="btn btn-sm btn-outline-secondary py-0">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($report->status === 'submitted')
                                    <form method="POST" action="{{ route('supervisor.reports.approve', $report) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success py-0" title="Setujui">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-3" style="color:#94a3b8;font-size:13px">
                                Belum ada laporan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Presensi terbaru --}}
    <div class="card">
        <div class="card-header"><i class="bi bi-calendar-check text-success me-2"></i>Presensi Terbaru</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Durasi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($internship->attendance->sortByDesc('attendance_date')->take(7) as $att)
                        <tr>
                            <td style="font-size:13px;white-space:nowrap;font-weight:600">
                                {{ \Carbon\Carbon::parse($att->attendance_date)->format('d M Y') }}
                            </td>
                            <td style="font-size:13px">
                                {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '—' }}
                            </td>
                            <td style="font-size:13px">
                                {{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '—' }}
                            </td>
                            <td style="font-size:13px;color:#64748b">{{ $att->duration() ?? '—' }}</td>
                            <td>
                                @php $amap=['present'=>['Hadir','success'],'sick'=>['Sakit','warning'],'permission'=>['Izin','info'],'absent'=>['Alpha','danger'],'holiday'=>['Libur','secondary']]; [$al,$ac]=$amap[$att->status]??[$att->status,'secondary']; @endphp
                                <span class="badge-status bg-{{ $ac }}-subtle text-{{ $ac }}-emphasis">{{ $al }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3" style="color:#94a3b8;font-size:13px">
                                Belum ada data presensi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div>
        <a href="{{ route('supervisor.internships.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
        </a>
    </div>

</div>
</div>

@endsection
