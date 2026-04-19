@extends('layouts.app')

@section('title', 'Dokumen Magang')
@section('page-title', 'Dokumen Magang')
@section('page-subtitle', 'Kelola semua dokumen magang Anda')

@section('content')

@if(!$hasActiveInternship)
<div class="alert alert-info d-flex align-items-center" role="alert">
    <i class="bi bi-info-circle me-2"></i>
    <div>
        <strong>Anda belum memiliki magang aktif.</strong>
        <div class="mt-1" style="font-size:13px">Silakan pilih program magang terlebih dahulu untuk mulai mengunggah dokumen.</div>
    </div>
</div>
<div class="text-center py-5">
    <div style="font-size:64px;margin-bottom:16px">📁</div>
    <h5 style="font-weight:700;color:#0f172a">Belum Ada Magang Aktif</h5>
    <p style="color:#64748b;font-size:14px">Anda perlu memiliki magang aktif untuk mengakses fitur dokumen.</p>
    <a href="{{ route('student.applications.index') }}" class="btn btn-primary">
        <i class="bi bi-briefcase me-2"></i>Lihat Lowongan Magang
    </a>
</div>
@else

<div class="row g-3">
    <div class="col-xl-8">
        {{-- Form Upload Dokumen --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-cloud-upload text-primary"></i>
                    <span class="fw-semibold">Unggah Dokumen Baru</span>
                </div>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('student.internship.documents.upload') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Jenis Dokumen</label>
                            <select name="document_type" class="form-select @error('document_type') is-invalid @enderror" required @if(!$hasActiveInternship) disabled @endif>
                                <option value="">Pilih jenis dokumen</option>
                                <option value="introduction_letter" {{ old('document_type') == 'introduction_letter' ? 'selected' : '' }}>Surat Pengantar Magang</option>
                                <option value="acceptance_letter" {{ old('document_type') == 'acceptance_letter' ? 'selected' : '' }}>Surat Penerimaan Magang</option>
                                <option value="activity_plan" {{ old('document_type') == 'activity_plan' ? 'selected' : '' }}>Rencana Kegiatan</option>
                                <option value="progress_report" {{ old('document_type') == 'progress_report' ? 'selected' : '' }}>Laporan Progress</option>
                                <option value="final_report" {{ old('document_type') == 'final_report' ? 'selected' : '' }}>Laporan Akhir</option>
                                <option value="certificate" {{ old('document_type') == 'certificate' ? 'selected' : '' }}>Sertifikat Magang</option>
                                <option value="other" {{ old('document_type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('document_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Judul Dokumen</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title') }}" placeholder="Masukkan judul dokumen" required @if(!$hasActiveInternship) disabled @endif>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Pilih File</label>
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror"
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required @if(!$hasActiveInternship) disabled @endif>
                            <div class="form-text small mt-1">
                                Format yang diizinkan: PDF, DOC, DOCX, JPG, JPEG, PNG. Maksimal 10 MB.
                            </div>
                            @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary" @if(!$hasActiveInternship) disabled @endif>
                                <i class="bi bi-upload me-2"></i>
                                Unggah Dokumen
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        {{-- Panduan --}}
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle text-primary"></i>
                    <span class="fw-semibold">Panduan</span>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="small">
                    <div class="mb-3">
                        <div class="fw-semibold mb-1 text-primary">📋 Jenis Dokumen:</div>
                        <ul class="list-unstyled mb-0 text-muted">
                            <li>• Surat Pengantar dari Sekolah</li>
                            <li>• Surat Penerimaan dari Perusahaan</li>
                            <li>• Rencana Kegiatan Magang</li>
                            <li>• Laporan Progress Bulanan</li>
                            <li>• Laporan Akhir Magang</li>
                            <li>• Sertifikat Magang</li>
                        </ul>
                    </div>
                    <div>
                        <div class="fw-semibold mb-1 text-success">✅ Status Dokumen:</div>
                        <ul class="list-unstyled mb-0 text-muted">
                            <li>• <span class="text-warning">Menunggu</span> - Menunggu diverifikasi pembimbing</li>
                            <li>• <span class="text-success">Terverifikasi</span> - Dokumen sudah disetujui</li>
                            <li>• <span class="text-danger">Ditolak</span> - Dokumen perlu diperbaiki</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Daftar Dokumen --}}
@foreach($documents as $type => $typeDocuments)
<div class="card mb-3">
    <div class="card-header">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-folder text-primary"></i>
            <span class="fw-semibold">
                @switch($type)
                @case('introduction_letter') Surat Pengantar Magang @break
                @case('acceptance_letter') Surat Penerimaan Magang @break
                @case('activity_plan') Rencana Kegiatan @break
                @case('progress_report') Laporan Progress @break
                @case('final_report') Laporan Akhir @break
                @case('certificate') Sertifikat Magang @break
                @default Lainnya
                @endswitch
            </span>
            <span class="badge bg-secondary ms-auto">{{ $typeDocuments->count() }}</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            @foreach($typeDocuments as $document)
            <div class="list-group-item list-group-item-action p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="flex-shrink-0">
                        @if(in_array($document->file_type, ['pdf', 'doc', 'docx']))
                        <i class="bi bi-file-earmark-text text-primary" style="font-size: 32px;"></i>
                        @else
                        <i class="bi bi-file-earmark-image text-success" style="font-size: 32px;"></i>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $document->title }}</div>
                        <div class="small text-muted">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ \Carbon\Carbon::parse($document->uploaded_at)->translatedFormat('d F Y H:i') }}
                            <span class="mx-2">|</span>
                            <i class="bi bi-file-earmark me-1"></i>
                            {{ strtoupper($document->file_type) }}
                            <span class="mx-2">|</span>
                            <i class="bi bi-database me-1"></i>
                            {{ number_format($document->file_size / 1024, 1) }} KB
                        </div>
                    </div>
                    <div class="flex-shrink-0 d-flex align-items-center gap-2">
                        <span class="badge px-3 py-2 @if($document->status === 'verified') bg-success @elseif($document->status === 'rejected') bg-danger @else bg-warning @endif">
                            @if($document->status === 'verified') Terverifikasi
                            @elseif($document->status === 'rejected') Ditolak
                            @else Menunggu Verifikasi
                            @endif
                        </span>
                        <a href="{{ route('student.internship.documents.download', $document) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                </div>
                @if($document->notes)
                <div class="mt-2 p-2 rounded bg-light small text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Catatan: {{ $document->notes }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endforeach

@if($documents->isEmpty())
<div class="card text-center p-5">
    <div class="mb-3">
        <i class="bi bi-folder-x" style="font-size: 48px; color: #cbd5e1;"></i>
    </div>
    <h5>Belum ada dokumen</h5>
    <p class="text-muted mb-0">Unggah dokumen magang Anda menggunakan form di atas.</p>
</div>
@endif
</div>

@endif

@endsection