@extends('layouts.app')

@section('title', 'Manajemen Sekolah')
@section('page-title', 'Manajemen Sekolah')
@section('page-subtitle', 'Kelola data sekolah mitra')

@section('content')

<div class="card mb-4">
    <div class="card-body d-flex flex-wrap gap-3 align-items-end">
        <form method="GET" action="{{ route('admin.schools.index') }}" class="d-flex flex-wrap gap-2 flex-fill">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" style="max-width:260px;font-size:13.5px"
                   placeholder="Cari nama sekolah atau NPSN…">
            <button class="btn btn-outline-secondary" style="font-size:13.5px">
                <i class="bi bi-search me-1"></i>Cari
            </button>
            @if(request('search'))
                <a href="{{ route('admin.schools.index') }}" class="btn btn-outline-danger" style="font-size:13.5px">
                    <i class="bi bi-x"></i> Reset
                </a>
            @endif
        </form>
        <a href="{{ route('admin.schools.create') }}" class="btn btn-primary ms-auto">
            <i class="bi bi-building-add me-2"></i>Tambah Sekolah
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Sekolah</th>
                        <th>NPSN</th>
                        <th>Kepala Sekolah</th>
                        <th class="text-center">Siswa</th>
                        <th>Kontak</th>
                        <th style="width:110px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schools as $school)
                    <tr>
                        <td style="color:#94a3b8;font-size:12px">{{ $schools->firstItem() + $loop->index }}</td>
                        <td>
                            <div style="font-size:13.5px;font-weight:600;color:#0f172a">{{ $school->name }}</div>
                            <div style="font-size:12px;color:#94a3b8">{{ Str::limit($school->address, 40) }}</div>
                        </td>
                        <td style="font-size:13px">{{ $school->npsn ?? '—' }}</td>
                        <td style="font-size:13px">{{ $school->principal_name ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">
                                {{ $school->students_count }}
                            </span>
                        </td>
                        <td style="font-size:13px">
                            {{ $school->phone ?? '—' }}
                            @if($school->email)
                                <div style="font-size:11.5px;color:#94a3b8">{{ $school->email }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.schools.show', $school) }}"
                                   class="btn btn-sm btn-outline-secondary py-1" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.schools.edit', $school) }}"
                                   class="btn btn-sm btn-outline-primary py-1" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.schools.destroy', $school) }}"
                                      onsubmit="return confirm('Hapus sekolah {{ addslashes($school->name) }}? Semua data terkait akan ikut terhapus.')">
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
                        <td colspan="7" class="text-center py-5" style="color:#94a3b8">
                            <i class="bi bi-building" style="font-size:36px;display:block;margin-bottom:8px"></i>
                            Tidak ada sekolah yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($schools->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2" style="padding:12px 20px">
        <div style="font-size:13px;color:#94a3b8">
            Menampilkan {{ $schools->firstItem() }}–{{ $schools->lastItem() }} dari {{ $schools->total() }} sekolah
        </div>
        {{ $schools->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection