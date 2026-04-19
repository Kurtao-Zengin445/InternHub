@extends('layouts.app')

@section('title', isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('page-title', isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('page-subtitle', isset($user) ? 'Perbarui data akun pengguna' : 'Buat akun pengguna baru')

@section('content')

<div class="row justify-content-center">
    <div class="col-xl-7 col-lg-9">

        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary py-0 px-2 me-1">
                    <i class="bi bi-arrow-left"></i>
                </a>
                {{ isset($user) ? 'Edit: ' . $user->name : 'Tambah Pengguna Baru' }}
            </div>
            <div class="card-body" style="padding:28px">
                <form method="POST"
                      action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}">
                    @csrf
                    @if(isset($user)) @method('PUT') @endif

                    {{-- Nama --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13.5px">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Masukkan nama lengkap">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13.5px">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="contoh@email.com">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Role --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13.5px">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror"
                                {{ isset($user) && $user->id === auth()->id() ? 'disabled' : '' }}>
                            <option value="">— Pilih Role —</option>
                            @foreach(['admin'=>'Administrator','student'=>'Siswa','school'=>'Sekolah','supervisor'=>'Pembimbing','company'=>'Perusahaan'] as $val => $label)
                                <option value="{{ $val }}" {{ old('role', $user->role ?? '') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @if(isset($user) && $user->id === auth()->id())
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <div class="form-text text-warning">
                                <i class="bi bi-exclamation-triangle me-1"></i>Anda tidak dapat mengubah role akun sendiri.
                            </div>
                        @endif
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13.5px">
                            Password
                            @if(!isset($user)) <span class="text-danger">*</span> @endif
                        </label>
                        <div class="input-group">
                            <input type="password" name="password" id="passwordInput"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="{{ isset($user) ? 'Kosongkan jika tidak ingin mengubah' : 'Minimal 8 karakter' }}">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password') <div class="text-danger" style="font-size:12.5px;margin-top:4px">{{ $message }}</div> @enderror
                        @if(isset($user))
                            <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
                        @endif
                    </div>

                    {{-- Status (hanya saat edit, bukan diri sendiri) --}}
                    @if(isset($user) && $user->id !== auth()->id())
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:13.5px">Status Akun</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_active" value="1"
                                       id="statusActive" {{ old('is_active', $user->is_active) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="statusActive" style="font-size:13.5px">Aktif</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_active" value="0"
                                       id="statusInactive" {{ old('is_active', $user->is_active) == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="statusInactive" style="font-size:13.5px">Nonaktif</label>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Actions --}}
                    <div class="d-flex gap-2 pt-2 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-{{ isset($user) ? 'check-lg' : 'person-plus-fill' }} me-2"></i>
                            {{ isset($user) ? 'Simpan Perubahan' : 'Tambah Pengguna' }}
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('passwordInput');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
@endpush

@endsection