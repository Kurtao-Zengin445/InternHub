@extends('layouts.app')

@section('title', isset($supervisor) ? 'Edit Pembimbing' : 'Tambah Pembimbing')
@section('page-title', isset($supervisor) ? 'Edit Pembimbing' : 'Tambah Pembimbing')
@section('page-subtitle', isset($supervisor) ? $supervisor->user->name : 'Daftarkan pembimbing sekolah baru')

@section('content')

@php
    $availableUserCount = isset($availableUsers) ? $availableUsers->count() : 0;
@endphp

<div class="row justify-content-center">
<div class="col-xl-8 col-lg-10">

<form method="POST"
      action="{{ isset($supervisor) ? route('admin.supervisors.update', $supervisor) : route('admin.supervisors.store') }}">
    @csrf
    @if(isset($supervisor)) @method('PUT') @endif

    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-person-badge-fill me-2" style="color:#4c1d95"></i>Data Pembimbing</div>
        <div class="card-body" style="padding:24px">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Sekolah <span class="text-danger">*</span></label>
                    <select name="school_id" class="form-select @error('school_id') is-invalid @enderror">
                        <option value="">- Pilih Sekolah -</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ (string) old('school_id', $supervisor->school_id ?? '') === (string) $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('school_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">NIP</label>
                    <input type="text" name="nip" value="{{ old('nip', $supervisor->nip ?? '') }}"
                           class="form-control @error('nip') is-invalid @enderror"
                           placeholder="Nomor Induk Pegawai">
                    @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Jabatan</label>
                    <input type="text" name="position" value="{{ old('position', $supervisor->position ?? '') }}"
                           class="form-control @error('position') is-invalid @enderror"
                           placeholder="Contoh: Guru Produktif RPL">
                    @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $supervisor->phone ?? '') }}"
                           class="form-control @error('phone') is-invalid @enderror"
                           placeholder="08xxxxxxxxxx">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-key-fill text-warning me-2"></i>Akun Login Pembimbing</div>
        <div class="card-body" style="padding:24px">
            @if(!isset($supervisor))
            <div class="mb-3">
                <label class="form-label fw-semibold d-block" style="font-size:13.5px">Sumber Akun Login <span class="text-danger">*</span></label>
                <div class="d-flex flex-wrap gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="account_mode" id="accountModeExisting" value="existing"
                               {{ old('account_mode', $availableUserCount > 0 ? 'existing' : 'new') === 'existing' ? 'checked' : '' }}>
                        <label class="form-check-label" for="accountModeExisting" style="font-size:13.5px">
                            Tautkan ke user supervisor yang sudah ada
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="account_mode" id="accountModeNew" value="new"
                               {{ old('account_mode', $availableUserCount === 0 ? 'new' : 'existing') === 'new' ? 'checked' : '' }}>
                        <label class="form-check-label" for="accountModeNew" style="font-size:13.5px">
                            Buat akun supervisor baru
                        </label>
                    </div>
                </div>
                @error('account_mode') <div class="text-danger" style="font-size:12.5px;margin-top:4px">{{ $message }}</div> @enderror
            </div>
            @endif

            <div class="row g-3">
                @if(!isset($supervisor))
                <div class="col-12" id="existingUserWrapper">
                    <label class="form-label fw-semibold" style="font-size:13.5px">User Supervisor yang Sudah Ada</label>
                    <select name="existing_user_id" class="form-select @error('existing_user_id') is-invalid @enderror">
                        <option value="">- Pilih User Supervisor -</option>
                        @foreach(($availableUsers ?? []) as $availableUser)
                            <option value="{{ $availableUser->id }}" {{ (string) old('existing_user_id') === (string) $availableUser->id ? 'selected' : '' }}>
                                {{ $availableUser->name }} - {{ $availableUser->email }}
                            </option>
                        @endforeach
                    </select>
                    @error('existing_user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if($availableUserCount === 0)
                        <div class="form-text">Belum ada user role supervisor yang siap ditautkan. Gunakan opsi buat akun baru.</div>
                    @endif
                </div>
                @endif

                <div id="newAccountFields" class="contents">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Nama Akun <span class="text-danger">*</span></label>
                    <input type="text" name="user_name"
                           value="{{ old('user_name', isset($supervisor) ? $supervisor->user->name : '') }}"
                           class="form-control @error('user_name') is-invalid @enderror"
                           placeholder="Nama untuk login">
                    @error('user_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13.5px">Email Login <span class="text-danger">*</span></label>
                    <input type="email" name="user_email"
                           value="{{ old('user_email', isset($supervisor) ? $supervisor->user->email : '') }}"
                           class="form-control @error('user_email') is-invalid @enderror"
                           placeholder="email@login.com">
                    @error('user_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @if(!isset($supervisor))
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
                        Ubah password melalui <a href="{{ route('admin.users.edit', $supervisor->user_id) }}">Edit Pengguna</a>.
                    </div>
                </div>
                @endif
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-2"></i>
            {{ isset($supervisor) ? 'Simpan Perubahan' : 'Tambah Pembimbing' }}
        </button>
        <a href="{{ route('admin.supervisors.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>

</div>
</div>

@if(!isset($supervisor))
@push('scripts')
<script>
function toggleSupervisorAccountMode() {
    const existingRadio = document.getElementById('accountModeExisting');
    const existingWrapper = document.getElementById('existingUserWrapper');
    const newFields = document.getElementById('newAccountFields');

    if (!existingRadio || !existingWrapper || !newFields) {
        return;
    }

    const useExisting = existingRadio.checked;

    existingWrapper.style.display = useExisting ? '' : 'none';
    newFields.style.display = useExisting ? 'none' : '';
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[name="account_mode"]').forEach(function (radio) {
        radio.addEventListener('change', toggleSupervisorAccountMode);
    });

    toggleSupervisorAccountMode();
});
</script>
@endpush
@endif

@endsection
