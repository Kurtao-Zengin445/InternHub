@extends('layouts.app')

@section('title', 'Detail Laporan')
@section('page-title', 'Detail Laporan Harian')
@section('page-subtitle', $report->internship->application->student->user->name . ' · ' . \Carbon\Carbon::parse($report->report_date)->translatedFormat('l, d F Y'))

@section('content')

<div class="row g-3">
<div class="col-xl-8">

    {{-- Status banner --}}
    @php
        $statusConfig = [
            'draft'     => ['Draft',              'secondary', '#f1f5f9', '#475569', 'file-earmark'],
            'submitted' => ['Menunggu Verifikasi', 'warning',   '#fffbeb', '#92400e', 'hourglass-split'],
            'approved'  => ['Disetujui',           'success',   '#f0fdf4', '#065f46', 'check-circle-fill'],
            'revision'  => ['Perlu Direvisi',      'danger',    '#fef2f2', '#991b1b', 'arrow-repeat'],
        ];
        [$slabel, $scolor, $sbg, $stxt, $sicon] = $statusConfig[$report->status] ?? [$report->status,'secondary','#f8fafc','#475569','question'];
    @endphp

    <div class="card mb-3" style="background: {{ $sbg }}; border-color:transparent">
        <div class="card-body d-flex align-items-center gap-3" style="padding:18px 24px">
            <i class="bi bi-{{ $sicon }}" style="font-size:28px;color: {{ $stxt }}; flex-shrink:0"></i>
            <div>
                <div style="font-size:15px;font-weight:700; color: {{ $stxt }}">{{ $slabel }}</div>
                <div style="font-size:12.5px;color: {{ $stxt }}; opacity:.7; margin-top:1px">
                    Siswa: <strong>{{ $report->internship->application->student->user->name }}</strong>
                    @if($report->submitted_at)
                        · Dikirim {{ $report->submitted_at->diffForHumans() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Feedback yang sudah diberikan --}}
    @if($report->feedback && in_array($report->status, ['approved','revision']))
    <div class="card mb-3" style="border-color: {{ $report->status === 'approved' ? '#d1fae5' : '#fecaca' }};background: {{ $report->status === 'approved' ? '#f0fdf4' : '#fef2f2' }}">
        <div class="card-body" style="padding:18px 22px">
            <div style="font-size:13px;font-weight:700;color: {{ $report->status === 'approved' ? '#065f46' : '#991b1b' }}; margin-bottom:8px">
                <i class="bi bi-chat-left-text-fill me-2"></i>Catatan Anda
            </div>
            <div style="font-size:13.5px;line-height:1.65;color: {{ $report->status === 'approved' ? '#064e3b' : '#7f1d1d' }}">
                {{ $report->feedback }}
            </div>
        </div>
    </div>
    @endif

    {{-- Isi laporan --}}
    <div class="card mb-3">
        <div class="card-body" style="padding:28px">

            <div class="mb-4">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-bottom:8px">
                    Kegiatan yang Dilakukan
                </div>
                <div style="font-size:14px;color:#1e293b;line-height:1.8;white-space:pre-wrap">{{ $report->activity }}</div>
            </div>

            @if($report->problems)
            <div class="mb-4 pt-4 border-top">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-bottom:8px">
                    Kendala yang Dihadapi
                </div>
                <div style="font-size:14px;color:#1e293b;line-height:1.8;white-space:pre-wrap">{{ $report->problems }}</div>
            </div>
            @endif

            @if($report->solutions)
            <div class="pt-4 border-top">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-bottom:8px">
                    Solusi yang Dilakukan
                </div>
                <div style="font-size:14px;color:#1e293b;line-height:1.8;white-space:pre-wrap">{{ $report->solutions }}</div>
            </div>
            @endif

        </div>
    </div>

    {{-- Foto dokumentasi --}}
    @if($report->photo)
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-image text-primary me-2"></i>Foto Dokumentasi</div>
        <div class="card-body" style="padding:16px">
            <a href="{{ asset('storage/'.$report->photo) }}" target="_blank" rel="noopener">
                <img src="{{ asset('storage/'.$report->photo) }}"
                     class="w-100 rounded-3" style="max-height:400px;object-fit:cover;cursor:zoom-in"
                     alt="Foto dokumentasi">
            </a>
        </div>
    </div>
    @endif

    {{-- ── Aksi verifikasi ────────────────────── --}}
    @if($report->status === 'submitted')
    <div class="card" style="border-color:#e2e8f0">
        <div class="card-header">
            <i class="bi bi-shield-check text-primary me-2"></i>Tindakan Verifikasi
        </div>
        <div class="card-body" style="padding:20px 24px">

            {{-- Approve --}}
            <div class="mb-4">
                <label class="form-label fw-semibold" style="font-size:13.5px">
                    Catatan Persetujuan <span style="font-weight:400;color:#94a3b8">(opsional)</span>
                </label>
                <form method="POST" action="{{ route('supervisor.reports.approve', $report) }}" id="approveForm">
                    @csrf
                    <textarea name="feedback" rows="3" class="form-control mb-3"
                              style="font-size:13.5px"
                              placeholder="Tuliskan apresiasi atau catatan positif untuk siswa…"></textarea>
                    <button type="submit" class="btn btn-success px-4"
                            onclick="return confirm('Setujui laporan ini?')">
                        <i class="bi bi-check-circle-fill me-2"></i>Setujui Laporan
                    </button>
                </form>
            </div>

            <div class="border-top pt-4">
                <label class="form-label fw-semibold" style="font-size:13.5px">
                    Minta Revisi <span class="text-danger">*</span>
                </label>
                <form method="POST" action="{{ route('supervisor.reports.revision', $report) }}" id="revisionForm">
                    @csrf
                    <textarea name="feedback" rows="3" class="form-control mb-3 @error('feedback') is-invalid @enderror"
                              style="font-size:13.5px"
                              placeholder="Jelaskan bagian mana yang perlu diperbaiki dan bagaimana caranya…"
                              minlength="10" required></textarea>
                    @error('feedback') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <button type="submit" class="btn btn-outline-danger px-4"
                            onclick="return confirm('Kembalikan laporan ini untuk direvisi?')">
                        <i class="bi bi-arrow-repeat me-2"></i>Minta Revisi
                    </button>
                </form>
            </div>

        </div>
    </div>
    @else
    <div class="d-flex gap-2">
        <a href="{{ route('supervisor.reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
    @endif

</div>

{{-- Sidebar --}}
<div class="col-xl-4 d-flex flex-column gap-3">

    {{-- Info siswa --}}
    <div class="card">
        <div class="card-header"><i class="bi bi-person-fill text-primary me-2"></i>Info Siswa</div>
        <div class="card-body" style="font-size:13px;padding:16px 20px">
            @php $student = $report->internship->application->student; @endphp
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:42px;height:42px;border-radius:11px;background:#eff6ff;color:#1a56db;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;flex-shrink:0">
                    {{ strtoupper(substr($student->user->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight:700;color:#0f172a">{{ $student->user->name }}</div>
                    <div style="color:#94a3b8;font-size:12px">{{ $student->nis }} · {{ $student->class }}</div>
                </div>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span style="color:#94a3b8">Perusahaan</span>
                <span style="font-weight:500;text-align:right;max-width:150px">{{ Str::limit($report->internship->application->program->company->name, 22) }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span style="color:#94a3b8">Program</span>
                <span style="font-weight:500;text-align:right;max-width:150px">{{ Str::limit($report->internship->application->program->title, 22) }}</span>
            </div>
        </div>
    </div>

    {{-- Progress laporan siswa --}}
    <div class="card">
        <div class="card-header"><i class="bi bi-bar-chart text-success me-2"></i>Progress Laporan</div>
        <div class="card-body" style="font-size:13px;padding:16px 20px">
            @php
                $totalReports    = $report->internship->dailyReports()->count();
                $approvedReports = $report->internship->dailyReports()->where('status','approved')->count();
                $pendingReports  = $report->internship->dailyReports()->where('status','submitted')->count();
            @endphp
            <div class="d-flex justify-content-between mb-1">
                <span style="color:#94a3b8">Total laporan</span>
                <span style="font-weight:600">{{ $totalReports }}</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span style="color:#94a3b8">Disetujui</span>
                <span style="font-weight:600;color:#10b981">{{ $approvedReports }}</span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span style="color:#94a3b8">Menunggu</span>
                <span style="font-weight:600;color:#f59e0b">{{ $pendingReports }}</span>
            </div>
            @if($totalReports > 0)
            <div class="progress" style="height:7px;border-radius:4px">
                <div class="progress-bar bg-success" style="width: {{ round(($approvedReports/$totalReports)*100) }}%"></div>
            </div>
            <div style="font-size:11.5px;color:#94a3b8;margin-top:4px;text-align:right">
                {{ round(($approvedReports/$totalReports)*100) }}% disetujui
            </div>
            @endif
        </div>
    </div>

    {{-- Navigasi laporan --}}
    <div class="card">
        <div class="card-header"><i class="bi bi-arrow-left-right text-secondary me-2"></i>Navigasi</div>
        <div class="card-body d-flex flex-column gap-2" style="padding:12px 16px">
            @php
                $internshipId = $report->internship_id;
                $prev = \App\Models\DailyReport::where('internship_id', $internshipId)
                    ->where('report_date', '<', $report->report_date)
                    ->latest('report_date')->first();
                $next = \App\Models\DailyReport::where('internship_id', $internshipId)
                    ->where('report_date', '>', $report->report_date)
                    ->oldest('report_date')->first();
            @endphp
            @if($prev)
                <a href="{{ route('supervisor.reports.show', $prev) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-chevron-left me-1"></i>
                    {{ \Carbon\Carbon::parse($prev->report_date)->format('d M Y') }}
                </a>
            @endif
            @if($next)
                <a href="{{ route('supervisor.reports.show', $next) }}"
                   class="btn btn-outline-secondary btn-sm d-flex justify-content-between align-items-center">
                    <span>{{ \Carbon\Carbon::parse($next->report_date)->format('d M Y') }}</span>
                    <i class="bi bi-chevron-right ms-1"></i>
                </a>
            @endif
        </div>
    </div>

</div>
</div>

@endsection
