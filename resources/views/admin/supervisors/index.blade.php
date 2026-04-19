@extends('layouts.app')

@section('title', 'Manajemen Pembimbing')
@section('page-title', 'Manajemen Pembimbing')
@section('page-subtitle', 'Kelola data pembimbing sekolah dan akun loginnya')

@section('content')

<div class="card mb-4">
    <div class="card-body d-flex flex-wrap gap-3 align-items-end">
        <form method="GET" action="{{ route('admin.supervisors.index') }}" class="d-flex flex-wrap gap-2 flex-fill">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" style="max-width:240px;font-size:13.5px"
                   placeholder="Cari nama, email, NIP, sekolah">
            <select name="school_id" class="form-select" style="max-width:220px;font-size:13.5px">
                <option value="">Semua Sekolah</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ (string) request('school_id') === (string) $school->id ? 'selected' : '' }}>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>
            <button class="btn btn-outline-secondary" style="font-size:13.5px">
                <i class="bi bi-search me-1"></i>Filter
            </button>
            @if(request()->hasAny(['search', 'school_id']))
                <a href="{{ route('admin.supervisors.index') }}" class="btn btn-outline-danger" style="font-size:13.5px">
                    <i class="bi bi-x"></i> Reset
                </a>
            @endif
        </form>
        <a href="{{ route('admin.supervisors.create') }}" class="btn btn-primary ms-auto">
            <i class="bi bi-person-plus-fill me-2"></i>Tambah Pembimbing
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Pembimbing</th>
                        <th>Sekolah</th>
                        <th>Status Akun</th>
                        <th>Siswa Bimbingan</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supervisors as $supervisor)
                    <tr>
                        <td style="color:#94a3b8;font-size:12px">{{ $supervisors->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:36px;height:36px;border-radius:9px;background:#ede9fe;color:#4c1d95;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0">
                                    {{ strtoupper(substr($supervisor->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-size:13.5px;font-weight:600;color:#0f172a">{{ $supervisor->user->name }}</div>
                                    <div style="font-size:12px;color:#94a3b8">{{ $supervisor->user->email }}</div>
                                    <div style="font-size:12px;color:#64748b">{{ $supervisor->nip ?: 'NIP belum diisi' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size:13px;font-weight:600">{{ $supervisor->school->name }}</div>
                            <div style="font-size:12px;color:#94a3b8">{{ $supervisor->position ?: '-' }}</div>
                        </td>
                        <td>
                            @if($supervisor->user->is_active)
                                <span class="badge-status bg-success-subtle text-success-emphasis">Aktif</span>
                            @else
                                <span class="badge-status bg-danger-subtle text-danger-emphasis">Nonaktif</span>
                            @endif
                        </td>
                        <td style="font-size:13px">{{ $supervisor->internships_count }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.supervisors.show', $supervisor) }}"
                                   class="btn btn-sm btn-outline-secondary py-1" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.supervisors.edit', $supervisor) }}"
                                   class="btn btn-sm btn-outline-primary py-1" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.supervisors.destroy', $supervisor) }}"
                                      onsubmit="return confirm('Hapus pembimbing {{ addslashes($supervisor->user->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger py-1" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5" style="color:#94a3b8">
                            <i class="bi bi-person-badge" style="font-size:36px;display:block;margin-bottom:8px"></i>
                            Belum ada data pembimbing.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($supervisors->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2" style="padding:12px 20px">
        <div style="font-size:13px;color:#94a3b8">
            Menampilkan {{ $supervisors->firstItem() }}-{{ $supervisors->lastItem() }} dari {{ $supervisors->total() }} pembimbing
        </div>
        {{ $supervisors->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
