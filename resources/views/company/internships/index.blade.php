@extends('layouts.app')

@section('title', 'Peserta Magang')
@section('page-title', 'Peserta Magang')
@section('page-subtitle', 'Pantau progress peserta magang di perusahaan')

@section('content')

{{-- Toolbar --}}
<div class="card mb-4">
    <div class="card-body d-flex flex-wrap gap-3 align-items-center">
        <form method="GET" class="d-flex gap-2 flex-fill flex-wrap">
            <select name="status" class="form-select" style="max-width:160px;font-size:13.5px"
                    onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="active"    {{ request('status')==='active'    ? 'selected':'' }}>Aktif</option>
                <option value="completed" {{ request('status')==='completed' ? 'selected':'' }}>Selesai</option>
                <option value="terminated"{{ request('status')==='terminated'? 'selected':'' }}>Dihentikan</option>
            </select>
            @if(request('status'))
                <a href="{{ route('company.internships.index') }}" class="btn btn-outline-danger" style="font-size:13.5px">
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
                        <th>Peserta</th>
                        <th>Program</th>
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
                        $studentUser = $internship->application->student;
                        $student = $internship->application->studentModel;
                        $pct     = $internship->attendancePercentage();
                        $totalReports    = $internship->dailyReports()->count();
                        $approvedReports = $internship->dailyReports()->where('status','approved')->count();
                    @endphp
                    <tr>
                        <td style="color:#94a3b8;font-size:12px">
                            {{ $internships->firstItem() + $loop->index }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;border-radius:9px;background:#ede9fe;color:#4c1d95;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0">
                                    {{ strtoupper(substr($studentUser->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-size:13.5px;font-weight:600;color:#0f172a">
                                        {{ $studentUser->name ?? 'Nama Tidak Tersedia' }}
                                    </div>
                                    <div style="font-size:11.5px;color:#94a3b8">
                                        {{ $student?->school->name ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:13px">
                            {{ Str::limit($internship->application->program->title, 30) }}
                        </td>
                        <td style="font-size:12.5px;color:#64748b;white-space:nowrap">
                            {{ \Carbon\Carbon::parse($internship->start_date)->format('d M Y') }}<br>
                            {{ \Carbon\Carbon::parse($internship->end_date)->format('d M Y') }}
                        </td>
                        <td class="text-center">
                            <div style="font-size:13px;font-weight:700;color: {{ $pct >= 80 ? '#10b981' : ($pct >= 60 ? '#f59e0b' : '#ef4444') }}">
                                {{ $pct }}%
                            </div>
                            <div style="width:60px;height:5px;border-radius:3px;background:#f1f5f9;margin:4px auto 0;overflow:hidden">
                                <div style="height:100%;background: {{ $pct >= 80 ? '#10b981' : ($pct >= 60 ? '#f59e0b' : '#ef4444') }}; width: {{ $pct }}%"></div>
                            </div>
                        </td>
                        <td class="text-center" style="font-size:13px">
                            <span style="font-weight:700;color:#10b981">{{ $approvedReports }}</span>
                            <span style="color:#94a3b8"> / {{ $totalReports }}</span>
                        </td>
                        <td>
                            @php
                                $smap = ['active'=>['Aktif','success'],'completed'=>['Selesai','primary'],'terminated'=>['Dihentikan','danger']];
                                [$sl,$sc] = $smap[$internship->status] ?? [$internship->status,'secondary'];
                            @endphp
                            <span class="badge-status bg-{{ $sc }}-subtle text-{{ $sc }}-emphasis">
                                {{ $sl }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('company.internships.show', $internship) }}"
                               class="btn btn-sm btn-outline-secondary py-1" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5" style="color:#94a3b8">
                            <i class="bi bi-person-workspace" style="font-size:36px;display:block;margin-bottom:8px"></i>
                            Belum ada peserta magang.
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
            dari {{ $internships->total() }} peserta
        </div>
        {{ $internships->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
