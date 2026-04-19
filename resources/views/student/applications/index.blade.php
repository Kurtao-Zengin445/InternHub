@extends('layouts.app')

@section('title', 'Lamaran Saya')
@section('page-title', 'Lamaran Saya')
@section('page-subtitle', 'Riwayat dan status lamaran magang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    <a href="{{ route('student.applications.create') }}" class="btn btn-primary"><i class="bi bi-search me-2"></i>Cari Program Magang</a>
</div>

@forelse($applications as $application)
<div class="card mb-3">
    <div class="card-body" style="padding:20px 24px">
        <div class="row align-items-center g-3">
            <div class="col-md-5">
                <div style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:4px">{{ $application->program->title }}</div>
                <div style="font-size:13px;color:#64748b;margin-bottom:8px"><i class="bi bi-building me-1"></i>{{ $application->program->company->name }}</div>
                @if($application->program->field)
                <span style="font-size:11.5px;background:#eff6ff;color:#1a56db;padding:3px 10px;border-radius:20px;font-weight:600">{{ $application->program->field }}</span>
                @endif
            </div>
            <div class="col-md-3">
                <div style="font-size:12px;color:#94a3b8;margin-bottom:2px">Periode Magang</div>
                <div style="font-size:13px;font-weight:500;color:#0f172a">{{ \Carbon\Carbon::parse($application->program->start_date)->format('d M Y') }}</div>
                <div style="font-size:12px;color:#94a3b8">s.d. {{ \Carbon\Carbon::parse($application->program->end_date)->format('d M Y') }}</div>
            </div>
            <div class="col-md-2 text-center">
                @php
                    $statusMap = ['pending' => ['Menunggu', 'warning'], 'reviewed' => ['Ditinjau', 'info'], 'accepted' => ['Diterima', 'success'], 'rejected' => ['Ditolak', 'danger'], 'cancelled' => ['Dibatalkan', 'secondary']];
                    [$statusLabel, $statusColor] = $statusMap[$application->status] ?? [$application->status, 'secondary'];
                @endphp
                <span class="badge-status bg-{{ $statusColor }}-subtle text-{{ $statusColor }}-emphasis d-block text-center">{{ $statusLabel }}</span>
                <div style="font-size:11px;color:#94a3b8;margin-top:4px">{{ \Carbon\Carbon::parse($application->applied_at)->diffForHumans() }}</div>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{ route('student.applications.show', $application) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye me-1"></i>Detail</a>
                @if($application->isPending())
                <form method="POST" action="{{ route('student.applications.destroy', $application) }}" class="mt-1" onsubmit="return confirm('Batalkan lamaran ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-x me-1"></i>Batalkan</button>
                </form>
                @endif
            </div>
        </div>

        @if($application->isRejected() && $application->rejection_reason)
        <div class="mt-3 pt-3 border-top" style="font-size:13px;color:#64748b"><i class="bi bi-chat-left-text text-danger me-2"></i><strong style="color:#991b1b">Alasan penolakan:</strong> {{ $application->rejection_reason }}</div>
        @endif

        @if($application->isAccepted() && $application->internship)
        <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div style="font-size:13px;color:#065f46;font-weight:500"><i class="bi bi-check-circle-fill text-success me-2"></i>Magang aktif - {{ \Carbon\Carbon::parse($application->internship->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($application->internship->end_date)->format('d M Y') }}</div>
            <a href="{{ route('student.internship.show') }}" class="btn btn-success btn-sm"><i class="bi bi-person-workspace me-1"></i>Lihat Data Magang</a>
        </div>
        @endif
    </div>
</div>
@empty
<div class="card text-center" style="padding:60px">
    <div style="font-size:48px;margin-bottom:12px"><i class="bi bi-send"></i></div>
    <h5 style="font-weight:700;color:#0f172a">Belum ada lamaran</h5>
    <p style="color:#64748b;font-size:14px;margin-bottom:20px">Mulai cari program magang yang sesuai dengan bidang keahlianmu.</p>
    <a href="{{ route('student.applications.create') }}" class="btn btn-primary mx-auto" style="max-width:220px"><i class="bi bi-search me-2"></i>Cari Program Magang</a>
</div>
@endforelse

@if($applications->hasPages())
<div class="d-flex justify-content-center mt-2">{{ $applications->links('pagination::bootstrap-5') }}</div>
@endif

@endsection
