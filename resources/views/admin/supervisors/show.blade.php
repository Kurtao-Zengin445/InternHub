@extends('layouts.app')

@section('title', 'Detail Pembimbing - ' . $supervisor->user->name)
@section('page-title', 'Detail Pembimbing')
@section('page-subtitle', $supervisor->user->email)

@section('content')

<div class="row g-3">
    <div class="col-xl-4">
        <div class="card text-center" style="padding:28px">
            <div style="width:72px;height:72px;border-radius:18px;background:#ede9fe;color:#4c1d95;font-size:28px;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                {{ strtoupper(substr($supervisor->user->name, 0, 1)) }}
            </div>
            <div style="font-size:17px;font-weight:700;color:#0f172a;margin-bottom:4px">{{ $supervisor->user->name }}</div>
            <div style="font-size:13px;color:#94a3b8;margin-bottom:12px">{{ $supervisor->user->email }}</div>

            <div class="d-flex justify-content-center gap-2 mb-4">
                <span class="badge-status" style="background:#ede9fe;color:#4c1d95">Pembimbing</span>
                <span class="badge-status {{ $supervisor->user->is_active ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                    {{ $supervisor->user->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            <div class="d-flex flex-column gap-2">
                <a href="{{ route('admin.supervisors.edit', $supervisor) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit Pembimbing
                </a>
                <a href="{{ route('admin.users.edit', $supervisor->user_id) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-person-gear me-2"></i>Edit Akun Login
                </a>
                <form method="POST" action="{{ route('admin.supervisors.destroy', $supervisor) }}"
                      onsubmit="return confirm('Hapus pembimbing ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash me-2"></i>Hapus Pembimbing
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><i class="bi bi-info-circle text-secondary me-2"></i>Informasi Singkat</div>
            <div class="card-body">
                <div class="d-flex flex-column gap-2" style="font-size:13px">
                    <div class="d-flex justify-content-between">
                        <span style="color:#94a3b8">Sekolah</span>
                        <span style="font-weight:500">{{ $supervisor->school->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:#94a3b8">NIP</span>
                        <span style="font-weight:500">{{ $supervisor->nip ?: '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:#94a3b8">Jabatan</span>
                        <span style="font-weight:500">{{ $supervisor->position ?: '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:#94a3b8">Telepon</span>
                        <span style="font-weight:500">{{ $supervisor->phone ?: '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:#94a3b8">Siswa Bimbingan</span>
                        <span style="font-weight:500">{{ $supervisor->internships_count }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-people-fill text-primary me-2"></i>Siswa Bimbingan Terbaru</div>
            <div class="card-body p-0">
                @forelse($recentInternships as $internship)
                <div class="d-flex justify-content-between align-items-center gap-3 px-4 py-3 border-bottom">
                    <div>
                        <div style="font-size:13.5px;font-weight:600;color:#0f172a">
                            {{ $internship->application->student->user->name }}
                        </div>
                        <div style="font-size:12px;color:#64748b">
                            {{ $internship->application->program->company->name }}
                        </div>
                    </div>
                    <span class="badge-status {{ $internship->status === 'active' ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                        {{ ucfirst($internship->status) }}
                    </span>
                </div>
                @empty
                <div class="text-center py-4" style="color:#94a3b8;font-size:13px">
                    Pembimbing ini belum memiliki siswa bimbingan.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
