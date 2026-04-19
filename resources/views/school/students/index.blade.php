@extends('layouts.app')

@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')
@section('page-subtitle', 'Seluruh siswa terdaftar di ' . (auth()->user()->schoolProfile?->name ?? 'Sekolah Anda'))

@section('content')

{{-- Toolbar --}}
<div class="card mb-4">
    <div class="card-body d-flex flex-wrap gap-3 align-items-end">
        <form method="GET" action="{{ route('school.students.index') }}" class="d-flex flex-wrap gap-2 flex-fill">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" style="max-width:220px;font-size:13.5px"
                   placeholder="Cari nama siswa…">
            <select name="major" class="form-select" style="max-width:200px;font-size:13.5px">
                <option value="">Semua Jurusan</option>
                @foreach($majors as $major)
                    <option value="{{ $major }}" {{ request('major')===$major?'selected':'' }}>{{ $major }}</option>
                @endforeach
            </select>
            <select name="status" class="form-select" style="max-width:160px;font-size:13.5px">
                <option value="">Semua Status</option>
                <option value="active"     {{ request('status')==='active'    ?'selected':'' }}>Sedang Magang</option>
                <option value="not_active" {{ request('status')==='not_active'?'selected':'' }}>Belum Magang</option>
            </select>
            <button class="btn btn-outline-secondary" style="font-size:13.5px">
                <i class="bi bi-search me-1"></i>Filter
            </button>
            @if(request()->hasAny(['search','major','status']))
                <a href="{{ route('school.students.index') }}" class="btn btn-outline-danger" style="font-size:13.5px">
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
                        <th>NIS</th>
                        <th>Kelas / Jurusan</th>
                        <th>Status Magang</th>
                        <th>Tempat Magang</th>
                        <th style="width:80px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    @php $activeInternship = $student->internships->first(); @endphp
                    <tr>
                        <td style="color:#94a3b8;font-size:12px">
                            {{ $students->firstItem() + $loop->index }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:34px;height:34px;border-radius:9px;background:#eff6ff;color:#1a56db;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">
                                    {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-size:13.5px;font-weight:600;color:#0f172a">{{ $student->user->name }}</div>
                                    <div style="font-size:11.5px;color:#94a3b8">{{ $student->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:13px">{{ $student->nis }}</td>
                        <td style="font-size:13px">
                            {{ $student->class ?? '—' }}
                            @if($student->major)
                                <div style="font-size:11.5px;color:#94a3b8">{{ $student->major }}</div>
                            @endif
                        </td>
                        <td>
                            @if($activeInternship)
                                <span class="badge-status bg-success-subtle text-success-emphasis">
                                    <i class="bi bi-circle-fill me-1" style="font-size:7px"></i>Aktif Magang
                                </span>
                            @else
                                <span class="badge-status bg-secondary-subtle text-secondary-emphasis">Belum Magang</span>
                            @endif
                        </td>
                        <td style="font-size:13px">
                            @if($activeInternship)
                                {{ Str::limit($activeInternship->application->program->company->name, 24) }}
                            @else
                                <span style="color:#cbd5e1">—</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('school.students.show', $student) }}"
                               class="btn btn-sm btn-outline-secondary py-1" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5" style="color:#94a3b8">
                            <i class="bi bi-people" style="font-size:36px;display:block;margin-bottom:8px"></i>
                            Tidak ada siswa yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($students->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2"
         style="padding:12px 20px">
        <div style="font-size:13px;color:#94a3b8">
            Menampilkan {{ $students->firstItem() }}–{{ $students->lastItem() }}
            dari {{ $students->total() }} siswa
        </div>
        {{ $students->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
