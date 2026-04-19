@extends('layouts.app')

@section('title', 'Detail Pengguna — ' . $user->name)
@section('page-title', 'Detail Pengguna')
@section('page-subtitle', $user->email)

@section('content')

<div class="row g-3">
    {{-- Profil utama --}}
    <div class="col-xl-4">
        <div class="card text-center" style="padding:28px">
            <div style="width:72px;height:72px;border-radius:18px;background:#eff6ff;color:#1a56db;font-size:28px;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div style="font-size:17px;font-weight:700;color:#0f172a;margin-bottom:4px">{{ $user->name }}</div>
            <div style="font-size:13px;color:#94a3b8;margin-bottom:12px">{{ $user->email }}</div>

            @php
                $roleMap = ['admin'=>['Administrator','#fef3c7','#92400e'],'student'=>['Siswa','#dbeafe','#1e40af'],'school'=>['Sekolah','#d1fae5','#065f46'],'supervisor'=>['Pembimbing','#ede9fe','#4c1d95'],'company'=>['Perusahaan','#fee2e2','#991b1b']];
                [$rLabel, $rBg, $rColor] = $roleMap[$user->role] ?? [$user->role,'#f1f5f9','#475569'];
            @endphp

            <div class="d-flex justify-content-center gap-2 mb-4">
                <span class="badge-status" style="background: {{ $rBg }}; color: {{ $rColor }};">{{ $rLabel }}</span>
                <span class="badge-status {{ $user->is_active ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            <div class="d-flex flex-column gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit Pengguna
                </a>
                @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                    @csrf @method('PATCH')
                    <button class="btn w-100 {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                        <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}-circle me-2"></i>
                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Akun
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                      onsubmit="return confirm('Hapus pengguna ini? Semua data terkait akan ikut terhapus.')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash me-2"></i>Hapus Pengguna
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><i class="bi bi-clock-history text-secondary me-2"></i>Info Akun</div>
            <div class="card-body">
                <div class="d-flex flex-column gap-2" style="font-size:13px">
                    <div class="d-flex justify-content-between">
                        <span style="color:#94a3b8">Terdaftar</span>
                        <span style="font-weight:500">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:#94a3b8">Diperbarui</span>
                        <span style="font-weight:500">{{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Profil detail per role --}}
    <div class="col-xl-8">
        @if($user->role === 'student' && $user->student)
        @php $student = $user->student; @endphp
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-mortarboard-fill text-primary me-2"></i>Data Siswa</div>
            <div class="card-body">
                <div class="row g-3" style="font-size:13.5px">
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">NIS</div>
                        <div style="font-weight:600">{{ $student->nis }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Sekolah</div>
                        <div style="font-weight:600">{{ $student->school->name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Kelas</div>
                        <div style="font-weight:600">{{ $student->class ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Jurusan</div>
                        <div style="font-weight:600">{{ $student->major ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Jenis Kelamin</div>
                        <div style="font-weight:600">{{ $student->gender === 'male' ? 'Laki-laki' : ($student->gender === 'female' ? 'Perempuan' : '-') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Telepon</div>
                        <div style="font-weight:600">{{ $student->phone ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
        @elseif($user->role === 'school' && $user->schoolProfile)
        @php $school = $user->schoolProfile; @endphp
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-building text-success me-2"></i>Data Sekolah</div>
            <div class="card-body">
                <div class="row g-3" style="font-size:13.5px">
                    <div class="col-12">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Nama Sekolah</div>
                        <div style="font-weight:600">{{ $school->name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">NPSN</div>
                        <div style="font-weight:600">{{ $school->npsn ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Jenis</div>
                        <div style="font-weight:600">{{ $school->school_type ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Kepala Sekolah</div>
                        <div style="font-weight:600">{{ $school->principal_name ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Telepon</div>
                        <div style="font-weight:600">{{ $school->phone ?? '-' }}</div>
                    </div>
                    <div class="col-12">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Alamat</div>
                        <div style="font-weight:600">{{ $school->address }}</div>
                    </div>
                </div>
            </div>
        </div>
        @elseif($user->role === 'company' && $user->company)
        @php $company = $user->company; @endphp
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-briefcase-fill text-warning me-2"></i>Data Perusahaan</div>
            <div class="card-body">
                <div class="row g-3" style="font-size:13.5px">
                    <div class="col-12">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Nama Perusahaan</div>
                        <div style="font-weight:600">{{ $company->name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Industri</div>
                        <div style="font-weight:600">{{ $company->industry ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Kontak</div>
                        <div style="font-weight:600">{{ $company->contact_person ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Telepon</div>
                        <div style="font-weight:600">{{ $company->phone ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Website</div>
                        <div style="font-weight:600">
                            @if($company->website)
                                <a href="{{ $company->website }}" target="_blank" rel="noopener">{{ $company->website }}</a>
                            @else — @endif
                        </div>
                    </div>
                    <div class="col-12">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Alamat</div>
                        <div style="font-weight:600">{{ $company->address }}</div>
                    </div>
                </div>
            </div>
        </div>
        @elseif($user->role === 'supervisor' && $user->supervisor)
        @php $supervisor = $user->supervisor; @endphp
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-person-badge-fill text-purple me-2" style="color:#4c1d95"></i>Data Pembimbing</div>
            <div class="card-body">
                <div class="row g-3" style="font-size:13.5px">
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">NIP</div>
                        <div style="font-weight:600">{{ $supervisor->nip ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Jabatan</div>
                        <div style="font-weight:600">{{ $supervisor->position ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Sekolah</div>
                        <div style="font-weight:600">{{ $supervisor->school->name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:#94a3b8;font-size:12px;margin-bottom:2px">Telepon</div>
                        <div style="font-weight:600">{{ $supervisor->phone ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="card mb-3">
            <div class="card-body text-center py-4" style="color:#94a3b8;font-size:13.5px">
                <i class="bi bi-person-circle" style="font-size:36px;display:block;margin-bottom:8px"></i>
                Profil detail belum tersedia untuk role ini.
            </div>
        </div>
        @endif

        {{-- Notifikasi terbaru --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-bell text-secondary me-2"></i>Notifikasi Terbaru</div>
            <div class="card-body p-0">
                @forelse($user->notifications()->latest()->take(5)->get() as $notif)
                <div class="d-flex gap-3 px-4 py-3 border-bottom {{ $notif->is_read ? '' : 'bg-blue-50' }}"
                     @if(!$notif->is_read) style="background:#f0f7ff" @endif>
                    <div style="width:8px;height:8px;border-radius:50%; background: {{ $notif->is_read ? '#cbd5e1' : '#1a56db' }}; margin-top:6px; flex-shrink:0"></div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#0f172a">{{ $notif->title }}</div>
                        <div style="font-size:12px;color:#64748b">{{ Str::limit($notif->message, 60) }}</div>
                        <div style="font-size:11px;color:#94a3b8;margin-top:2px">{{ $notif->sent_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4" style="color:#94a3b8;font-size:13px">Belum ada notifikasi.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection