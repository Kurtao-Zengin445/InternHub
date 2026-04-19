@extends('layouts.app')

@section('title', 'Detail Siswa')
@section('page-title', 'Detail Siswa')
@section('page-subtitle', $student->user->name)

@section('content')

<div class="row g-3">

{{-- Sidebar profil --}}
<div class="col-xl-4 d-flex flex-column gap-3">

    <div class="card">
        <div class="card-body text-center" style="padding:28px">
            @if($student->photo)
                <img src="{{ asset('storage/'.$student->photo) }}" class="rounded-3 mb-3"
                     style="width:72px;height:72px;object-fit:cover" alt="Foto">
            @else
                <div style="width:72px;height:72px;border-radius:18px;background:#eff6ff;color:#1a56db;font-size:28px;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                    {{ strtoupper(substr($student->user->name, 0, 1)) }}
                </div>
            @endif
            <div style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:4px">
                {{ $student->user->name }}
            </div>
            <div style="font-size:13px;color:#94a3b8;margin-bottom:4px">{{ $student->nis }}</div>
            @if($activeInternship)
                <span class="badge-status bg-success-subtle text-success-emphasis">Aktif Magang</span>
            @else
                <span class="badge-status bg-secondary-subtle text-secondary-emphasis">Belum Magang</span>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="bi bi-person-lines-fill text-primary me-2"></i>Info Pribadi</div>
        <div class="card-body" style="font-size:13px;padding:16px 20px">
            <div class="d-flex flex-column gap-2">
                <div class="d-flex justify-content-between">
                    <span style="color:#94a3b8">Kelas</span>
                    <span style="font-weight:500">{{ $student->class ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span style="color:#94a3b8">Jurusan</span>
                    <span style="font-weight:500;text-align:right;max-width:160px">{{ $student->major ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span style="color:#94a3b8">Jenis Kelamin</span>
                    <span style="font-weight:500">
                        {{ $student->gender === 'male' ? 'Laki-laki' : ($student->gender === 'female' ? 'Perempuan' : '—') }}
                    </span>
                </div>
                @if($student->birth_place && $student->birth_date)
                <div class="d-flex justify-content-between">
                    <span style="color:#94a3b8">TTL</span>
                    <span style="font-weight:500;text-align:right;max-width:160px">
                        {{ $student->birth_place }},
                        {{ \Carbon\Carbon::parse($student->birth_date)->format('d M Y') }}
                    </span>
                </div>
                @endif
                @if($student->phone)
                <div class="d-flex justify-content-between">
                    <span style="color:#94a3b8">Telepon</span>
                    <span style="font-weight:500">{{ $student->phone }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between">
                    <span style="color:#94a3b8">Email</span>
                    <span style="font-weight:500;font-size:12px;text-align:right;max-width:160px">{{ $student->user->email }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Konten utama --}}
<div class="col-xl-8 d-flex flex-column gap-3">

    {{-- Info magang aktif --}}
    @if($activeInternship)
    <div class="card" style="border-color:#d1fae5;background:#f0fdf4">
        <div class="card-body" style="padding:20px 24px">
            <div style="font-size:13px;font-weight:700;color:#065f46;margin-bottom:8px">
                <i class="bi bi-person-workspace me-2"></i>Magang Aktif
            </div>
            <div style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:4px">
                {{ $activeInternship->application->program->title }}
            </div>
            <div style="font-size:13px;color:#16a34a;margin-bottom:10px">
                <i class="bi bi-building me-1"></i>{{ $activeInternship->application->program->company->name }}
            </div>
            <div class="d-flex flex-wrap gap-3" style="font-size:12.5px;color:#065f46">
                <span><i class="bi bi-calendar3 me-1"></i>
                    {{ \Carbon\Carbon::parse($activeInternship->start_date)->format('d M Y') }} –
                    {{ \Carbon\Carbon::parse($activeInternship->end_date)->format('d M Y') }}
                </span>
                <span><i class="bi bi-calendar-check me-1"></i>
                    Kehadiran: {{ $activeInternship->attendancePercentage() }}%
                </span>
                <span><i class="bi bi-journal-check me-1"></i>
                    Laporan: {{ $activeInternship->dailyReports()->where('status','approved')->count() }}
                    / {{ $activeInternship->dailyReports()->count() }}
                </span>
            </div>
        </div>
    </div>
    @endif

    {{-- Riwayat lamaran --}}
    <div class="card">
        <div class="card-header">
            <i class="bi bi-send-fill text-primary me-2"></i>Riwayat Lamaran
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Perusahaan</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->applications as $app)
                        <tr>
                            <td style="font-size:13px">{{ Str::limit($app->program->title, 30) }}</td>
                            <td style="font-size:13px">{{ Str::limit($app->program->company->name, 24) }}</td>
                            <td>
                                @php
                                    $am=['pending'=>['Menunggu','warning'],'accepted'=>['Diterima','success'],'rejected'=>['Ditolak','danger'],'cancelled'=>['Batal','secondary'],'reviewed'=>['Ditinjau','info']];
                                    [$al,$ac]=$am[$app->status]??[$app->status,'secondary'];
                                @endphp
                                <span class="badge-status bg-{{ $ac }}-subtle text-{{ $ac }}-emphasis">{{ $al }}</span>
                            </td>
                            <td style="font-size:12px;color:#94a3b8">
                                {{ \Carbon\Carbon::parse($app->applied_at)->format('d M Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-3" style="color:#94a3b8;font-size:13px">
                                Belum ada lamaran.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Riwayat magang & nilai --}}
    @foreach($student->internships as $internship)
    @php
        $finalScore = $internship->finalScore();
        $supEval    = $internship->supervisorEvaluation();
        $cmpEval    = $internship->companyEvaluation();
    @endphp
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>
                <i class="bi bi-briefcase-fill text-warning me-2"></i>
                {{ $internship->application->program->company->name }}
            </span>
            @php $smap=['active'=>['Aktif','success'],'completed'=>['Selesai','primary'],'terminated'=>['Dihentikan','danger']]; [$sl,$sc]=$smap[$internship->status]??[$internship->status,'secondary']; @endphp
            <span class="badge-status bg-{{ $sc }}-subtle text-{{ $sc }}-emphasis">{{ $sl }}</span>
        </div>
        <div class="card-body" style="padding:18px 22px;font-size:13px">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div style="color:#94a3b8;margin-bottom:2px">Program</div>
                    <div style="font-weight:600">{{ $internship->application->program->title }}</div>
                </div>
                <div class="col-sm-6">
                    <div style="color:#94a3b8;margin-bottom:2px">Periode</div>
                    <div style="font-weight:600">
                        {{ \Carbon\Carbon::parse($internship->start_date)->format('d M Y') }} –
                        {{ \Carbon\Carbon::parse($internship->end_date)->format('d M Y') }}
                    </div>
                </div>
                @if($finalScore !== null)
                <div class="col-sm-4 text-center p-2 rounded-2" style="background:#eff6ff;border:1px solid #bfdbfe">
                    <div style="font-size:22px;font-weight:800;color:#1e40af">{{ number_format($finalScore, 1) }}</div>
                    <div style="font-size:11px;color:#3b82f6">Nilai Akhir</div>
                </div>
                @endif
                @if($supEval)
                <div class="col text-center p-2 rounded-2" style="background:#f0fdf4;border:1px solid #d1fae5">
                    <div style="font-size:18px;font-weight:700;color:#065f46">{{ number_format($supEval->final_score, 1) }}</div>
                    <div style="font-size:11px;color:#16a34a">Nilai Sekolah</div>
                </div>
                @endif
                @if($cmpEval)
                <div class="col text-center p-2 rounded-2" style="background:#fef3c7;border:1px solid #fde68a">
                    <div style="font-size:18px;font-weight:700;color:#92400e">{{ number_format($cmpEval->final_score, 1) }}</div>
                    <div style="font-size:11px;color:#b45309">Nilai Perusahaan</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach

    <a href="{{ route('school.students.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>

</div>
</div>

@endsection
