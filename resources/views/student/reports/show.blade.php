@extends('layouts.app')

@section('title', 'Detail Laporan')
@section('page-title', 'Detail Laporan Harian')
@section('page-subtitle', \Carbon\Carbon::parse($report->report_date)->translatedFormat('l, d F Y'))

@section('content')

<div class="row g-3">
<div class="col-xl-8">

    {{-- Header status --}}
    @php
        $statusConfig = [
            'draft'     => ['Draft',         'secondary', '#f1f5f9', '#475569', 'file-earmark'],
            'submitted' => ['Menunggu Verifikasi', 'warning',  '#fffbeb', '#92400e', 'hourglass-split'],
            'approved'  => ['Disetujui',      'success',   '#f0fdf4', '#065f46', 'check-circle-fill'],
            'revision'  => ['Perlu Direvisi', 'danger',    '#fef2f2', '#991b1b', 'arrow-repeat'],
        ];
        [$slabel, $scolor, $sbg, $stxt, $sicon] = $statusConfig[$report->status] ?? [$report->status,'secondary','#f8fafc','#475569','question'];
    @endphp

    <div class="card mb-3">
        <div class="card-body d-flex align-items-center gap-3" style="padding:18px 24px;background:{{ $sbg }};border-radius:14px">
            <i class="bi bi-{{ $sicon }}" style="font-size:28px;color:{{ $stxt }};flex-shrink:0"></i>
            <div class="flex-fill">
                <div style="font-size:15px;font-weight:700;color:{{ $stxt }}">{{ $slabel }}</div>
                <div style="font-size:12.5px;color:{{ $stxt }};opacity:.7;margin-top:1px">
                    {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('l, d F Y') }}
                    @if($report->submitted_at)
                        · Dikirim {{ $report->submitted_at->diffForHumans() }}
                    @endif
                </div>
            </div>
            @if(in_array($report->status, ['draft','revision']))
                <a href="{{ route('student.reports.edit', $report) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
            @endif
        </div>
    </div>

    {{-- Catatan revisi dari pembimbing --}}
    @if($report->status === 'revision' && $report->feedback)
    <div class="card mb-3" style="border-color:#fecaca;background:#fef2f2">
        <div class="card-body" style="padding:18px 22px">
            <div style="font-size:13px;font-weight:700;color:#991b1b;margin-bottom:8px">
                <i class="bi bi-chat-left-text-fill me-2"></i>Catatan Pembimbing
            </div>
            <div style="font-size:13.5px;color:#7f1d1d;line-height:1.65">{{ $report->feedback }}</div>
        </div>
    </div>
    @endif

    {{-- Feedback positif --}}
    @if($report->status === 'approved' && $report->feedback)
    <div class="card mb-3" style="border-color:#d1fae5;background:#f0fdf4">
        <div class="card-body" style="padding:18px 22px">
            <div style="font-size:13px;font-weight:700;color:#065f46;margin-bottom:8px">
                <i class="bi bi-chat-left-text-fill me-2"></i>Catatan Pembimbing
            </div>
            <div style="font-size:13.5px;color:#064e3b;line-height:1.65">{{ $report->feedback }}</div>
        </div>
    </div>
    @endif

    {{-- Konten laporan --}}
    <div class="card mb-3">
        <div class="card-body" style="padding:28px">

            {{-- Kegiatan --}}
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
            <div class="mb-2 pt-4 border-top">
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
                     class="w-100 rounded-3"
                     style="max-height:400px;object-fit:cover;cursor:zoom-in"
                     alt="Foto dokumentasi">
            </a>
            <div style="font-size:12px;color:#94a3b8;margin-top:8px;text-align:center">
                Klik foto untuk melihat ukuran penuh
            </div>
        </div>
    </div>
    @endif

    {{-- Aksi tombol --}}
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('student.reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>

        @if(in_array($report->status, ['draft','revision']))
            <a href="{{ route('student.reports.edit', $report) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Edit Laporan
            </a>
            <form method="POST" action="{{ route('student.reports.update', $report) }}">
                @csrf @method('PUT')
                <input type="hidden" name="activity"  value="{{ $report->activity }}">
                <input type="hidden" name="problems"  value="{{ $report->problems }}">
                <input type="hidden" name="solutions" value="{{ $report->solutions }}">
                <input type="hidden" name="send"      value="1">
                <button class="btn btn-primary" onclick="return confirm('Kirim laporan ini ke pembimbing?')">
                    <i class="bi bi-send-fill me-2"></i>
                    {{ $report->status === 'revision' ? 'Kirim Ulang' : 'Kirim ke Pembimbing' }}
                </button>
            </form>
        @endif

        @if($report->status === 'draft')
        <form method="POST" action="{{ route('student.reports.destroy', $report) }}"
              onsubmit="return confirm('Hapus draft laporan ini?')" class="ms-auto">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger">
                <i class="bi bi-trash me-2"></i>Hapus Draft
            </button>
        </form>
        @endif
    </div>

</div>

{{-- Sidebar info --}}
<div class="col-xl-4 d-flex flex-column gap-3">
    <div class="card">
        <div class="card-header"><i class="bi bi-info-circle text-primary me-2"></i>Info Laporan</div>
        <div class="card-body" style="font-size:13px;padding:16px 20px">
            <div class="d-flex justify-content-between mb-2">
                <span style="color:#94a3b8">Tanggal</span>
                <span style="font-weight:600">{{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span style="color:#94a3b8">Status</span>
                <span class="badge-status bg-{{ $scolor }}-subtle text-{{ $scolor }}-emphasis" style="font-size:11px">{{ $slabel }}</span>
            </div>
            @if($report->submitted_at)
            <div class="d-flex justify-content-between mb-2">
                <span style="color:#94a3b8">Dikirim</span>
                <span style="font-weight:500">{{ $report->submitted_at->format('d M Y H:i') }}</span>
            </div>
            @endif
            <div class="d-flex justify-content-between">
                <span style="color:#94a3b8">Terakhir diperbarui</span>
                <span style="font-weight:500">{{ $report->updated_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>

    {{-- Navigasi laporan terdekat --}}
    <div class="card">
        <div class="card-header"><i class="bi bi-arrow-left-right text-secondary me-2"></i>Navigasi</div>
        <div class="card-body d-flex flex-column gap-2" style="padding:12px 16px">
            @php
                $internship = auth()->user()->student->activeInternship()
                    ?? auth()->user()->student->internships()->latest()->first();
                $prev = $internship ? $internship->dailyReports()
                    ->where('report_date', '<', $report->report_date)
                    ->latest('report_date')->first() : null;
                $next = $internship ? $internship->dailyReports()
                    ->where('report_date', '>', $report->report_date)
                    ->oldest('report_date')->first() : null;
            @endphp
            @if($prev)
                <a href="{{ route('student.reports.show', $prev) }}"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-chevron-left me-1"></i>
                    {{ \Carbon\Carbon::parse($prev->report_date)->format('d M Y') }}
                </a>
            @endif
            @if($next)
                <a href="{{ route('student.reports.show', $next) }}"
                   class="btn btn-outline-secondary btn-sm d-flex justify-content-between align-items-center">
                    <span>{{ \Carbon\Carbon::parse($next->report_date)->format('d M Y') }}</span>
                    <i class="bi bi-chevron-right ms-1"></i>
                </a>
            @endif
            @if(!$prev && !$next)
                <div style="font-size:13px;color:#94a3b8;text-align:center;padding:8px 0">
                    Tidak ada laporan lain.
                </div>
            @endif
        </div>
    </div>
</div>
</div>

@endsection
