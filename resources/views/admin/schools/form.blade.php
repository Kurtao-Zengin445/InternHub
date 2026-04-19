@extends('layouts.app')

@section('title', isset($school) ? 'Edit Sekolah' : 'Tambah Sekolah')
@section('page-title', isset($school) ? 'Edit Sekolah' : 'Tambah Sekolah')
@section('page-subtitle', isset($school) ? $school->name : 'Daftarkan sekolah mitra baru')

@section('content')

<div class="row justify-content-center">
<div class="col-xl-8 col-lg-10">

<form method="POST" enctype="multipart/form-data"
      action="{{ isset($school) ? route('admin.schools.update', $school) : route('admin.schools.store') }}">
    @csrf
    @if(isset($school)) @method('PUT') @endif

    {{-- ── Data Sekolah ──────────────────────── --}}
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-building text-success me-2"></i>Data Sekolah</div>
        <div class="card-body" style="padding:24px">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Nama Sekolah <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $school->name ?? '') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Contoh: SMK Negeri 1 Jakarta">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">NPSN</label>
                    <input type="text" name="npsn" value="{{ old('npsn', $school->npsn ?? '') }}"
                           class="form-control @error('npsn') is-invalid @enderror"
                           placeholder="8 digit NPSN">
                    @error('npsn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Jenis Sekolah</label>
                    <select name="school_type" class="form-select @error('school_type') is-invalid @enderror">
                        <option value="">— Pilih Jenis —</option>
                        @foreach(['SMK','SMA','MA','MAN'] as $type)
                            <option value="{{ $type }}" {{ old('school_type', $school->school_type ?? '') === $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Alamat <span class="text-danger">*</span></label>
                    <textarea name="address" rows="2"
                              class="form-control @error('address') is-invalid @enderror"
                              placeholder="Alamat lengkap sekolah">{{ old('address', $school->address ?? '') }}</textarea>
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $school->phone ?? '') }}"
                           class="form-control" placeholder="021-xxxxxxxx">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Email Sekolah</label>
                    <input type="email" name="email" value="{{ old('email', $school->email ?? '') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="email@sekolah.sch.id">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Nama Kepala Sekolah</label>
                    <input type="text" name="principal_name" value="{{ old('principal_name', $school->principal_name ?? '') }}"
                           class="form-control" placeholder="Nama beserta gelar">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Logo Sekolah</label>
                    <input type="file" name="logo" accept="image/*" class="form-control">
                    @if(isset($school) && $school->logo)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $school->logo) }}" height="48" class="rounded" alt="Logo">
                            <span style="font-size:12px;color:#94a3b8;margin-left:8px">Logo saat ini</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Akun Login ────────────────────────── --}}
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-key-fill text-warning me-2"></i>Akun Login Sekolah</div>
        <div class="card-body" style="padding:24px">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Nama Akun <span class="text-danger">*</span></label>
                    <input type="text" name="user_name"
                           value="{{ old('user_name', isset($school) ? $school->user->name : '') }}"
                           class="form-control @error('user_name') is-invalid @enderror"
                           placeholder="Nama untuk login">
                    @error('user_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Email Login <span class="text-danger">*</span></label>
                    <input type="email" name="user_email"
                           value="{{ old('user_email', isset($school) ? $school->user->email : '') }}"
                           class="form-control @error('user_email') is-invalid @enderror"
                           placeholder="email@login.com">
                    @error('user_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @if(!isset($school))
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Password <span class="text-danger">*</span></label>
                    <input type="password" name="user_password"
                           class="form-control @error('user_password') is-invalid @enderror"
                           placeholder="Minimal 8 karakter">
                    @error('user_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @endif
            </div>
            @if(isset($school))
                <div class="alert alert-info mt-3 py-2 px-3" style="font-size:13px">
                    <i class="bi bi-info-circle me-2"></i>
                    Untuk mengubah password akun sekolah, gunakan menu
                    <a href="{{ route('admin.users.edit', $school->user_id) }}">Edit Pengguna</a>.
                </div>
            @endif
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-2"></i>
            {{ isset($school) ? 'Simpan Perubahan' : 'Tambah Sekolah' }}
        </button>
        <a href="{{ route('admin.schools.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>

</div>
</div>

@endsection