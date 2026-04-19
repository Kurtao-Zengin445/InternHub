@extends('layouts.app')

@section('title', isset($company) ? 'Edit Perusahaan' : 'Tambah Perusahaan')
@section('page-title', isset($company) ? 'Edit Perusahaan' : 'Tambah Perusahaan')
@section('page-subtitle', isset($company) ? $company->name : 'Daftarkan perusahaan mitra baru')

@section('content')

<div class="row justify-content-center">
<div class="col-xl-8 col-lg-10">

<form method="POST" enctype="multipart/form-data"
      action="{{ isset($company) ? route('admin.companies.update', $company) : route('admin.companies.store') }}">
    @csrf
    @if(isset($company)) @method('PUT') @endif

    {{-- ── Data Perusahaan ───────────────────── --}}
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-briefcase-fill text-warning me-2"></i>Data Perusahaan</div>
        <div class="card-body" style="padding:24px">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Nama Perusahaan <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $company->name ?? '') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Contoh: PT TechCorp Indonesia">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Industri</label>
                    <input type="text" name="industry" value="{{ old('industry', $company->industry ?? '') }}"
                           class="form-control" placeholder="Contoh: Teknologi Informasi">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Website</label>
                    <input type="url" name="website" value="{{ old('website', $company->website ?? '') }}"
                           class="form-control @error('website') is-invalid @enderror"
                           placeholder="https://perusahaan.com">
                    @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Alamat <span class="text-danger">*</span></label>
                    <textarea name="address" rows="2"
                              class="form-control @error('address') is-invalid @enderror"
                              placeholder="Alamat lengkap perusahaan">{{ old('address', $company->address ?? '') }}</textarea>
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $company->phone ?? '') }}"
                           class="form-control" placeholder="021-xxxxxxxx">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Email</label>
                    <input type="email" name="email" value="{{ old('email', $company->email ?? '') }}"
                           class="form-control" placeholder="hrd@perusahaan.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Nama Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $company->contact_person ?? '') }}"
                           class="form-control" placeholder="Nama PIC">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Telepon Contact Person</label>
                    <input type="text" name="contact_person_phone" value="{{ old('contact_person_phone', $company->contact_person_phone ?? '') }}"
                           class="form-control" placeholder="08xxxxxxxxxx">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Deskripsi Perusahaan</label>
                    <textarea name="description" rows="3" class="form-control"
                              placeholder="Gambaran singkat tentang perusahaan…">{{ old('description', $company->description ?? '') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Logo Perusahaan</label>
                    <input type="file" name="logo" accept="image/*" class="form-control">
                    @if(isset($company) && $company->logo)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $company->logo) }}" height="48" class="rounded" alt="Logo">
                            <span style="font-size:12px;color:#94a3b8;margin-left:8px">Logo saat ini</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Akun Login ────────────────────────── --}}
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-key-fill text-warning me-2"></i>Akun Login Perusahaan</div>
        <div class="card-body" style="padding:24px">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Nama Akun <span class="text-danger">*</span></label>
                    <input type="text" name="user_name"
                           value="{{ old('user_name', isset($company) ? $company->user->name : '') }}"
                           class="form-control @error('user_name') is-invalid @enderror"
                           placeholder="Nama untuk login">
                    @error('user_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Email Login <span class="text-danger">*</span></label>
                    <input type="email" name="user_email"
                           value="{{ old('user_email', isset($company) ? $company->user->email : '') }}"
                           class="form-control @error('user_email') is-invalid @enderror"
                           placeholder="email@login.com">
                    @error('user_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @if(!isset($company))
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Password <span class="text-danger">*</span></label>
                    <input type="password" name="user_password"
                           class="form-control @error('user_password') is-invalid @enderror"
                           placeholder="Minimal 8 karakter">
                    @error('user_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @else
                <div class="col-12">
                    <div class="alert alert-info py-2 px-3" style="font-size:13px">
                        <i class="bi bi-info-circle me-2"></i>
                        Ubah password melalui <a href="{{ route('admin.users.edit', $company->user_id) }}">Edit Pengguna</a>.
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-2"></i>
            {{ isset($company) ? 'Simpan Perubahan' : 'Tambah Perusahaan' }}
        </button>
        <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>

</div>
</div>

@endsection