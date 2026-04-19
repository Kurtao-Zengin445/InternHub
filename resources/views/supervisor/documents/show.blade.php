@extends('layouts.app')

@section('title', 'Detail Dokumen')
@section('page-title', 'Detail Dokumen')
@section('page-subtitle', $document->internship->application->student->user->name . ' — ' . $document->title)

@section('content')

<div class="row justify-content-center">
<div class="col-xl-8 col-lg-10">

    {{-- Status banner --}}
    @php
        $dmap=['pending'=>['Menunggu Verifikasi','warning','#fffbeb','#92400e','hourglass-split'],'approved'=>['Disetujui','success','#f0fdf4','#065f46','check-circle-fill'],'rejected'=>['Ditolak','danger','#fef2f2','#991b1b','x-circle-fill']];
        [$dl,$dc,$dbg,$dtxt,$dicon]=$dmap[$document->status]??[$document->status,'secondary','#f8fafc','#475569','question'];
    @endphp

    <div class="card mb-3" style="background:{{ $dbg }};border-color:transparent">
        <div class="card-body d-flex align-items-center gap-3" style="padding:18px 24px">
            <i class="bi bi-{{ $dicon }}" style="font-size:28px;color:{{ $dtxt }};flex-shrink:0"></i>
            <div class="flex-fill">
                <div style="font-size:15px;font-weight:700;color:{{ $dtxt }}">{{ $dl }}</div>
                <div style="font-size:12.5px;color:{{ $dtxt }};opacity:.7;margin-top:1px">
                    Diunggah {{ \Carbon\Carbon::parse($document->uploaded_at)->translatedFormat('d F Y, H:i') }}
                    oleh {{ $document->uploader->name }}
                </div>
            </div>
        </div>
    </div>

    {{-- Alasan penolakan --}}
    @if($document->status === 'rejected' && $document->rejection_reason)
    <div class="card mb-3" style="border-color:#fecaca;background:#fef2f2">
        <div class="card-body" style="padding:18px 22px">
            <div style="font-size:13px;font-weight:700;color:#991b1b;margin-bottom:8px">
                <i class="bi bi-chat-left-text-fill me-2"></i>Alasan Penolakan
            </div>
            <div style="font-size:13.5px;color:#7f1d1d;line-height:1.65">{{ $document->rejection_reason }}</div>
        </div>
    </div>
    @endif

    {{-- Info dokumen --}}
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-file-earmark-text text-primary me-2"></i>Info Dokumen</div>
        <div class="card-body" style="padding:24px">
            <div class="row g-3" style="font-size:13.5px">
                <div class="col-sm-6">
                    <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Judul</div>
                    <div style="font-weight:600">{{ $document->title }}</div>
                </div>
                <div class="col-sm-6">
                    <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Jenis Dokumen</div>
                    @php
                        $typeLabels = ['introduction_letter'=>'Surat Pengantar','acceptance_letter'=>'Surat Penerimaan','activity_plan'=>'Rencana Kegiatan','progress_report'=>'Laporan Kemajuan','final_report'=>'Laporan Akhir','certificate'=>'Sertifikat','other'=>'Lainnya'];
                    @endphp
                    <div style="font-weight:600">{{ $typeLabels[$document->document_type] ?? $document->document_type }}</div>
                </div>
                <div class="col-sm-6">
                    <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Nama File</div>
                    <div style="font-weight:600">{{ $document->file_name }}</div>
                </div>
                <div class="col-sm-6">
                    <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Ukuran</div>
                    <div style="font-weight:600">{{ $document->fileSizeFormatted() }}</div>
                </div>
                <div class="col-sm-6">
                    <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Siswa</div>
                    <div style="font-weight:600">{{ $document->internship->application->student->user->name }}</div>
                </div>
                <div class="col-sm-6">
                    <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Perusahaan</div>
                    <div style="font-weight:600">{{ $document->internship->application->program->company->name }}</div>
                </div>
            </div>

            {{-- Tombol download --}}
            <div class="mt-4 pt-3 border-top">
                <a href="{{ route('supervisor.documents.download', $document) }}"
                   class="btn btn-primary">
                    <i class="bi bi-download me-2"></i>Unduh Dokumen
                </a>
            </div>
        </div>
    </div>

    {{-- Aksi verifikasi --}}
    @if($document->status === 'pending')
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-shield-check text-primary me-2"></i>Verifikasi</div>
        <div class="card-body" style="padding:24px">

            {{-- Setujui --}}
            <div class="mb-4">
                <form method="POST" action="{{ route('supervisor.documents.approve', $document) }}">
                    @csrf
                    <button type="submit" class="btn btn-success px-4"
                            onclick="return confirm('Setujui dokumen ini?')">
                        <i class="bi bi-check-circle-fill me-2"></i>Setujui Dokumen
                    </button>
                </form>
            </div>

            {{-- Tolak --}}
            <div class="border-top pt-4">
                <label class="form-label fw-semibold" style="font-size:13.5px">
                    Tolak dengan Alasan <span class="text-danger">*</span>
                </label>
                <form method="POST" action="{{ route('supervisor.documents.reject', $document) }}">
                    @csrf
                    <textarea name="rejection_reason" rows="3"
                              class="form-control mb-3 @error('rejection_reason') is-invalid @enderror"
                              style="font-size:13.5px"
                              placeholder="Jelaskan mengapa dokumen ini perlu diperbaiki atau diunggah ulang…"
                              required minlength="10">{{ old('rejection_reason') }}</textarea>
                    @error('rejection_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <button type="submit" class="btn btn-outline-danger px-4"
                            onclick="return confirm('Tolak dokumen ini?')">
                        <i class="bi bi-x-circle me-2"></i>Tolak Dokumen
                    </button>
                </form>
            </div>

        </div>
    </div>
    @endif

    <div class="d-flex gap-2">
        <a href="{{ route('supervisor.documents.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>

</div>
</div>

@endsection
