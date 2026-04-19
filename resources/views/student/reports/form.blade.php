@extends('layouts.app')

@section('title', isset($report) ? 'Edit Laporan' : 'Buat Laporan Harian')
@section('page-title', isset($report) ? 'Edit Laporan' : 'Buat Laporan Harian')
@section('page-subtitle', isset($report)
    ? \Carbon\Carbon::parse($report->report_date)->translatedFormat('l, d F Y')
    : now()->translatedFormat('l, d F Y'))

@push('styles')
<style>
.char-count { font-size:11.5px; color:#94a3b8; text-align:right; margin-top:3px; }
.char-count.warn  { color:#f59e0b; }
.char-count.limit { color:#ef4444; }
.revision-box {
    border-radius: 12px;
    padding: 16px 20px;
    background: #fef2f2;
    border: 1.5px solid #fecaca;
    margin-bottom: 20px;
}
.photo-preview-wrap { position:relative; display:inline-block; }
.photo-preview-wrap .remove-photo {
    position: absolute;
    top: -6px; right: -6px;
    width: 22px; height: 22px;
    background: #ef4444;
    border: none;
    border-radius: 50%;
    color: #fff;
    font-size: 12px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    line-height: 1;
}
</style>
@endpush

@section('content')

<div class="row g-3">
<div class="col-xl-8">

    {{-- Feedback revisi (jika ada) --}}
    @if(isset($report) && $report->status === 'revision' && $report->feedback)
    <div class="revision-box">
        <div style="font-size:13px;font-weight:700;color:#991b1b;margin-bottom:6px">
            <i class="bi bi-arrow-repeat me-2"></i>Catatan Pembimbing — Perlu Revisi
        </div>
        <div style="font-size:13.5px;color:#7f1d1d;line-height:1.65">{{ $report->feedback }}</div>
    </div>
    @endif

    <form method="POST" enctype="multipart/form-data"
          action="{{ isset($report) ? route('student.reports.update', $report) : route('student.reports.store') }}"
          id="reportForm">
        @csrf
        @if(isset($report)) @method('PUT') @endif

        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-journal-text text-primary me-2"></i>
                {{ isset($report) ? 'Edit Laporan' : 'Laporan Harian Baru' }}
            </div>
            <div class="card-body" style="padding:24px">

                {{-- Tanggal --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold" style="font-size:13.5px">
                        Tanggal Laporan <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="report_date"
                           value="{{ old('report_date', isset($report) ? $report->report_date->format('Y-m-d') : today()->format('Y-m-d')) }}"
                           max="{{ today()->format('Y-m-d') }}"
                           class="form-control @error('report_date') is-invalid @enderror"
                           style="max-width:200px"
                           {{ isset($report) ? 'readonly' : '' }}>
                    @error('report_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if(isset($report))
                        <div class="form-text">Tanggal laporan tidak dapat diubah.</div>
                    @endif
                </div>

                {{-- Kegiatan --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold" style="font-size:13.5px">
                        Kegiatan yang Dilakukan <span class="text-danger">*</span>
                    </label>
                    <textarea name="activity" id="activityInput" rows="6"
                              class="form-control @error('activity') is-invalid @enderror"
                              placeholder="Deskripsikan kegiatan yang Anda lakukan hari ini secara detail…
Contoh: Mengerjakan fitur login menggunakan Laravel Breeze, melakukan debugging pada modul pembayaran, mengikuti daily standup meeting bersama tim."
                              oninput="countChars(this, 'activityCount', 20, 2000)">{{ old('activity', $report->activity ?? '') }}</textarea>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        @error('activity') <div class="text-danger" style="font-size:12.5px">{{ $message }}</div>
                        @else <div class="form-text">Minimal 20 karakter. Jelaskan secara detail.</div>
                        @enderror
                        <div class="char-count" id="activityCount">0 karakter</div>
                    </div>
                </div>

                {{-- Kendala --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold" style="font-size:13.5px">
                        Kendala yang Dihadapi
                        <span style="font-weight:400;color:#94a3b8;font-size:12.5px">(opsional)</span>
                    </label>
                    <textarea name="problems" id="problemsInput" rows="3"
                              class="form-control @error('problems') is-invalid @enderror"
                              placeholder="Adakah hambatan atau kesulitan yang Anda temui hari ini?"
                              oninput="countChars(this, 'problemsCount', 0, 1000)">{{ old('problems', $report->problems ?? '') }}</textarea>
                    <div class="d-flex justify-content-end mt-1">
                        <div class="char-count" id="problemsCount">0 karakter</div>
                    </div>
                </div>

                {{-- Solusi --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold" style="font-size:13.5px">
                        Solusi yang Dilakukan
                        <span style="font-weight:400;color:#94a3b8;font-size:12.5px">(opsional)</span>
                    </label>
                    <textarea name="solutions" id="solutionsInput" rows="3"
                              class="form-control @error('solutions') is-invalid @enderror"
                              placeholder="Bagaimana cara Anda mengatasi kendala tersebut?"
                              oninput="countChars(this, 'solutionsCount', 0, 1000)">{{ old('solutions', $report->solutions ?? '') }}</textarea>
                    <div class="d-flex justify-content-end mt-1">
                        <div class="char-count" id="solutionsCount">0 karakter</div>
                    </div>
                </div>

                {{-- Foto dokumentasi --}}
                <div class="mb-2">
                    <label class="form-label fw-semibold" style="font-size:13.5px">
                        Foto Dokumentasi
                        <span style="font-weight:400;color:#94a3b8;font-size:12.5px">(opsional)</span>
                    </label>

                    {{-- Foto existing (saat edit) --}}
                    @if(isset($report) && $report->photo)
                    <div class="mb-2">
                        <div class="photo-preview-wrap">
                            <img src="{{ asset('storage/'.$report->photo) }}"
                                 id="existingPhoto"
                                 style="height:100px;border-radius:10px;object-fit:cover;border:1px solid #e2e8f0"
                                 alt="Foto dokumentasi">
                            <button type="button" class="remove-photo" onclick="removeExistingPhoto()"
                                    title="Hapus foto">×</button>
                        </div>
                        <input type="hidden" name="keep_photo" id="keepPhotoInput" value="1">
                        <div style="font-size:12px;color:#94a3b8;margin-top:4px">Foto saat ini. Upload baru untuk mengganti.</div>
                    </div>
                    @endif

                    <input type="file" name="photo" id="photoInput" accept="image/*"
                           class="form-control @error('photo') is-invalid @enderror"
                           onchange="previewPhoto(this)">
                    @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror

                    {{-- Preview foto baru --}}
                    <div id="photoPreviewWrap" style="display:none;margin-top:10px">
                        <img id="photoPreview" style="max-height:160px;border-radius:10px;object-fit:cover;border:1px solid #e2e8f0" alt="Preview">
                        <button type="button" class="btn btn-sm btn-outline-danger mt-1 d-block"
                                onclick="clearPhoto()">
                            <i class="bi bi-x me-1"></i>Hapus foto
                        </button>
                    </div>

                    <div class="form-text">Maksimal 3MB. Format: JPG, PNG, WebP.</div>
                </div>

            </div>
        </div>

        {{-- Tombol aksi --}}
        <div class="card">
            <div class="card-body d-flex flex-wrap gap-2" style="padding:18px 24px">
                {{-- Draft --}}
                <button type="submit" name="send" value="0" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-floppy me-2"></i>Simpan Draft
                </button>

                {{-- Kirim ke pembimbing --}}
                <button type="submit" name="send" value="1" class="btn btn-primary px-4"
                        onclick="return confirmSend()">
                    <i class="bi bi-send-fill me-2"></i>
                    {{ isset($report) && $report->status === 'revision' ? 'Kirim Ulang' : 'Kirim ke Pembimbing' }}
                </button>

                <a href="{{ route('student.reports.index') }}" class="btn btn-outline-danger ms-auto">
                    Batal
                </a>
            </div>
        </div>

    </form>
</div>

{{-- ── Panduan ──────────────────────────────────── --}}
<div class="col-xl-4 d-flex flex-column gap-3">

    <div class="card">
        <div class="card-header"><i class="bi bi-lightbulb-fill text-warning me-2"></i>Tips Menulis Laporan</div>
        <div class="card-body" style="font-size:13px;line-height:1.7;padding:16px 20px">
            <div class="d-flex gap-2 mb-2">
                <span style="color:#1a56db;font-weight:700;flex-shrink:0">1.</span>
                <span>Jelaskan kegiatan secara <strong>spesifik</strong> — bukan hanya "mengerjakan tugas" tapi "mengerjakan fitur X menggunakan Y".</span>
            </div>
            <div class="d-flex gap-2 mb-2">
                <span style="color:#1a56db;font-weight:700;flex-shrink:0">2.</span>
                <span>Sebutkan <strong>tools atau teknologi</strong> yang digunakan dalam kegiatan.</span>
            </div>
            <div class="d-flex gap-2 mb-2">
                <span style="color:#1a56db;font-weight:700;flex-shrink:0">3.</span>
                <span>Kendala boleh kosong jika tidak ada, tapi jika ada <strong>tulis dengan jujur</strong>.</span>
            </div>
            <div class="d-flex gap-2">
                <span style="color:#1a56db;font-weight:700;flex-shrink:0">4.</span>
                <span>Foto dokumentasi sangat <strong>dianjurkan</strong> sebagai bukti kegiatan.</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="bi bi-calendar3 text-primary me-2"></i>Info Magang</div>
        <div class="card-body" style="font-size:13px;padding:16px 20px">
            @php $internship = auth()->user()->student->activeInternship(); @endphp
            @if($internship)
            <div style="font-weight:600;color:#0f172a;margin-bottom:4px">
                {{ $internship->application->program->company->name }}
            </div>
            <div style="color:#64748b;margin-bottom:10px">
                {{ $internship->application->program->title }}
            </div>
            <div class="d-flex justify-content-between border-top pt-2">
                <span style="color:#94a3b8">Periode</span>
                <span style="font-weight:500;font-size:12px">
                    {{ \Carbon\Carbon::parse($internship->start_date)->format('d M') }} –
                    {{ \Carbon\Carbon::parse($internship->end_date)->format('d M Y') }}
                </span>
            </div>
            <div class="d-flex justify-content-between mt-1">
                <span style="color:#94a3b8">Laporan dibuat</span>
                <span style="font-weight:500">{{ $internship->dailyReports()->count() }}</span>
            </div>
            @endif
        </div>
    </div>

</div>
</div>

@push('scripts')
<script>
// Hitung karakter
function countChars(el, countId, min, max) {
    const len  = el.value.length;
    const el2  = document.getElementById(countId);
    el2.textContent = len + ' karakter';
    el2.className   = 'char-count' + (len < min && len > 0 ? ' warn' : len > max ? ' limit' : '');
}

// Init hitungan karakter saat halaman load (saat edit)
document.addEventListener('DOMContentLoaded', () => {
    ['activityInput','problemsInput','solutionsInput'].forEach(id => {
        const el = document.getElementById(id);
        if (el && el.value) el.dispatchEvent(new Event('input'));
    });
});

// Preview foto baru
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('photoPreview').src = e.target.result;
            document.getElementById('photoPreviewWrap').style.display = '';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function clearPhoto() {
    document.getElementById('photoInput').value    = '';
    document.getElementById('photoPreviewWrap').style.display = 'none';
    document.getElementById('photoPreview').src    = '';
}

function removeExistingPhoto() {
    document.getElementById('existingPhoto').style.display   = 'none';
    document.getElementById('keepPhotoInput').value = '0';
}

// Konfirmasi kirim
function confirmSend() {
    const activity = document.getElementById('activityInput').value.trim();
    if (activity.length < 20) {
        alert('Kegiatan harus diisi minimal 20 karakter sebelum dikirim.');
        return false;
    }
    return confirm('Kirim laporan ke pembimbing? Pastikan semua isian sudah benar.');
}
</script>
@endpush

@endsection
