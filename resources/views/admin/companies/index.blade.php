@extends('layouts.app')

@section('title', 'Manajemen Perusahaan')
@section('page-title', 'Manajemen Perusahaan')
@section('page-subtitle', 'Kelola data perusahaan mitra')

@section('content')

<div class="card mb-4">
    <div class="card-body d-flex flex-wrap gap-3 align-items-end">
        <form method="GET" action="{{ route('admin.companies.index') }}" class="d-flex flex-wrap gap-2 flex-fill">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" style="max-width:240px;font-size:13.5px"
                   placeholder="Cari nama perusahaan…">
            <select name="industry" class="form-select" style="max-width:200px;font-size:13.5px">
                <option value="">Semua Industri</option>
                @foreach($industries as $industry)
                    <option value="{{ $industry }}" {{ request('industry') === $industry ? 'selected' : '' }}>
                        {{ $industry }}
                    </option>
                @endforeach
            </select>
            <button class="btn btn-outline-secondary" style="font-size:13.5px">
                <i class="bi bi-search me-1"></i>Filter
            </button>
            @if(request()->hasAny(['search','industry']))
                <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-danger" style="font-size:13.5px">
                    <i class="bi bi-x"></i> Reset
                </a>
            @endif
        </form>
        <a href="{{ route('admin.companies.create') }}" class="btn btn-primary ms-auto">
            <i class="bi bi-briefcase me-2"></i>Tambah Perusahaan
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
                        <th>Perusahaan</th>
                        <th>Industri</th>
                        <th>Kontak</th>
                        <th class="text-center">Program</th>
                        <th style="width:110px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                    <tr>
                        <td style="color:#94a3b8;font-size:12px">{{ $companies->firstItem() + $loop->index }}</td>
                        <td>
                            <div style="font-size:13.5px;font-weight:600;color:#0f172a">{{ $company->name }}</div>
                            <div style="font-size:12px;color:#94a3b8">{{ Str::limit($company->address, 40) }}</div>
                        </td>
                        <td>
                            @if($company->industry)
                                <span class="badge-status bg-warning-subtle text-warning-emphasis">
                                    {{ $company->industry }}
                                </span>
                            @else
                                <span style="color:#94a3b8;font-size:13px">—</span>
                            @endif
                        </td>
                        <td style="font-size:13px">
                            {{ $company->contact_person ?? '—' }}
                            @if($company->phone)
                                <div style="font-size:11.5px;color:#94a3b8">{{ $company->phone }}</div>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">
                                {{ $company->programs_count }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.companies.show', $company) }}"
                                   class="btn btn-sm btn-outline-secondary py-1" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.companies.edit', $company) }}"
                                   class="btn btn-sm btn-outline-primary py-1" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.companies.destroy', $company) }}"
                                      onsubmit="return confirm('Hapus perusahaan {{ addslashes($company->name) }}?')">
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
                            <i class="bi bi-briefcase" style="font-size:36px;display:block;margin-bottom:8px"></i>
                            Tidak ada perusahaan yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($companies->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2" style="padding:12px 20px">
        <div style="font-size:13px;color:#94a3b8">
            Menampilkan {{ $companies->firstItem() }}–{{ $companies->lastItem() }} dari {{ $companies->total() }} perusahaan
        </div>
        {{ $companies->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection