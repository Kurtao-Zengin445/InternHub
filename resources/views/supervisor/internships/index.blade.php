@extends('layouts.app')

@section('title', 'Siswa Bimbingan')
@section('page-title', 'Siswa Bimbingan')
@section('page-subtitle', 'Pantau progress seluruh siswa bimbingan Anda')

@section('content')

{{-- Stat ringkas --}}
@php
    $supervisor    = auth()->user()->supervisor;
    $allInternships = \App\Models\Internship::with(['application.student.user','application.program.company'])
        ->where('supervisor_id', $supervisor->id)->get();
    $activeCount    = $allInternships->where('status','active')->count();
    $completedCount = $allInternships->where('status','completed')->count();
    $pendingReports = \App\Models\DailyReport::whereIn('internship_id', $allInternships->pluck('id'))
        ->where('status','submitted')->count();
@endphp

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#d1fae5;color:#065f46">
                <i class="bi bi-person-workspace"></i>
            </div>
            <div>
                <div class="stat-value">{{ $activeCount }}</div>
                <div class="stat-label">Sedang Magang</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#fef3c7;color:#92400e">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="stat-value">{{ $pendingReports }}</div>
                <div class="stat-label">Laporan Pending</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#eff6ff;color:#1a56db">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="stat-value">{{ $allInternships->count() }}</div>
                <div class="stat-label">Total Siswa</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#d1fae5;color:#065f46">
                <i class="bi bi-patch-check-fill"></i>
            </div>
            <div>
                <div class="stat-value">{{ $completedCount }}</div>
                <div class="stat-label">Selesai Magang</div>
            </div>
        </div>
    </div>
</div>

{{-- Toolbar --}}
<div class="card mb-3">
    <div class="card-body d-flex flex-wrap gap-3 align-items-center">
        <form method="GET" class="d-flex gap-2 flex-fill flex-wrap">
            <select name="status" class="form-select" style="max-width:160px;font-size:13.5px"
                    onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="active"    {{ request('status')==='active'    ?'selected':'' }}>Aktif</option>
                <option value="completed" {{ request('status')==='completed' ?'selected':'' }}>Selesai</option>
                <option value="terminated"{{ request('status')==='terminated'?'selected':'' }}>Dihentikan</option>
            </select>
            @if(request('status'))
                <a href="{{ route('supervisor.internships.index') }}" class="btn btn-outline-danger" style="font-size:13.5px">
                    <i class="bi bi-x"></i>
                </a>
            @endif
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Siswa</th>
                        <th>Perusahaan</th>
                        <th>Periode</th>
                        <th class="text-center">Kehadiran</th>
                        <th class="text-center">Laporan</th>
                        <th>Status</th>
                        <th style="width:100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($internships as $internship)
                    @php
                        $student         = $internship->application->student;
                        $pct             = $internship->attendancePercentage();
                        $totalReports    = $internship->dailyReports()->count();
                        $approvedReports = $internship->dailyReports()->where('status','approved')->count();
                        $pendingCount    = $internship->dailyReports()->where('status','submitted')->count();
                    @endphp
                    <tr>
                        <td style="color:#94a3b8;font-size:12px">
                            {{ $internships->firstItem() + $loop->index }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;border-radius:9px;background:#eff6ff;color:#1a56db;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0">
                                    {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-size:13.5px;font-weight:600;color:#0f172a">
                                        {{ $student->user->name }}
                                    </div>
                                    <div style="font-size:11.5px;color:#94a3b8">
                                        {{ $student->nis }} · {{ $student->class }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:13px">
                            {{ Str::limit($internship->application->program->company->name, 26) }}
                            <div style="font-size:11.5px;color:#94a3b8">
                                {{ Str::limit($internship->application->program->title, 26) }}
                            </div>
                        </td>
                        <td style="font-size:12.5px;color:#64748b;white-space:nowrap">
                            {{ \Carbon\Carbon::parse($internship->start_date)->format('d M Y') }}<br>
                            {{ \Carbon\Carbon::parse($internship->end_date)->format('d M Y') }}
                        </td>
                        <td class="text-center">
                            <div style="font-size:13px;font-weight:700;color:{{ $pct >= 80 ? '#10b981' : ($pct >= 60 ? '#f59e0b' : '#ef4444') }}">
                                {{ $pct }}%
                            </div>
                            <div style="width:60px;height:5px;border-radius:3px;background:#f1f5f9;margin:4px auto 0;overflow:hidden">
                                <div style="height:100%;background:{{ $pct >= 80 ? '#10b981' : ($pct >= 60 ? '#f59e0b' : '#ef4444') }};width:{{ $pct }}%"></div>
                            </div>
                        </td>
                        <td class="text-center">
                            <div style="font-size:13px">
                                <span style="font-weight:700;color:#10b981">{{ $approvedReports }}</span>
                                <span style="color:#94a3b8"> / {{ $totalReports }}</span>
                            </div>
                            @if($pendingCount > 0)
                                <div class="mt-1">
                                    <span class="badge bg-warning-subtle text-warning-emphasis"
                                          style="font-size:10.5px">{{ $pendingCount }} pending</span>
                                </div>
                            @endif
                        </td>
                        <td>
                            @php
                                $smap=['active'=>['Aktif','success'],'completed'=>['Selesai','primary'],'terminated'=>['Dihentikan','danger']];
                                [$sl,$sc]=$smap[$internship->status]??[$internship->status,'secondary'];
                            @endphp
                            <span class="badge-status bg-{{ $sc }}-subtle text-{{ $sc }}-emphasis">
                                {{ $sl }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('supervisor.internships.show', $internship) }}"
                                   class="btn btn-sm btn-outline-secondary py-1" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($pendingCount > 0)
                                    <a href="{{ route('supervisor.reports.index') }}?internship_id={{ $internship->id }}&status=submitted"
                                       class="btn btn-sm btn-warning py-1" title="Laporan pending">
                                        <i class="bi bi-journal-check"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5" style="color:#94a3b8">
                            <i class="bi bi-people" style="font-size:36px;display:block;margin-bottom:8px"></i>
                            Belum ada siswa bimbingan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($internships->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2"
         style="padding:12px 20px">
        <div style="font-size:13px;color:#94a3b8">
            Menampilkan {{ $internships->firstItem() }}–{{ $internships->lastItem() }}
            dari {{ $internships->total() }} siswa
        </div>
        {{ $internships->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
