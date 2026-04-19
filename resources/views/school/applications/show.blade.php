@extends('layouts.app')

@section('title', 'Detail Lamaran')
@section('page-title', 'Detail Lamaran')
@section('page-subtitle', $application->student->user->name . ' — ' . $application->program->title)

@section('content')

<div class="row justify-content-center">
<div class="col-xl-8 col-lg-10">

    {{-- Status --}}
    @php
        $am=['pending'=>['Menunggu Keputusan','warning','#fffbeb','#92400e','hourglass-split'],'reviewed'=>['Sedang Ditinjau','info','#eff6ff','#1e40af','eye'],'accepted'=>['Lamaran Diterima','success','#f0fdf4','#065f46','check-circle-fill'],'rejected'=>['Lamaran Ditolak','danger','#fef2f2','#991b1b','x-circle-fill'],'cancelled'=>['Dibatalkan','secondary','#f8fafc','#475569','slash-circle']];
        [$al,$ac,$abg,$atxt,$aicon]=$am[$application->status]??[$application->status,'secondary','#f8fafc','#475569','question'];
    @endphp
    <div class="card mb-3" style="background: {{ $abg }};border-color:transparent">
        <div class="card-body d-flex align-items-center gap-3" style="padding:18px 24px">
            <i class="bi bi-{{ $aicon }}" style="font-size:28px;color: {{ $atxt }};flex-shrink:0"></i>
            <div>
                <div style="font-size:15px;font-weight:700;color: {{ $atxt }}">{{ $al }}</div>
                <div style="font-size:12.5px;color: {{ $atxt }};opacity:.7;margin-top:1px">
                    {{ $application->student->user->name }} ·
                    Dikirim {{ \Carbon\Carbon::parse($application->applied_at)->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Info lamaran --}}
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-info-circle text-primary me-2"></i>Info Lamaran</div>
        <div class="card-body" style="padding:24px">
            <div class="row g-3" style="font-size:13.5px">
                <div class="col-sm-6">
                    <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Siswa</div>
                    <div style="font-weight:600">{{ $application->student->user->name }}</div>
                    <div style="font-size:12px;color:#94a3b8">{{ $application->student->nis }} · {{ $application->student->class }}</div>
                </div>
                <div class="col-sm-6">
                    <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Program</div>
                    <div style="font-weight:600">{{ $application->program->title }}</div>
                    <div style="font-size:12px;color:#94a3b8">{{ $application->program->company->name }}</div>
                </div>
                <div class="col-sm-6">
                    <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Tanggal Lamar</div>
                    <div style="font-weight:600">{{ \Carbon\Carbon::parse($application->applied_at)->format('d M Y, H:i') }}</div>
                </div>
                @if($application->reviewed_at)
                <div class="col-sm-6">
                    <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Tanggal Diproses</div>
                    <div style="font-weight:600">{{ \Carbon\Carbon::parse($application->reviewed_at)->format('d M Y') }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Alasan penolakan --}}
    @if($application->isRejected() && $application->rejection_reason)
    <div class="card mb-3" style="border-color:#fecaca;background:#fef2f2">
        <div class="card-body" style="padding:18px 22px">
            <div style="font-size:13px;font-weight:700;color:#991b1b;margin-bottom:8px">
                <i class="bi bi-chat-left-text-fill me-2"></i>Alasan Penolakan
            </div>
            <div style="font-size:13.5px;color:#7f1d1d;line-height:1.65">{{ $application->rejection_reason }}</div>
        </div>
    </div>
    @endif

    {{-- Link ke magang --}}
    @if($application->isAccepted() && $application->internship)
    <div class="card mb-3" style="border-color:#d1fae5;background:#f0fdf4">
        <div class="card-body d-flex align-items-center justify-content-between gap-3" style="padding:16px 22px">
            <div>
                <div style="font-size:13.5px;font-weight:700;color:#065f46">
                    <i class="bi bi-person-workspace me-2"></i>Magang Aktif
                </div>
                <div style="font-size:12.5px;color:#16a34a;margin-top:2px">
                    {{ \Carbon\Carbon::parse($application->internship->start_date)->format('d M Y') }} –
                    {{ \Carbon\Carbon::parse($application->internship->end_date)->format('d M Y') }}
                </div>
            </div>
        </div>
    </div>
    @endif

    <a href="{{ route('school.applications.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>

</div>
</div>

@endsection
