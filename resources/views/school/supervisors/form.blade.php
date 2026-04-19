@extends('layouts.app')

@section('title', isset($supervisor) ? 'Edit Pembimbing' : 'Tambah Pembimbing')
@section('page-title', isset($supervisor) ? 'Edit Pembimbing' : 'Tambah Pembimbing Baru')
@section('page-subtitle', isset($supervisor) ? $supervisor->user->name : 'Daftarkan guru pembimbing magang')

@section('content')

<div class="row justify-content-center">
<div class="col-xl-7 col-lg-9">

    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <a href="{{ route('school.supervisors.index') }}" class="btn btn-sm btn-outline-secondary py-0 px-2">
                <i class="bi bi-arrow-left"></i>
            </a>
            <i class="bi bi-person-badge-fill text-warning me-1"></i>
            {{ isset($supervisor) ? 'Edit: ' . $supervisor->user->name : 'Tambah Pembimbing' }}
        </div>
        <div class="card-body" style="padding:28px">
            <form method="POST"
                  action="{{ isset($supervisor) ? route('school.supervisors.update', $supervisor) : route('school.supervisors.store') }}">
                @csrf
                @if(isset($supervisor)) @method('PUT') @endif

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size:13.5px">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name"
                               value="{{ old('name', isset($supervisor) ? $supervisor->user->name : '') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Nama beserta gelar">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13.5px">
                            Email Login <span class="text-danger">*</span>
                        </label>
                        <input type="email" name="email"
                               value="{{ old('email', isset($supervisor) ? $supervisor->user->email : '') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="email@sekolah.sch.id">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13.5px">
                            Password @if(!isset($supervisor)) <span class="text-danger">*</span> @endif
                        </label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="{{ isset($supervisor) ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' }}">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13.5px">NIP</label>
                        <input type="text" name="nip"
                               value="{{ old('nip', $supervisor->nip ?? '') }}"
                               class="form-control @error('nip') is-invalid @enderror"
                               placeholder="Nomor Induk Pegawai">
                        @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13.5px">Jabatan / Mata Pelajaran</label>
                        <input type="text" name="position"
                               value="{{ old('position', $supervisor->position ?? '') }}"
                               class="form-control"
                               placeholder="Contoh: Guru Produktif RPL">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size:13.5px">Nomor Telepon</label>
                        <input type="text" name="phone"
                               value="{{ old('phone', $supervisor->phone ?? '') }}"
                               class="form-control"
                               placeholder="08xxxxxxxxxx">
                    </div>

                    <div class="col-12 pt-2 border-top">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-lg me-2"></i>
                                {{ isset($supervisor) ? 'Simpan Perubahan' : 'Tambah Pembimbing' }}
                            </button>
                            <a href="{{ route('school.supervisors.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

</div>
</div>

@endsection
