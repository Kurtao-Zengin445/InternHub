@extends('layouts.app')

@section('title', 'Detail Lamaran')
@section('page-title', 'Detail Lamaran')
@section('page-subtitle', $application->program->title)

@section('content')
<div class="row g-3">
<div class="col-xl-8">
    @php
        $statusMap = [
            'pending' => ['Menunggu Keputusan', 'warning', '#fffbeb', '#92400e', 'hourglass-split'],
            'reviewed' => ['Sedang Ditinjau', 'info', '#eff6ff', '#1e40af', 'eye'],
            'accepted' => ['Lamaran Diterima!', 'success', '#f0fdf4', '#065f46', 'check-circle-fill'],
            'rejected' => ['Lamaran Ditolak', 'danger', '#fef2f2', '#991b1b', 'x-circle-fill'],
            'cancelled' => ['Dibatalkan', 'secondary', '#f8fafc', '#475569', 'slash-circle'],
        ];
        [$statusLabel, $statusColor, $statusBackground, $statusText, $statusIcon] = $statusMap[$application->status] ?? [$application->status, 'secondary', '#f8fafc', '#475569', 'question'];
    @endphp
    <div class="card mb-3" style="background:{{ $statusBackground }};border-color:transparent">
        <div class="card-body d-flex align-items-center gap-3" style="padding:18px 24px">
            <i class="bi bi-{{ $statusIcon }}" style="font-size:32px;color:{{ $statusText }};flex-shrink:0"></i>
            <div>
                <div style="font-size:16px;font-weight:800;color:{{ $statusText }}">{{ $statusLabel }}</div>
                <div style="font-size:12.5px;color:{{ $statusText }};opacity:.7;margin-top:2px">
                    Dikirim {{ \Carbon\Carbon::parse($application->applied_at)->translatedFormat('d F Y, H:i') }}
                    @if($application->reviewed_at)
                    - Diproses {{ \Carbon\Carbon::parse($application->reviewed_at)->translatedFormat('d F Y') }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($application->isRejected() && $application->rejection_reason)
    <div class="card mb-3" style="border-color:#fecaca;background:#fef2f2">
        <div class="card-body" style="padding:18px 22px">
            <div style="font-size:13px;font-weight:700;color:#991b1b;margin-bottom:8px"><i class="bi bi-chat-left-text-fill me-2"></i>Alasan Penolakan</div>
            <div style="font-size:13.5px;color:#7f1d1d;line-height:1.65">{{ $application->rejection_reason }}</div>
        </div>
    </div>
    @endif

    @if($application->isAccepted() && $application->internship)
    <div class="card mb-3" style="border-color:#d1fae5;background:#f0fdf4">
        <div class="card-body d-flex align-items-center justify-content-between gap-3 flex-wrap" style="padding:16px 22px">
            <div>
                <div style="font-size:13.5px;font-weight:700;color:#065f46"><i class="bi bi-person-workspace me-2"></i>Magang Aktif</div>
                <div style="font-size:12.5px;color:#16a34a;margin-top:2px">{{ \Carbon\Carbon::parse($application->internship->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($application->internship->end_date)->format('d M Y') }}</div>
            </div>
            <a href="{{ route('student.internship.show') }}" class="btn btn-success btn-sm">Lihat Data Magang <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
    @endif

    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-file-text text-primary me-2"></i>Surat Motivasi</div>
        <div class="card-body" style="padding:24px"><div style="font-size:14px;color:#1e293b;line-height:1.85;white-space:pre-wrap">{{ $application->motivation_letter }}</div></div>
    </div>

    @if($application->cv_file)
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-file-earmark-person text-success me-2"></i>CV yang Dilampirkan</div>
        <div class="card-body d-flex align-items-center gap-3" style="padding:18px 24px">
            <div style="width:44px;height:44px;border-radius:10px;background:#d1fae5;color:#065f46;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0"><i class="bi bi-file-earmark-pdf"></i></div>
            <div class="flex-fill"><div style="font-size:13.5px;font-weight:600;color:#0f172a">{{ basename($application->cv_file) }}</div></div>
            <a href="{{ asset('storage/' . $application->cv_file) }}" target="_blank" rel="noopener" class="btn btn-outline-success btn-sm"><i class="bi bi-download me-1"></i>Unduh</a>
        </div>
    </div>
    @endif

    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('student.applications.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
        @if($application->isPending())
        <form method="POST" action="{{ route('student.applications.destroy', $application) }}" onsubmit="return confirm('Batalkan lamaran ini? Tindakan ini tidak dapat dibatalkan.')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger"><i class="bi bi-x-circle me-2"></i>Batalkan Lamaran</button>
        </form>
        @endif
    </div>
</div>

<div class="col-xl-4 d-flex flex-column gap-3">
    <div class="card">
        <div class="card-header"><i class="bi bi-briefcase-fill text-warning me-2"></i>Info Program</div>
        <div class="card-body" style="font-size:13px;padding:16px 20px">
            <div style="font-weight:700;color:#0f172a;margin-bottom:4px">{{ $application->program->title }}</div>
            @if($application->program->field)
            <span style="font-size:12px;background:#eff6ff;color:#1a56db;padding:3px 10px;border-radius:20px;font-weight:600;margin-bottom:12px;display:inline-block">{{ $application->program->field }}</span>
            @endif
            <div class="d-flex flex-column gap-1 mt-2">
                <div class="d-flex justify-content-between"><span style="color:#94a3b8">Perusahaan</span><span style="font-weight:500;text-align:right;max-width:150px">{{ $application->program->company->name }}</span></div>
                <div class="d-flex justify-content-between"><span style="color:#94a3b8">Mulai</span><span style="font-weight:500">{{ \Carbon\Carbon::parse($application->program->start_date)->format('d M Y') }}</span></div>
                <div class="d-flex justify-content-between"><span style="color:#94a3b8">Selesai</span><span style="font-weight:500">{{ \Carbon\Carbon::parse($application->program->end_date)->format('d M Y') }}</span></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="bi bi-clock-history text-secondary me-2"></i>Riwayat Status</div>
        <div class="card-body" style="font-size:13px;padding:16px 20px">
            <div class="d-flex align-items-center gap-2 mb-2"><div style="width:8px;height:8px;border-radius:50%;background:#10b981;flex-shrink:0"></div><span>Lamaran dikirim</span><span style="color:#94a3b8;margin-left:auto;font-size:11.5px">{{ \Carbon\Carbon::parse($application->applied_at)->format('d M Y') }}</span></div>
            @if($application->reviewed_at)
            <div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:50%;background:{{ $application->isAccepted() ? '#10b981' : '#ef4444' }};flex-shrink:0"></div><span>{{ $application->isAccepted() ? 'Diterima' : 'Ditolak' }}</span><span style="color:#94a3b8;margin-left:auto;font-size:11.5px">{{ \Carbon\Carbon::parse($application->reviewed_at)->format('d M Y') }}</span></div>
            @endif
        </div>
    </div>
</div>
</div>

@endsection
