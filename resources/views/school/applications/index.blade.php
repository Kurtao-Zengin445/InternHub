@extends('layouts.app')

@section('title', 'Lamaran Siswa')
@section('page-title', 'Lamaran Siswa')
@section('page-subtitle', 'Pantau status lamaran magang seluruh siswa')

@section('content')

{{-- Toolbar --}}
<div class="card mb-4">
    <div class="card-body d-flex flex-wrap gap-3 align-items-end">
        <form method="GET" action="{{ route('school.applications.index') }}" class="d-flex flex-wrap gap-2 flex-fill">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" style="max-width:220px;font-size:13.5px"
                   placeholder="Cari nama siswa…">
            <select name="status" class="form-select" style="max-width:160px;font-size:13.5px">
                <option value="">Semua Status</option>
                @foreach(['pending'=>'Menunggu','reviewed'=>'Ditinjau','accepted'=>'Diterima','rejected'=>'Ditolak','cancelled'=>'Dibatalkan'] as $v=>$l)
                    <option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-secondary" style="font-size:13.5px">
                <i class="bi bi-search me-1"></i>Filter
            </button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('school.applications.index') }}" class="btn btn-outline-danger" style="font-size:13.5px">
                    <i class="bi bi-x"></i> Reset
                </a>
            @endif
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Siswa</th>
                        <th>Program Magang</th>
                        <th>Perusahaan</th>
                        <th>Status</th>
                        <th>Tanggal Lamar</th>
                        <th style="width:80px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                    <tr>
                        <td style="color:#94a3b8;font-size:12px">
                            {{ $applications->firstItem() + $loop->index }}
                        </td>
                        <td>
                            <div style="font-size:13.5px;font-weight:600;color:#0f172a">
                                {{ $app->student->user->name }}
                            </div>
                            <div style="font-size:11.5px;color:#94a3b8">
                                {{ $app->student->class }} · {{ $app->student->major }}
                            </div>
                        </td>
                        <td style="font-size:13px">
                            {{ Str::limit($app->program->title, 32) }}
                        </td>
                        <td style="font-size:13px">
                            {{ Str::limit($app->program->company->name, 26) }}
                        </td>
                        <td>
                            @php
                                $am=['pending'=>['Menunggu','warning'],'reviewed'=>['Ditinjau','info'],'accepted'=>['Diterima','success'],'rejected'=>['Ditolak','danger'],'cancelled'=>['Dibatalkan','secondary']];
                                [$al,$ac]=$am[$app->status]??[$app->status,'secondary'];
                            @endphp
                            <span class="badge-status bg-{{ $ac }}-subtle text-{{ $ac }}-emphasis">{{ $al }}</span>
                        </td>
                        <td style="font-size:12px;color:#94a3b8;white-space:nowrap">
                            {{ \Carbon\Carbon::parse($app->applied_at)->format('d M Y') }}<br>
                            {{ \Carbon\Carbon::parse($app->applied_at)->diffForHumans() }}
                        </td>
                        <td>
                            <a href="{{ route('school.applications.show', $app) }}"
                               class="btn btn-sm btn-outline-secondary py-1" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5" style="color:#94a3b8">
                            <i class="bi bi-inbox" style="font-size:36px;display:block;margin-bottom:8px"></i>
                            Tidak ada lamaran yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($applications->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2"
         style="padding:12px 20px">
        <div style="font-size:13px;color:#94a3b8">
            Menampilkan {{ $applications->firstItem() }}–{{ $applications->lastItem() }}
            dari {{ $applications->total() }} lamaran
        </div>
        {{ $applications->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
