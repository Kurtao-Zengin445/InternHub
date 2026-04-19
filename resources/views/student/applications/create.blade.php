@extends('layouts.app')

@section('title', $program ? 'Lamar Program Magang' : 'Cari Program Magang')
@section('page-title', $program ? 'Lamar Program Magang' : 'Cari Program Magang')
@section('page-subtitle', $program ? $program->company->name . ' - ' . $program->title : 'Temukan program magang yang sesuai')

@push('styles')
<style>
.program-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px;
    transition: all .2s;
    cursor: pointer;
}
.program-card:hover,
.program-card.selected {
    border-color: #1a56db;
    background: #f8fbff;
    box-shadow: 0 4px 16px rgba(26,86,219,.1);
}
.program-card.selected {
    background: #eff6ff;
}
.quota-badge {
    font-size: 11.5px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
}
</style>
@endpush

@section('content')
@if(!$program)
<div class="row g-3">
    <div class="col-xl-9">
        <div class="card mb-4" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border-color:#bfdbfe">
            <div class="card-body d-flex align-items-center gap-3" style="padding:20px 24px">
                <div style="font-size:36px"><i class="bi bi-search"></i></div>
                <div>
                    <div style="font-size:15px;font-weight:700;color:#1e40af">Temukan Program Magang</div>
                    <div style="font-size:13px;color:#3b82f6;margin-top:2px">Pilih program yang sesuai dengan jurusan dan minatmu. Kamu bisa melamar lebih dari satu program selama kuota tersedia.</div>
                </div>
            </div>
        </div>

        @forelse($programs as $browseProgram)
        <div class="program-card mb-3" onclick="selectProgram({{ $browseProgram->id }})">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div style="flex:1;margin-right:12px">
                    <div style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:4px">{{ $browseProgram->title }}</div>
                    <div style="font-size:13px;color:#64748b"><i class="bi bi-building me-1"></i>{{ $browseProgram->company->name }}</div>
                </div>
                <span class="quota-badge" style="background:#d1fae5;color:#065f46;white-space:nowrap">{{ $browseProgram->remainingQuota() }} slot tersisa</span>
            </div>
            @if($browseProgram->field)
            <span style="font-size:12px;background:#eff6ff;color:#1a56db;padding:3px 10px;border-radius:20px;font-weight:600;margin-bottom:10px;display:inline-block">{{ $browseProgram->field }}</span>
            @endif
            <div class="d-flex flex-wrap gap-3 mt-2 mb-3" style="font-size:12.5px;color:#64748b">
                <span><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($browseProgram->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($browseProgram->end_date)->format('d M Y') }}</span>
                <span><i class="bi bi-clock me-1"></i>Daftar s.d. {{ \Carbon\Carbon::parse($browseProgram->registration_end)->format('d M Y') }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center gap-3">
                <div style="font-size:12.5px;color:#94a3b8">{{ Str::limit($browseProgram->description, 100) }}</div>
                <a href="{{ route('student.applications.create', ['program_id' => $browseProgram->id]) }}" class="btn btn-primary btn-sm ms-3" style="white-space:nowrap" onclick="event.stopPropagation()"><i class="bi bi-send me-1"></i>Lamar</a>
            </div>
        </div>
        @empty
        <div class="card text-center" style="padding:60px">
            <div style="font-size:48px;margin-bottom:12px"><i class="bi bi-briefcase"></i></div>
            <h5 style="font-weight:700;color:#0f172a">Tidak ada program yang tersedia</h5>
            <p style="color:#64748b;font-size:14px">Semua program magang yang tersedia sudah penuh atau belum ada yang membuka. Coba cek lagi nanti.</p>
        </div>
        @endforelse
    </div>

    <div class="col-xl-3">
        <div class="card" style="position:sticky;top:80px">
            <div class="card-header"><i class="bi bi-info-circle text-primary me-2"></i>Panduan Melamar</div>
            <div class="card-body" style="font-size:13px;line-height:1.7;padding:16px 20px">
                <div class="d-flex gap-2 mb-2"><span style="color:#1a56db;font-weight:700;flex-shrink:0">1.</span><span>Pilih program yang sesuai dengan jurusanmu.</span></div>
                <div class="d-flex gap-2 mb-2"><span style="color:#1a56db;font-weight:700;flex-shrink:0">2.</span><span>Tulis surat motivasi yang meyakinkan (minimal 100 karakter).</span></div>
                <div class="d-flex gap-2 mb-2"><span style="color:#1a56db;font-weight:700;flex-shrink:0">3.</span><span>Upload CV jika ada (PDF, DOC, DOCX maksimal 5 MB).</span></div>
                <div class="d-flex gap-2"><span style="color:#1a56db;font-weight:700;flex-shrink:0">4.</span><span>Tunggu konfirmasi dari perusahaan.</span></div>
            </div>
        </div>
    </div>
</div>
@else
<div class="row g-3">
<div class="col-xl-8">
    <div class="card mb-3" style="border-color:#bfdbfe;background:#eff6ff">
        <div class="card-body" style="padding:20px 24px">
            <div style="font-size:15px;font-weight:700;color:#1e40af;margin-bottom:6px">{{ $program->title }}</div>
            <div style="font-size:13px;color:#3b82f6;margin-bottom:8px"><i class="bi bi-building me-1"></i>{{ $program->company->name }} @if($program->field) - <span style="font-weight:600">{{ $program->field }}</span>@endif</div>
            <div class="d-flex flex-wrap gap-3" style="font-size:12.5px;color:#64748b">
                <span><i class="bi bi-calendar3 me-1 text-primary"></i>{{ \Carbon\Carbon::parse($program->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($program->end_date)->format('d M Y') }}</span>
                <span><i class="bi bi-people me-1 text-success"></i>{{ $program->remainingQuota() }} slot tersisa dari {{ $program->quota }}</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="bi bi-send-fill text-primary me-2"></i>Form Lamaran</div>
        <div class="card-body" style="padding:24px">
            <form method="POST" action="{{ route('student.applications.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Menampilkan error umum yang tidak tertangkap input spesifik --}}
                @if ($errors->any())
                    <div class="alert alert-danger mb-4" style="font-size:13px">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Terdapat kesalahan pada data Anda:
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <input type="hidden" name="internship_program_id" value="{{ $program->id }}">
                <div class="mb-4">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Surat Motivasi <span class="text-danger">*</span></label>
                    <textarea name="motivation_letter" rows="10" class="form-control @error('motivation_letter') is-invalid @enderror" placeholder="Ceritakan tentang dirimu, mengapa kamu tertarik dengan program ini, keahlian yang kamu miliki, dan apa yang ingin kamu pelajari selama magang.

Contoh pembuka:
Saya adalah siswa kelas XII jurusan Rekayasa Perangkat Lunak di SMK Negeri 1 Jakarta. Saya sangat tertarik untuk mengikuti program magang ini karena..." oninput="countMotivation(this)">{{ old('motivation_letter') }}</textarea>
                    <div class="d-flex justify-content-between mt-1 flex-wrap gap-2">
                        @error('motivation_letter')
                        <div class="text-danger" style="font-size:12.5px">{{ $message }}</div>
                        @else
                        <div class="form-text">Minimal 100 karakter. Tulis dengan bahasa yang sopan dan meyakinkan.</div>
                        @enderror
                        <div id="motivationCount" style="font-size:11.5px;color:#94a3b8">0 / 100 min</div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Curriculum Vitae (CV) <span style="font-weight:400;color:#94a3b8">(opsional)</span></label>
                    <input type="file" name="cv_file" accept=".pdf,.doc,.docx" class="form-control @error('cv_file') is-invalid @enderror">
                    @error('cv_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="form-text">Format: PDF, DOC, DOCX. Maksimal 5 MB.</div>
                </div>
                <div class="p-3 rounded-3 mb-4" style="background:#fffbeb;border:1px solid #fde68a;font-size:13px;color:#92400e"><i class="bi bi-exclamation-triangle me-2"></i>Pastikan data yang Anda masukkan sudah benar. Lamaran yang sudah dikirim tidak dapat diedit, hanya bisa dibatalkan selama masih berstatus <strong>Menunggu</strong>.</div>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary px-4" onclick="return validateMotivation()"><i class="bi bi-send-fill me-2"></i>Kirim Lamaran</button>
                    <a href="{{ route('student.applications.create') }}" class="btn btn-outline-secondary">Pilih Program Lain</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="col-xl-4 d-flex flex-column gap-3">
    <div class="card">
        <div class="card-header"><i class="bi bi-briefcase-fill text-warning me-2"></i>Detail Program</div>
        <div class="card-body" style="font-size:13px;padding:16px 20px">
            <div style="font-size:13.5px;color:#1e293b;line-height:1.65;margin-bottom:12px">{{ Str::limit($program->description, 200) }}</div>
            @if($program->requirements)
            <div class="pt-3 border-top">
                <div style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Persyaratan</div>
                <div style="font-size:12.5px;color:#475569;line-height:1.7;white-space:pre-wrap">{{ $program->requirements }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="bi bi-building text-primary me-2"></i>Tentang Perusahaan</div>
        <div class="card-body" style="font-size:13px;padding:16px 20px">
            <div style="font-weight:700;color:#0f172a;margin-bottom:4px">{{ $program->company->name }}</div>
            @if($program->company->industry)
            <span style="font-size:12px;background:#fef3c7;color:#92400e;padding:3px 10px;border-radius:20px;font-weight:600;margin-bottom:10px;display:inline-block">{{ $program->company->industry }}</span>
            @endif
            @if($program->company->description)
            <div style="color:#64748b;margin-top:8px;line-height:1.65">{{ Str::limit($program->company->description, 150) }}</div>
            @endif
            @if($program->company->website)
            <a href="{{ $program->company->website }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm mt-2 w-100"><i class="bi bi-globe me-1"></i>Kunjungi Website</a>
            @endif
        </div>
    </div>
</div>
</div>
@endif

@push('scripts')
<script>
function selectProgram(id) {
    window.location = `{{ route('student.applications.create') }}?program_id=${id}`;
}
function countMotivation(element) {
    const length = element.value.length;
    const counter = document.getElementById('motivationCount');
    counter.textContent = length + (length < 100 ? ' / 100 min' : ' karakter');
    counter.style.color = length < 100 ? '#f59e0b' : '#10b981';
}
function validateMotivation() {
    const value = document.querySelector('textarea[name="motivation_letter"]')?.value?.trim();
    if (value && value.length < 100) {
        alert('Surat motivasi minimal 100 karakter. Saat ini: ' + value.length + ' karakter.');
        return false;
    }
    return true;
}
document.addEventListener('DOMContentLoaded', () => {
    const textarea = document.querySelector('textarea[name="motivation_letter"]');
    if (textarea && textarea.value) textarea.dispatchEvent(new Event('input'));
});
</script>
@endpush

@endsection
