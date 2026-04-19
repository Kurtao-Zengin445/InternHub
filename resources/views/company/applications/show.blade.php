@extends('layouts.app')

@section('title', 'Detail Lamaran')
@section('page-title', 'Detail Lamaran')
@section('page-subtitle', optional($application->student)->name . ' - ' . $application->program->title)

@section('content')
<div class="row g-3">
<div class="col-xl-8">
    @php
        $statusMap = [
            'pending' => ['Menunggu Keputusan', 'warning', '#fffbeb', '#92400e'],
            'reviewed' => ['Sedang Ditinjau', 'info', '#eff6ff', '#1e40af'],
            'accepted' => ['Diterima', 'success', '#f0fdf4', '#065f46'],
            'rejected' => ['Ditolak', 'danger', '#fef2f2', '#991b1b'],
            'cancelled' => ['Dibatalkan', 'secondary', '#f8fafc', '#475569'],
        ];
        [$statusLabel, $statusColor, $statusBackground, $statusText] = $statusMap[$application->status] ?? [$application->status, 'secondary', '#f8fafc', '#475569'];
        $remaining = $application->program->remainingQuota();
    @endphp
    <div class="card mb-3" style="background: {{ $statusBackground }};border-color:transparent">
        <div class="card-body d-flex align-items-center justify-content-between gap-3" style="padding:18px 24px">
            <div>
                <div style="font-size:15px;font-weight:700;color: {{ $statusText }}">{{ $statusLabel }}</div>
                <div style="font-size:12.5px;color: {{ $statusText }};opacity:.7;margin-top:1px">Dikirim {{ \Carbon\Carbon::parse($application->applied_at)->translatedFormat('d F Y, H:i') }}</div>
            </div>
            @if($application->isRejected() && $application->rejection_reason)
            <div style="font-size:13px;color: {{ $statusText }};max-width:300px;text-align:right;font-style:italic">"{{ Str::limit($application->rejection_reason, 80) }}"</div>
            @endif
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-file-text text-primary me-2"></i>Surat Motivasi</div>
        <div class="card-body" style="padding:24px"><div style="font-size:14px;color:#1e293b;line-height:1.85;white-space:pre-wrap">{{ $application->motivation_letter }}</div></div>
    </div>

    @if($application->cv_file)
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-file-earmark-person text-success me-2"></i>Curriculum Vitae</div>
        <div class="card-body d-flex align-items-center gap-3" style="padding:18px 24px">
            <div style="width:44px;height:44px;border-radius:10px;background:#d1fae5;color:#065f46;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0"><i class="bi bi-file-earmark-pdf"></i></div>
            <div class="flex-fill"><div style="font-size:13.5px;font-weight:600;color:#0f172a">{{ basename($application->cv_file) }}</div><div style="font-size:12px;color:#94a3b8">Dokumen CV pelamar</div></div>
            <a href="{{ asset('storage/' . $application->cv_file) }}" target="_blank" rel="noopener" class="btn btn-outline-success btn-sm"><i class="bi bi-download me-1"></i>Unduh CV</a>
        </div>
    </div>
    @endif

    @if($application->isPending())
    <div class="card" style="border-color:#e2e8f0">
        <div class="card-header"><i class="bi bi-person-check text-primary me-2"></i>Keputusan Seleksi</div>
        <div class="card-body" style="padding:24px">
            <div class="mb-4">
                <h6 class="fw-bold mb-3" style="font-size:14px">Terima Lamaran</h6>
                <form method="POST" action="{{ route('company.applications.accept', $application) }}">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13.5px">Penanggung Jawab dari Perusahaan <span style="font-weight:400;color:#94a3b8">(opsional)</span></label>
                            <select name="company_supervisor_id" class="form-select" style="font-size:13.5px">
                                <option value="">Pilih penanggung jawab perusahaan</option>
                                <option value="{{ auth()->id() }}" {{ old('company_supervisor_id') == auth()->id() ? 'selected' : '' }}>{{ auth()->user()->name }} (Anda)</option>
                            </select>
                            <div class="form-text">Project ini saat ini memakai akun perusahaan yang sedang login sebagai penanggung jawab default.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13.5px">Catatan Penerimaan</label>
                            <textarea name="notes" rows="2" class="form-control" style="font-size:13.5px" placeholder="Instruksi atau informasi untuk siswa saat mulai magang.">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="p-3 mb-3 rounded-3" style="background:{{ $remaining > 0 ? '#f0fdf4' : '#fef2f2' }};border:1px solid {{ $remaining > 0 ? '#d1fae5' : '#fecaca' }};font-size:13px;color:{{ $remaining > 0 ? '#065f46' : '#991b1b' }}">
                        <i class="bi bi-info-circle me-2"></i>
                        Menerima lamaran akan otomatis membuat data magang aktif dan mengirim notifikasi ke siswa.
                        <strong>Sisa kuota: {{ $remaining }} slot.</strong>
                        @if($remaining <= 0)
                        Program ini sudah penuh. Tombol terima tetap ditampilkan sebagai peringatan, tetapi controller akan menolak proses penerimaan.
                        @endif
                    </div>
                    <button type="submit" class="btn {{ $remaining > 0 ? 'btn-success' : 'btn-outline-danger' }} px-4" onclick="return confirm('Terima lamaran dari {{ addslashes($application->student->name ?? 'pelamar') }}?')"><i class="bi bi-check-circle-fill me-2"></i>Terima Lamaran</button>
                </form>
            </div>

            <div class="border-top pt-4">
                <h6 class="fw-bold mb-3" style="font-size:14px">Tolak Lamaran</h6>
                <form method="POST" action="{{ route('company.applications.reject', $application) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13.5px">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" rows="3" class="form-control @error('rejection_reason') is-invalid @enderror" style="font-size:13.5px" placeholder="Berikan alasan penolakan yang jelas dan sopan." required minlength="10">{{ old('rejection_reason') }}</textarea>
                        @error('rejection_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-outline-danger px-4" onclick="return confirm('Tolak lamaran ini? Siswa akan mendapat notifikasi.')"><i class="bi bi-x-circle me-2"></i>Tolak Lamaran</button>
                </form>
            </div>
        </div>
    </div>
    @else
    <a href="{{ route('company.applications.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
    @endif
</div>

<div class="col-xl-4 d-flex flex-column gap-3">
    @php 
        $studentUser = $application->student;
    @endphp
    <div class="card">
        <div class="card-header"><i class="bi bi-person-fill text-primary me-2"></i>Profil Pelamar</div>
        <div class="card-body" style="padding:20px">
            <div class="text-center mb-3">
                @php 
                    $studentPhoto = $studentProfile?->photo;
                    $photoExists = $studentPhoto && file_exists(public_path('storage/' . $studentPhoto));
                @endphp
                @if($photoExists)
                <img src="{{ asset('storage/' . $studentPhoto) }}" class="rounded-3 mb-2" style="width:64px;height:64px;object-fit:cover" alt="Foto">
                @else
                <div style="width:64px;height:64px;border-radius:16px;background:#eff6ff;color:#1a56db;font-size:24px;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 8px">{{ $studentUser ? strtoupper(substr($studentUser->name, 0, 1)) : '?' }}</div>
                @endif
                <div style="font-size:15px;font-weight:700;color:#0f172a">{{ $studentUser?->name ?? 'Nama Tidak Tersedia' }}</div>
                <div style="font-size:12.5px;color:#94a3b8">{{ $studentProfile?->nis ?? '-' }}</div>
            </div>
            <div class="d-flex flex-column gap-2" style="font-size:13px">
                <div class="d-flex justify-content-between"><span style="color:#94a3b8">Sekolah</span><span style="font-weight:500;text-align:right;max-width:160px">{{ $studentProfile?->school->name ?? '-' }}</span></div>
                <div class="d-flex justify-content-between"><span style="color:#94a3b8">Kelas</span><span style="font-weight:500">{{ $studentProfile?->class ?? '-' }}</span></div>
                <div class="d-flex justify-content-between"><span style="color:#94a3b8">Jurusan</span><span style="font-weight:500;text-align:right;max-width:160px">{{ $studentProfile?->major ?? '-' }}</span></div>
                @if($studentProfile?->phone)
                <div class="d-flex justify-content-between"><span style="color:#94a3b8">Telepon</span><span style="font-weight:500">{{ $studentProfile->phone }}</span></div>
                @endif
                @if($studentProfile?->gender)
                <div class="d-flex justify-content-between"><span style="color:#94a3b8">Jenis Kelamin</span><span style="font-weight:500">{{ $studentProfile->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</span></div>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="bi bi-briefcase-fill text-warning me-2"></i>Program Dilamar</div>
        <div class="card-body" style="font-size:13px;padding:16px 20px">
            <div style="font-weight:700;color:#0f172a;margin-bottom:4px">{{ $application->program->title }}</div>
            @if($application->program->field)
            <span style="font-size:12px;background:#eff6ff;color:#1a56db;padding:3px 10px;border-radius:20px;font-weight:600">{{ $application->program->field }}</span>
            @endif
            <div class="mt-3 d-flex flex-column gap-1">
                <div class="d-flex justify-content-between"><span style="color:#94a3b8">Periode</span><span style="font-weight:500;font-size:12px">{{ \Carbon\Carbon::parse($application->program->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($application->program->end_date)->format('d M Y') }}</span></div>
                <div class="d-flex justify-content-between"><span style="color:#94a3b8">Sisa kuota</span><span style="font-weight:600;color:{{ $application->program->remainingQuota() > 0 ? '#10b981' : '#ef4444' }}">{{ $application->program->remainingQuota() }} slot</span></div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection
