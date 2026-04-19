<div class="row justify-content-center">
<div class="col-xl-8 col-lg-10">
<form method="POST" action="{{ isset($program) ? route('company.programs.update', $program) : route('company.programs.store') }}">
    @csrf
    @if(isset($program)) @method('PUT') @endif

    <div class="card mb-3">
        <div class="card-header d-flex align-items-center gap-2">
            <a href="{{ route('company.programs.index') }}" class="btn btn-sm btn-outline-secondary py-0 px-2"><i class="bi bi-arrow-left"></i></a>
            <i class="bi bi-briefcase-fill text-warning me-1"></i>Informasi Program
        </div>
        <div class="card-body" style="padding:24px">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Judul Program <span class="text-danger">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $program->title ?? '') }}" class="form-control @error('title') is-invalid @enderror" placeholder="Contoh: Magang Web Developer, Magang Administrasi Keuangan">
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Bidang / Divisi</label>
                    <input type="text" name="field" value="{{ old('field', $program->field ?? '') }}" class="form-control" placeholder="Contoh: IT, Akuntansi, Marketing">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Kuota Peserta <span class="text-danger">*</span></label>
                    <input type="number" name="quota" min="1" max="100" value="{{ old('quota', $program->quota ?? 1) }}" class="form-control @error('quota') is-invalid @enderror">
                    @error('quota') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Deskripsi Program <span class="text-danger">*</span></label>
                    <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Jelaskan gambaran program magang ini: tugas yang akan dikerjakan, lingkungan kerja, dan apa yang akan dipelajari peserta.">{{ old('description', $program->description ?? '') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Persyaratan Pendaftar</label>
                    <textarea name="requirements" rows="4" class="form-control" placeholder="Contoh:&#10;- Siswa aktif jurusan RPL atau TKJ&#10;- Memahami dasar HTML, CSS, JavaScript&#10;- Memiliki laptop pribadi&#10;- Bersedia hadir full-time">{{ old('requirements', $program->requirements ?? '') }}</textarea>
                    <div class="form-text">Gunakan format daftar untuk memudahkan pembacaan.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-calendar3 text-primary me-2"></i>Jadwal</div>
        <div class="card-body" style="padding:24px">
            <div class="row g-3">
                <div class="col-12"><div class="section-divider">Masa Magang</div></div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Tanggal Mulai Magang <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', isset($program) ? $program->start_date->format('Y-m-d') : '') }}" class="form-control @error('start_date') is-invalid @enderror" id="startDate" onchange="updateDateHints()">
                    @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Tanggal Selesai Magang <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', isset($program) ? $program->end_date->format('Y-m-d') : '') }}" class="form-control @error('end_date') is-invalid @enderror" id="endDate" onchange="updateDateHints()">
                    @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12" id="durationHint" style="display:none">
                    <div class="p-2 rounded-2" style="background:#eff6ff;font-size:12.5px;color:#1e40af"><i class="bi bi-clock me-1"></i>Durasi magang: <strong id="durationText"></strong></div>
                </div>
                <div class="col-12"><div class="section-divider mt-2">Masa Pendaftaran</div></div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Pendaftaran Dibuka <span class="text-danger">*</span></label>
                    <input type="date" name="registration_start" value="{{ old('registration_start', isset($program) ? $program->registration_start?->format('Y-m-d') : '') }}" class="form-control @error('registration_start') is-invalid @enderror">
                    @error('registration_start') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Pendaftaran Ditutup <span class="text-danger">*</span></label>
                    <input type="date" name="registration_end" value="{{ old('registration_end', isset($program) ? $program->registration_end?->format('Y-m-d') : '') }}" class="form-control @error('registration_end') is-invalid @enderror">
                    @error('registration_end') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="date-range-hint">Harus sebelum atau sama dengan tanggal mulai magang.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><i class="bi bi-eye text-success me-2"></i>Status Publikasi</div>
        <div class="card-body" style="padding:20px 24px">
            <div class="d-flex gap-4 flex-wrap">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" value="draft" id="statusDraft" {{ old('status', $program->status ?? 'draft') === 'draft' ? 'checked' : '' }}>
                    <label class="form-check-label" for="statusDraft" style="font-size:13.5px"><strong>Draft</strong><span style="color:#94a3b8;display:block;font-size:12px">Tersimpan, belum dipublikasi</span></label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" value="open" id="statusOpen" {{ old('status', $program->status ?? '') === 'open' ? 'checked' : '' }}>
                    <label class="form-check-label" for="statusOpen" style="font-size:13.5px"><strong>Publikasi Sekarang</strong><span style="color:#94a3b8;display:block;font-size:12px">Langsung menerima pendaftar</span></label>
                </div>
                @if(isset($program) && $program->status === 'closed')
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" value="closed" id="statusClosed" checked>
                    <label class="form-check-label" for="statusClosed" style="font-size:13.5px"><strong>Ditutup</strong><span style="color:#94a3b8;display:block;font-size:12px">Tidak menerima pendaftar baru</span></label>
                </div>
                @endif
            </div>
            @error('status') <div class="text-danger mt-2" style="font-size:12.5px">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-2"></i>{{ isset($program) ? 'Simpan Perubahan' : 'Buat Program' }}</button>
        <a href="{{ route('company.programs.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
</div>
</div>
