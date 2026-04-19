@extends('layouts.app')

@section('title', 'Detail Sekolah - ' . $school->name)
@section('page-title', 'Detail Sekolah')
@section('page-subtitle', ($school->school_type ?? 'Sekolah') . ' - ' . ($school->npsn ?? 'NPSN belum diisi'))

@section('content')

<div class="row g-3">
    <div class="col-xl-4">
        <div class="card mb-3" style="padding:24px">
            <div class="text-center mb-4">
                @if($school->logo)
                    <img src="{{ asset('storage/' . $school->logo) }}" height="72" class="rounded-3 mb-3" alt="Logo">
                @else
                    <div style="width:72px;height:72px;border-radius:18px;background:#d1fae5;color:#065f46;font-size:28px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                        <i class="bi bi-building"></i>
                    </div>
                @endif
                <div style="font-size:16px;font-weight:700;color:#0f172a">{{ $school->name }}</div>
                @if($school->school_type)
                    <span class="badge bg-success-subtle text-success-emphasis mt-1">{{ $school->school_type }}</span>
                @endif
            </div>

            <div class="d-flex flex-column gap-3" style="font-size:13px">
                @if($school->npsn)
                <div class="d-flex gap-2">
                    <i class="bi bi-hash text-primary" style="width:18px"></i>
                    <span>{{ $school->npsn }}</span>
                </div>
                @endif
                <div class="d-flex gap-2">
                    <i class="bi bi-geo-alt text-danger" style="width:18px;flex-shrink:0"></i>
                    <span style="color:#64748b">{{ $school->address }}</span>
                </div>
                @if($school->phone)
                <div class="d-flex gap-2">
                    <i class="bi bi-telephone text-success" style="width:18px"></i>
                    <span>{{ $school->phone }}</span>
                </div>
                @endif
                @if($school->email)
                <div class="d-flex gap-2">
                    <i class="bi bi-envelope text-warning" style="width:18px"></i>
                    <span>{{ $school->email }}</span>
                </div>
                @endif
                @if($school->principal_name)
                <div class="d-flex gap-2">
                    <i class="bi bi-person text-primary" style="width:18px"></i>
                    <span>{{ $school->principal_name }}</span>
                </div>
                @endif
            </div>

            <div class="d-flex flex-column gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.schools.edit', $school) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit Sekolah
                </a>
                <form method="POST" action="{{ route('admin.schools.destroy', $school) }}"
                      onsubmit="return confirm('Hapus sekolah ini? Semua data terkait akan terhapus.')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash me-2"></i>Hapus Sekolah
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-bar-chart text-primary me-2"></i>Statistik</div>
            <div class="card-body">
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div style="font-size:24px;font-weight:800;color:#1a56db">{{ $school->students_count }}</div>
                        <div style="font-size:12px;color:#94a3b8">Total Siswa</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:24px;font-weight:800;color:#10b981">{{ $activeInternships }}</div>
                        <div style="font-size:12px;color:#94a3b8">Sedang Magang</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:24px;font-weight:800;color:#f59e0b">{{ $school->applications_count }}</div>
                        <div style="font-size:12px;color:#94a3b8">Total Lamaran</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:24px;font-weight:800;color:#8b5cf6">{{ $school->supervisors->count() }}</div>
                        <div style="font-size:12px;color:#94a3b8">Pembimbing</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8 d-flex flex-column gap-3">
        <div class="card">
            <div class="card-header"><i class="bi bi-person-badge-fill text-warning me-2"></i>Pembimbing</div>
            <div class="card-body p-0">
                @forelse($school->supervisors as $sv)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div style="width:38px;height:38px;border-radius:10px;background:#fef3c7;color:#92400e;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0">
                        {{ strtoupper(substr($sv->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-fill">
                        <div style="font-size:13.5px;font-weight:600;color:#0f172a">{{ $sv->user->name }}</div>
                        <div style="font-size:12px;color:#94a3b8">{{ $sv->position ?? '-' }} - {{ $sv->nip ?? '-' }}</div>
                    </div>
                    <a href="{{ route('admin.users.show', $sv->user_id) }}" class="btn btn-sm btn-outline-secondary py-0">
                        <i class="bi bi-eye"></i>
                    </a>
                </div>
                @empty
                <div class="text-center py-4" style="color:#94a3b8;font-size:13px">
                    Belum ada pembimbing terdaftar.
                </div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-people-fill text-primary me-2"></i>Daftar Siswa</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>NIS</th>
                                <th>Kelas</th>
                                <th>Status Magang</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($school->students()->with(['user', 'internships' => fn($q) => $q->where('internships.status', 'active')])->take(10)->get() as $student)
                            <tr>
                                <td style="font-size:13.5px;font-weight:500">{{ $student->user->name }}</td>
                                <td style="font-size:13px">{{ $student->nis }}</td>
                                <td style="font-size:13px">{{ $student->class ?? '-' }}</td>
                                <td>
                                    @if($student->internships->count())
                                        <span class="badge-status bg-success-subtle text-success-emphasis">Aktif Magang</span>
                                    @else
                                        <span class="badge-status bg-secondary-subtle text-secondary-emphasis">Belum Magang</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3" style="color:#94a3b8;font-size:13px">
                                    Belum ada siswa terdaftar.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($school->students_count > 10)
                <div class="px-4 py-3 border-top" style="font-size:13px;color:#94a3b8">
                    Menampilkan 10 dari {{ $school->students_count }} siswa.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
