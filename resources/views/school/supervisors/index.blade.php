@extends('layouts.app')

@section('title', 'Data Pembimbing')
@section('page-title', 'Data Pembimbing')
@section('page-subtitle', 'Kelola guru pembimbing magang sekolah')

@section('content')

<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('school.supervisors.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus-fill me-2"></i>Tambah Pembimbing
    </a>
</div>

<div class="row g-3">
    @forelse($supervisors as $supervisor)
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-body d-flex gap-4 align-items-start" style="padding:22px">

                {{-- Avatar --}}
                <div style="width:52px;height:52px;border-radius:14px;background:#fef3c7;color:#92400e;font-size:20px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    {{ strtoupper(substr($supervisor->user->name, 0, 1)) }}
                </div>

                {{-- Info --}}
                <div class="flex-fill">
                    <div style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:2px">
                        {{ $supervisor->user->name }}
                    </div>
                    <div style="font-size:13px;color:#64748b;margin-bottom:8px">
                        {{ $supervisor->position ?? 'Guru Pembimbing' }}
                        @if($supervisor->nip)
                            · NIP {{ $supervisor->nip }}
                        @endif
                    </div>

                    <div class="d-flex flex-wrap gap-3 mb-3" style="font-size:12.5px;color:#94a3b8">
                        <span><i class="bi bi-envelope me-1"></i>{{ $supervisor->user->email }}</span>
                        @if($supervisor->phone)
                            <span><i class="bi bi-telephone me-1"></i>{{ $supervisor->phone }}</span>
                        @endif
                    </div>

                    {{-- Badge siswa dibimbing --}}
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge-status bg-primary-subtle text-primary-emphasis">
                            <i class="bi bi-people me-1"></i>
                            {{ $supervisor->internships_count }} siswa dibimbing
                        </span>
                        @if($supervisor->user->is_active)
                            <span class="badge-status bg-success-subtle text-success-emphasis">Aktif</span>
                        @else
                            <span class="badge-status bg-danger-subtle text-danger-emphasis">Nonaktif</span>
                        @endif
                    </div>
                </div>

                {{-- Aksi --}}
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('school.supervisors.edit', $supervisor) }}"
                       class="btn btn-sm btn-outline-primary py-1" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form method="POST" action="{{ route('school.supervisors.destroy', $supervisor) }}"
                          onsubmit="return confirm('Hapus pembimbing {{ addslashes($supervisor->user->name) }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger py-1" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card text-center" style="padding:60px">
            <div style="font-size:48px;margin-bottom:12px">👨‍🏫</div>
            <h5 style="font-weight:700;color:#0f172a">Belum ada pembimbing terdaftar</h5>
            <p style="color:#64748b;font-size:14px;margin-bottom:20px">
                Tambahkan guru pembimbing untuk mendampingi siswa selama magang.
            </p>
            <a href="{{ route('school.supervisors.create') }}" class="btn btn-primary mx-auto" style="max-width:220px">
                <i class="bi bi-person-plus-fill me-2"></i>Tambah Pembimbing
            </a>
        </div>
    </div>
    @endforelse
</div>

@endsection
