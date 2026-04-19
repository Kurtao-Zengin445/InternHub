@extends('layouts.app')

@section('title', 'Detail Magang')
@section('page-title', 'Detail Magang')
@section('page-subtitle', 'Informasi lengkap program magang Anda')

@section('content')

@if(!$internship)
<div class="alert alert-info d-flex align-items-center" role="alert">
    <i class="bi bi-info-circle me-2"></i>
    <div>
        <strong>Anda belum memiliki magang.</strong>
        <div class="mt-1" style="font-size:13px">Silakan pilih program magang terlebih dahulu.</div>
    </div>
</div>
<div class="text-center py-5">
    <div style="font-size:64px;margin-bottom:16px">💼</div>
    <h5 style="font-weight:700;color:#0f172a">Belum Ada Magang</h5>
    <p style="color:#64748b;font-size:14px">Anda perlu memiliki magang aktif untuk melihat detail magang.</p>
    <a href="{{ route('student.applications.index') }}" class="btn btn-primary">
        <i class="bi bi-briefcase me-2"></i>Lihat Lowongan Magang
    </a>
</div>
@else

<div class="row g-3">
    <div class="col-xl-8">
        {{-- Card Info Utama --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-briefcase-fill text-primary"></i>
                    <span class="fw-semibold">Informasi Magang</span>
                </div>
                <span class="badge px-3 py-2 @if($internship->status === 'active') bg-success @else bg-warning @endif">
                    {{ $internship->status_label }}
                </span>
            </div>
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">{{ $internship->application->program->title }}</h5>
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 text-muted mb-2">
                        <i class="bi bi-building"></i>
                        <span>{{ $internship->application->program->company->name }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-muted mb-2">
                        <i class="bi bi-calendar3"></i>
                        <span>{{ \Carbon\Carbon::parse($internship->start_date)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($internship->end_date)->translatedFormat('d F Y') }}</span>
                    </div>
                    @if($internship->application->program->field)
                    <div class="d-flex align-items-center gap-2 text-muted">
                        <i class="bi bi-tag"></i>
                        <span>{{ $internship->application->program->field }}</span>
                    </div>
                    @endif
                </div>

                {{-- Progress Bar --}}
                <div class="mb-2">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Progres Magang</span>
                        <span>{{ $internship->progress_percent }}%</span>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 8px;">
                        <div class="progress-bar bg-primary progress-bar-striped" role="progressbar" 
                             style="width: {{ $internship->progress_percent }}%" 
                             aria-valuenow="{{ $internship->progress_percent }}" 
                             aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <div class="row g-3 mt-4">
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="text-muted small text-uppercase mb-1">Tanggal Mulai</div>
                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($internship->start_date)->translatedFormat('l, d F Y') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="text-muted small text-uppercase mb-1">Tanggal Selesai</div>
                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($internship->end_date)->translatedFormat('l, d F Y') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="text-muted small text-uppercase mb-1">Hari Berjalan</div>
                            <div class="fw-semibold">{{ $internship->daysPassed() }} hari</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="text-muted small text-uppercase mb-1">Sisa Hari</div>
                            <div class="fw-semibold">{{ $internship->daysRemaining() }} hari</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Pembimbing --}}
        @if($internship->supervisor || $internship->companySupervisor)
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-person-badge text-primary"></i>
                    <span class="fw-semibold">Daftar Pembimbing</span>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    @if($internship->supervisor)
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3">
                            <div class="text-primary small text-uppercase mb-2"><i class="bi bi-person-check"></i> Pembimbing Sekolah</div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-weight: 700;">
                                    {{ strtoupper(substr($internship->supervisor->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $internship->supervisor->user->name }}</div>
                                    <div class="text-muted small">{{ $internship->supervisor->user->email }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($internship->companySupervisor)
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3">
                            <div class="text-success small text-uppercase mb-2"><i class="bi bi-building"></i> Pembimbing Perusahaan</div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-weight: 700;">
                                    {{ strtoupper(substr($internship->companySupervisor->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $internship->companySupervisor->name }}</div>
                                    <div class="text-muted small">{{ $internship->companySupervisor->position }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Card Deskripsi Program --}}
        @if($internship->application->program->description)
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle text-primary"></i>
                    <span class="fw-semibold">Deskripsi Program</span>
                </div>
            </div>
            <div class="card-body p-4">
                <p class="mb-0" style="white-space: pre-line">{{ $internship->application->program->description }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="col-xl-4">
        {{-- Aksi Cepat --}}
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-lightning-charge text-primary"></i>
                    <span class="fw-semibold">Aksi Cepat</span>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="d-grid gap-2">
                    <a href="{{ route('student.attendance.today') }}" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-2"></i>
                        Presensi Hari Ini
                    </a>
                    <a href="{{ route('student.reports.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-journal-text me-2"></i>
                        Buat Laporan Harian
                    </a>
                    <a href="{{ route('student.internship.documents') }}" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-arrow-up me-2"></i>
                        Kelola Dokumen
                    </a>
                    <a href="{{ route('student.internship.evaluation') }}" class="btn btn-outline-primary">
                        <i class="bi bi-star me-2"></i>
                        Lihat Penilaian
                    </a>
                </div>
            </div>
        </div>

        {{-- Statistik --}}
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart text-primary"></i>
                    <span class="fw-semibold">Statistik</span>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                        <span>Total Presensi</span>
                        <span class="fw-semibold">{{ $internship->attendances()->count() }} hari</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                        <span>Laporan Harian</span>
                        <span class="fw-semibold">{{ $internship->dailyReports()->count() }} laporan</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                        <span>Dokumen Terunggah</span>
                        <span class="fw-semibold">{{ $internship->documents()->count() }} dokumen</span>
                    </div>
                    @if($internship->evaluations()->count() > 0)
                    <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                        <span>Nilai Akhir</span>
                        <span class="fw-bold text-primary">{{ number_format($internship->finalScore(), 1) }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Lokasi Magang --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-geo-alt text-primary"></i>
                    <span class="fw-semibold">Lokasi Magang</span>
                </div>
            </div>
            <div class="card-body p-3">
                @if($internship->application->program->company->address)
                <p class="mb-0 text-muted">
                    <i class="bi bi-geo-alt me-2"></i>
                    {{ $internship->application->program->company->address }}
                </p>
                @else
                <p class="mb-0 text-muted">
                    <i class="bi bi-info-circle me-2"></i>
                    Lokasi belum diatur
                </p>
                @endif
            </div>
        </div>
    </div>
</div>

@endif

@endsection