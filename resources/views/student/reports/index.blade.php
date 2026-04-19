@extends('layouts.app')

@section('title', 'Laporan Harian')
@section('page-title', 'Laporan Harian')
@section('page-subtitle', 'Rekap laporan selama magang')

@section('content')

@if(!$hasActiveInternship)
<div class="alert alert-info d-flex align-items-center" role="alert">
    <i class="bi bi-info-circle me-2"></i>
    <div>
        <strong>Anda belum memiliki magang aktif.</strong>
        <div class="mt-1" style="font-size:13px">Silakan pilih program magang terlebih dahulu untuk mulai membuat laporan harian.</div>
    </div>
</div>
<div class="text-center py-5">
    <div style="font-size:64px;margin-bottom:16px">📝</div>
    <h5 style="font-weight:700;color:#0f172a">Belum Ada Magang Aktif</h5>
    <p style="color:#64748b;font-size:14px">Anda perlu memiliki magang aktif untuk mengakses fitur laporan harian.</p>
    <a href="{{ route('student.applications.index') }}" class="btn btn-primary">
        <i class="bi bi-briefcase me-2"></i>Lihat Lowongan Magang
    </a>
</div>
@else

{{-- ── Stat bar ────────────────────────────────── --}}
<div class="row g-3 mb-4">
    @php
        $statusCounts = [
            ['label'=>'Disetujui',  'count'=>$internship->dailyReports()->where('status','approved')->count(),  'bg'=>'#d1fae5','color'=>'#065f46','icon'=>'check-circle-fill'],
            ['label'=>'Menunggu',   'count'=>$internship->dailyReports()->where('status','submitted')->count(), 'bg'=>'#fef3c7','color'=>'#92400e','icon'=>'hourglass-split'],
            ['label'=>'Perlu Revisi','count'=>$internship->dailyReports()->where('status','revision')->count(), 'bg'=>'#fee2e2','color'=>'#991b1b','icon'=>'arrow-repeat'],
            ['label'=>'Draft',      'count'=>$internship->dailyReports()->where('status','draft')->count(),     'bg'=>'#f1f5f9','color'=>'#475569','icon'=>'file-earmark'],
        ];
    @endphp
    @foreach($statusCounts as $s)
    <div class="col-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background: {{ $s['bg'] }}; color: {{ $s['color'] }}">
                <i class="bi bi-{{ $s['icon'] }}"></i>
            </div>
            <div>
                <div class="stat-value">{{ $s['count'] }}</div>
                <div class="stat-label">{{ $s['label'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Toolbar ─────────────────────────────────── --}}
<div class="card mb-3">
    <div class="card-body d-flex flex-wrap gap-3 align-items-center">
        <form method="GET" action="{{ route('student.reports.index') }}" class="d-flex gap-2 flex-fill flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" style="max-width:220px;font-size:13.5px"
                   placeholder="Cari tanggal atau status…">
            <select name="status" class="form-select" style="max-width:160px;font-size:13.5px"
                    onchange="this.form.submit()">
                <option value="">Semua Status</option>
                @foreach(['draft'=>'Draft','submitted'=>'Menunggu','approved'=>'Disetujui','revision'=>'Perlu Revisi'] as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-secondary" style="font-size:13.5px">
                <i class="bi bi-search me-1"></i>Filter
            </button>
            @if(request('status'))
                <a href="{{ route('student.reports.index') }}" class="btn btn-outline-danger" style="font-size:13.5px">
                    <i class="bi bi-x"></i>
                </a>
            @endif
        </form>
        <div class="d-flex gap-2 ms-auto">
            <a href="{{ route('student.reports.export') }}" class="btn btn-success">
                📊 Export Excel <i class="bi bi-file-earmark-excel me-1"></i>
            </a>
            <a href="{{ route('student.reports.create') }}" class="btn btn-primary @if(!$hasActiveInternship) disabled @endif">
                <i class="bi bi-plus-lg me-2"></i>Buat Laporan
            </a>
        </div>
    </div>
</div>

{{-- ── Tabel laporan ───────────────────────────── --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kegiatan</th>
                        <th>Status</th>
                        <th>Feedback</th>
                        <th style="width:100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td style="white-space:nowrap;font-size:13px;font-weight:600">
                            {{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}
                            <div style="font-size:11px;color:#94a3b8;font-weight:400">
                                {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('l') }}
                            </div>
                        </td>
                        <td style="font-size:13px">
                            {{ Str::limit($report->activity, 60) }}
                        </td>
                        <td>
                            @php
                                $map = [
                                    'draft'     => ['Draft',         'secondary'],
                                    'submitted' => ['Menunggu',      'warning'],
                                    'approved'  => ['Disetujui',     'success'],
                                    'revision'  => ['Perlu Revisi',  'danger'],
                                ];
                                [$lbl,$clr] = $map[$report->status] ?? [$report->status,'secondary'];
                            @endphp
                            <span class="badge-status bg-{{ $clr }}-subtle text-{{ $clr }}-emphasis">
                                {{ $lbl }}
                            </span>
                        </td>
                        <td style="font-size:12.5px;color:#64748b">
                            @if($report->feedback)
                                <span title="{{ $report->feedback }}">
                                    {{ Str::limit($report->feedback, 40) }}
                                </span>
                            @else
                                <span style="color:#cbd5e1">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('student.reports.show', $report) }}"
                                   class="btn btn-sm btn-outline-secondary py-1" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(in_array($report->status, ['draft','revision']))
                                    <a href="{{ route('student.reports.edit', $report) }}"
                                       class="btn btn-sm btn-outline-primary py-1" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                                @if($report->status === 'draft')
                                    <form method="POST" action="{{ route('student.reports.destroy', $report) }}"
                                          onsubmit="return confirm('Hapus draft laporan ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-1" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5" style="color:#94a3b8">
                            <i class="bi bi-journal-x" style="font-size:36px;display:block;margin-bottom:8px"></i>
                            Belum ada laporan harian.
                            <div class="mt-2">
                                <a href="{{ route('student.reports.create') }}" class="btn btn-primary btn-sm">
                                    Buat Laporan Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reports->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2" style="padding:12px 20px">
        <div style="font-size:13px;color:#94a3b8">
            Menampilkan {{ $reports->firstItem() }}–{{ $reports->lastItem() }} dari {{ $reports->total() }} laporan
        </div>
        {{ $reports->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endif

@endsection
