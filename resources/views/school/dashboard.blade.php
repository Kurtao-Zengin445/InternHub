@extends('layouts.app')

@section('title', 'Dashboard Sekolah')
@section('page-title', 'Dashboard')
@section('page-subtitle', auth()->user()->schoolProfile->name ?? auth()->user()->name)

@section('content')

@php
$school = auth()->user()->schoolProfile;
$students = $school->students()
    ->whereHas('internships', function ($q) {
        $q->where('internships.status', 'active');
    })
    ->with([
        'internships' => function ($q) {
            $q->where('internships.status', 'active')
              ->with(['application.program.company']);
        },
        'user'
    ])
    ->get();
$activeCount = $students->filter(fn($s) => $s->internships->count() > 0)->count();
$totalStudents = $students->count();
$pendingApps = \App\Models\Application::where('school_id', $school->id)->where('status', 'pending')->count();
$completedCount = \App\Models\Internship::whereHas('application', fn($q) => $q->where('school_id', $school->id))->where('status', 'completed')->count();
$pendingEvaluations = \App\Models\Internship::whereHas('application', fn($q) => $q->where('school_id', $school->id))
    ->where('status', 'completed')
    ->whereDoesntHave('evaluations', fn($q) => $q->where('evaluator_type', 'school'))
    ->count();
@endphp

{{-- Stat cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#eff6ff;color:#1a56db">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalStudents }}</div>
                <div class="stat-label">Total Siswa</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#d1fae5;color:#065f46">
                <i class="bi bi-person-workspace"></i>
            </div>
            <div>
                <div class="stat-value">{{ $activeCount }}</div>
                <div class="stat-label">Sedang Magang</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#fef3c7;color:#92400e">
                <i class="bi bi-send-fill"></i>
            </div>
            <div>
                <div class="stat-value">{{ $pendingApps }}</div>
                <div class="stat-label">Lamaran Pending</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#d1fae5;color:#065f46">
                <i class="bi bi-patch-check-fill"></i>
            </div>
            <div>
                <div class="stat-value">{{ $completedCount }}</div>
                <div class="stat-label">Magang Selesai</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#fef3c7;color:#92400e">
                <i class="bi bi-clipboard-check"></i>
            </div>
            <div>
                <div class="stat-value">{{ $pendingEvaluations }}</div>
                <div class="stat-label">Menunggu Penilaian</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Daftar siswa aktif magang --}}
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-workspace text-primary me-2"></i>Siswa Sedang Magang
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Siswa</th>
                                <th>Perusahaan</th>
                                <th>Program</th>
                                <th>Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                            @php $internship = $student->internships->first(); @endphp
                            <tr>

                                <td style="color:#94a3b8;font-size:12px">
                                    {{ $loop->iteration }}
                                </td>
                                <td>
                                    <div style="font-size:13px;font-weight:600">{{ $student->user->name }}</div>
                                    <div style="font-size:11px;color:#94a3b8">{{ $student->class }} — {{ $student->major }}</div>
                                </td>
                                <td style="font-size:13px">
                                    {{ $internship->application?->program?->company?->name ?? '-' }}
                                </td>
                                <td style="font-size:12.5px;color:#64748b">
                                    {{ Str::limit($internship->application?->program?->title ?? '-', 28) }}
                                </td>
                                <td style="font-size:12px;color:#94a3b8;white-space:nowrap">
                                    {{ \Carbon\Carbon::parse($internship->end_date)->format('d M Y') ?? '-' }}<br>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4" style="color:#94a3b8;font-size:13px">
                                    Tidak ada siswa yang sedang magang.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Info sekolah + pembimbing --}}
    <div class="col-xl-4 d-flex flex-column gap-3">

        {{-- Profil sekolah --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-building text-success me-2"></i>Profil Sekolah</div>
            <div class="card-body">
                <div style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:4px">{{ $school->name }}</div>
                <div style="font-size:12.5px;color:#64748b;margin-bottom:12px">{{ $school->address }}</div>
                <div class="d-flex flex-column gap-2">
                    @if($school->phone)
                    <div style="font-size:12.5px;color:#64748b">
                        <i class="bi bi-telephone me-2 text-primary"></i>{{ $school->phone }}
                    </div>
                    @endif
                    @if($school->email)
                    <div style="font-size:12.5px;color:#64748b">
                        <i class="bi bi-envelope me-2 text-primary"></i>{{ $school->email }}
                    </div>
                    @endif
                    @if($school->principal_name)
                    <div style="font-size:12.5px;color:#64748b">
                        <i class="bi bi-person me-2 text-primary"></i>{{ $school->principal_name }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Daftar pembimbing --}}
        <div class="card flex-fill">
            <div class="card-header"><i class="bi bi-person-badge-fill text-warning me-2"></i>Pembimbing</div>
            <div class="card-body p-0">
                @forelse($school->supervisors()->with('user')->limit(5)->get() as $supervisor)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div style="width:34px;height:34px;border-radius:9px;background:#fef3c7;color:#92400e;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0">
                        {{ strtoupper(substr($supervisor->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#0f172a">{{ $supervisor->user->name }}</div>
                        <div style="font-size:11.5px;color:#94a3b8">{{ $supervisor->position }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-3" style="color:#94a3b8;font-size:13px">
                    Belum ada pembimbing terdaftar.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Penilaian siswa --}}
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clipboard-check text-info me-2"></i>Penilaian Siswa
                <a href="{{ route('school.evaluations.index') }}" class="btn btn-sm btn-outline-primary float-end">
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body">
                @php
                $recentEvaluations = \App\Models\Evaluation::where('evaluator_type', 'school')
                    ->whereHas('internship.application', fn($q) => $q->where('applications.school_id', $school->id))
                    ->with(['internship.student.user'])
                    ->latest('evaluated_at')
                    ->limit(3)
                    ->get();
                @endphp
                @forelse($recentEvaluations as $eval)
                <div class="d-flex align-items-center gap-3 mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                    <div style="width:34px;height:34px;border-radius:9px;background:#eff6ff;color:#1a56db;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0">
                        {{ strtoupper(substr($eval->internship->student->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-fill">
                        <div style="font-size:13px;font-weight:600;color:#0f172a">{{ $eval->internship->student->user->name }}</div>
                        <div style="font-size:11.5px;color:#94a3b8">{{ $eval->grade_letter }} — {{ number_format($eval->final_score, 1) }}</div>
                    </div>
                    <div style="font-size:12px;color:#94a3b8;text-align:right">
                        {{ \Carbon\Carbon::parse($eval->evaluated_at)->format('d M') }}
                    </div>
                </div>
                @empty
                <div class="text-center py-2" style="color:#94a3b8;font-size:13px">
                    Belum ada penilaian siswa.
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@endsection