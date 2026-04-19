@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Perbarui informasi akun dan data profil seperlunya')

@push('styles')
<style>
.profile-card { border-radius: 18px; overflow: hidden; }
.profile-photo {
    width: 110px; height: 110px; border-radius: 24px;
    object-fit: cover; border: 4px solid #fff; box-shadow: 0 10px 30px rgba(15,23,42,.12);
}
.profile-photo-placeholder {
    width: 110px; height: 110px; border-radius: 24px;
    display: flex; align-items: center; justify-content: center;
    font-size: 34px; font-weight: 700; color: #fff;
}
.readonly-box {
    border: 1px dashed #cbd5e1; border-radius: 14px; padding: 12px 14px; background: #f8fafc;
}
.readonly-label { font-size: 12px; color: #64748b; margin-bottom: 4px; }
.readonly-value { font-size: 14px; font-weight: 600; color: #0f172a; }
.section-title { font-size: 15px; font-weight: 700; color: #0f172a; }
.photo-actions { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
</style>
@endpush

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card profile-card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    @if($user->profilePhotoUrl())
                        <img
                            src="{{ $user->profilePhotoUrl() }}"
                            alt="Foto profil {{ $user->name }}"
                            class="profile-photo"
                            id="profilePhotoPreview"
                        >
                    @else
                        <div
                            class="profile-photo-placeholder"
                            id="profilePhotoPlaceholder"
                            style="background: {{ $roleMeta['accent'] }};"
                        >
                            {{ $user->initials() }}
                        </div>
                    @endif

                    <div>
                        <div class="badge rounded-pill px-3 py-2 mb-2" style="background: {{ $roleMeta['accent'] }}15; color: {{ $roleMeta['accent'] }};">
                            <i class="bi {{ $roleMeta['icon'] }} me-1"></i>{{ $roleMeta['label'] }}
                        </div>
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <div class="text-muted small">{{ $user->email }}</div>
                    </div>
                </div>

                <div class="small text-muted mb-3">
                    Anda bisa mengubah data seperlunya seperti kontak, informasi umum, dan foto profil. Data inti yang memengaruhi alur sistem ditampilkan sebagai informasi tetap.
                </div>

                <div class="d-grid gap-3">
                    @if($user->role === 'student')
                        <div class="readonly-box">
                            <div class="readonly-label">Sekolah</div>
                            <div class="readonly-value">{{ $profile->school->name ?? '-' }}</div>
                        </div>
                        <div class="readonly-box">
                            <div class="readonly-label">NIS / Kelas / Jurusan</div>
                            <div class="readonly-value">{{ $profile->nis }} / {{ $profile->class }} / {{ $profile->major }}</div>
                        </div>
                    @elseif($user->role === 'school')
                        <div class="readonly-box">
                            <div class="readonly-label">Nama Sekolah</div>
                            <div class="readonly-value">{{ $profile->name }}</div>
                        </div>
                        <div class="readonly-box">
                            <div class="readonly-label">NPSN</div>
                            <div class="readonly-value">{{ $profile->npsn ?: '-' }}</div>
                        </div>
                    @elseif($user->role === 'supervisor')
                        <div class="readonly-box">
                            <div class="readonly-label">Sekolah Asal</div>
                            <div class="readonly-value">{{ $profile->school->name ?? '-' }}</div>
                        </div>
                        <div class="readonly-box">
                            <div class="readonly-label">NIP</div>
                            <div class="readonly-value">{{ $profile->nip ?: '-' }}</div>
                        </div>
                    @elseif($user->role === 'company')
                        <div class="readonly-box">
                            <div class="readonly-label">Nama Perusahaan</div>
                            <div class="readonly-value">{{ $profile->name }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <div class="section-title mb-1">Ubah Profil</div>
                <p class="text-muted small mb-4">Simpan perubahan profil tanpa mengubah data inti yang memengaruhi proses utama sistem.</p>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nama Akun</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email Login</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label for="avatar" class="form-label fw-semibold">Foto Profil</label>
                            <input type="file" id="avatar" name="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
                            <div class="photo-actions mt-2">
                                <div class="form-text mb-0">Format gambar umum, maksimal 2 MB. Preview akan tampil sebelum disimpan.</div>
                                @if($user->avatar)
                                    <div class="form-check mb-0">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            value="1"
                                            id="remove_avatar"
                                            name="remove_avatar"
                                            @checked(old('remove_avatar'))
                                        >
                                        <label class="form-check-label text-danger small fw-semibold" for="remove_avatar">
                                            Hapus foto dan pakai avatar default
                                        </label>
                                    </div>
                                @endif
                            </div>
                            @error('avatar')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        @if($user->role === 'student')
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Nomor Telepon</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $profile->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="gender" class="form-label fw-semibold">Jenis Kelamin</label>
                                <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror">
                                    <option value="">Pilih jenis kelamin</option>
                                    <option value="male" @selected(old('gender', $profile->gender) === 'male')>Laki-laki</option>
                                    <option value="female" @selected(old('gender', $profile->gender) === 'female')>Perempuan</option>
                                </select>
                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="birth_place" class="form-label fw-semibold">Tempat Lahir</label>
                                <input type="text" id="birth_place" name="birth_place" value="{{ old('birth_place', $profile->birth_place) }}" class="form-control @error('birth_place') is-invalid @enderror">
                                @error('birth_place')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="birth_date" class="form-label fw-semibold">Tanggal Lahir</label>
                                <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', optional($profile->birth_date)->format('Y-m-d')) }}" class="form-control @error('birth_date') is-invalid @enderror">
                                @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">Alamat</label>
                                <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $profile->address) }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @elseif($user->role === 'school')
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Nomor Telepon</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $profile->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="institution_email" class="form-label fw-semibold">Email Sekolah</label>
                                <input type="email" id="institution_email" name="institution_email" value="{{ old('institution_email', $profile->email) }}" class="form-control @error('institution_email') is-invalid @enderror">
                                @error('institution_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="principal_name" class="form-label fw-semibold">Nama Kepala Sekolah</label>
                                <input type="text" id="principal_name" name="principal_name" value="{{ old('principal_name', $profile->principal_name) }}" class="form-control @error('principal_name') is-invalid @enderror">
                                @error('principal_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="school_type" class="form-label fw-semibold">Jenis Sekolah</label>
                                <input type="text" id="school_type" name="school_type" value="{{ old('school_type', $profile->school_type) }}" class="form-control @error('school_type') is-invalid @enderror">
                                @error('school_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">Alamat Sekolah</label>
                                <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $profile->address) }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @elseif($user->role === 'supervisor')
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Nomor Telepon</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $profile->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="position" class="form-label fw-semibold">Jabatan</label>
                                <input type="text" id="position" name="position" value="{{ old('position', $profile->position) }}" class="form-control @error('position') is-invalid @enderror">
                                @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @elseif($user->role === 'company')
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Nomor Telepon</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $profile->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="company_email" class="form-label fw-semibold">Email Perusahaan</label>
                                <input type="email" id="company_email" name="company_email" value="{{ old('company_email', $profile->email) }}" class="form-control @error('company_email') is-invalid @enderror">
                                @error('company_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person" class="form-label fw-semibold">Contact Person</label>
                                <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person', $profile->contact_person) }}" class="form-control @error('contact_person') is-invalid @enderror">
                                @error('contact_person')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person_phone" class="form-label fw-semibold">Nomor Contact Person</label>
                                <input type="text" id="contact_person_phone" name="contact_person_phone" value="{{ old('contact_person_phone', $profile->contact_person_phone) }}" class="form-control @error('contact_person_phone') is-invalid @enderror">
                                @error('contact_person_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="industry" class="form-label fw-semibold">Bidang Industri</label>
                                <input type="text" id="industry" name="industry" value="{{ old('industry', $profile->industry) }}" class="form-control @error('industry') is-invalid @enderror">
                                @error('industry')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="website" class="form-label fw-semibold">Website</label>
                                <input type="url" id="website" name="website" value="{{ old('website', $profile->website) }}" class="form-control @error('website') is-invalid @enderror">
                                @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">Alamat Perusahaan</label>
                                <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $profile->address) }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Deskripsi Singkat</label>
                                <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $profile->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const avatarInput = document.getElementById('avatar');
        const removeAvatarInput = document.getElementById('remove_avatar');
        const accentColor = @json($roleMeta['accent']);
        const initials = @json($user->initials());

        if (!avatarInput) {
            return;
        }

        const renderPlaceholder = () => {
            let previewImage = document.getElementById('profilePhotoPreview');
            let placeholder = document.getElementById('profilePhotoPlaceholder');

            if (previewImage) {
                placeholder = document.createElement('div');
                placeholder.id = 'profilePhotoPlaceholder';
                placeholder.className = 'profile-photo-placeholder';
                placeholder.style.background = accentColor;
                placeholder.textContent = initials;
                previewImage.replaceWith(placeholder);
            }

            if (placeholder) {
                placeholder.style.background = accentColor;
                placeholder.textContent = initials;
            }
        };

        avatarInput.addEventListener('change', (event) => {
            const [file] = event.target.files || [];

            if (!file || !file.type.startsWith('image/')) {
                return;
            }

            const previewUrl = URL.createObjectURL(file);
            let previewImage = document.getElementById('profilePhotoPreview');
            const placeholder = document.getElementById('profilePhotoPlaceholder');

            if (!previewImage) {
                previewImage = document.createElement('img');
                previewImage.id = 'profilePhotoPreview';
                previewImage.className = 'profile-photo';
                previewImage.alt = 'Preview foto profil';

                if (placeholder) {
                    placeholder.replaceWith(previewImage);
                }
            }

            previewImage.src = previewUrl;

            if (removeAvatarInput) {
                removeAvatarInput.checked = false;
            }
        });

        removeAvatarInput?.addEventListener('change', (event) => {
            if (event.target.checked) {
                avatarInput.value = '';
                renderPlaceholder();
            }
        });
    });
</script>
@endpush
