@extends('layouts.app')

@section('title', $program->title)
@section('page-title', $program->title)
@section('page-subtitle', $program->company->name . ' - ' . ($program->field ?? 'Program Magang'))

@section('content')
<div class="row g-3">
<div class="col-xl-8">
    @php
        $statusMap = [
            'draft' => ['Draft', 'secondary', '#f1f5f9', '#475569'],
            'open' => ['Pendaftaran Dibuka', 'success', '#f0fdf4', '#065f46'],
            'closed' => ['Pendaftaran Ditutup', 'warning', '#fffbeb', '#92400e'],
            'completed' => ['Program Selesai', 'primary', '#eff6ff', '#1e40af'],
        ];
        [$statusLabel, $statusColor, $statusBackground, $statusText] = $statusMap[$program->status] ?? [$program->status, 'secondary', '#f8fafc', '#475569'];
    @endphp
    <div class="card mb-3" style="background:{{ $statusBackground }};border-color:transparent">
        <div class="card-body d-flex align-items-center justify-content-between gap-3" style="padding:18px 24px">
            <div>
                <div style="font-size:15px;font-weight:700;color:{{ $statusText }}">{{ $statusLabel }}</div>
                <div style="font-size:12.5px;color:{{ $statusText }};opacity:.7;margin-top:2px">Pendaftaran: {{ \Carbon\Carbon::parse($program->registration_start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($program->registration_end)->format('d M Y') }}</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if($program->status !== 'completed')
                <a href="{{ route('company.programs.edit', $program) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
                @endif
                @if($program->status === 'open')
                <form method="POST" action="{{ route('company.programs.close', $program) }}" onsubmit="return confirm('Tutup pendaftaran program ini?')">
                    @csrf
                    <button class="btn btn-sm btn-warning"><i class="bi bi-stop-circle me-1"></i>Tutup Pendaftaran</button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body" style="padding:28px">
            <div class="mb-4">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-bottom:8px">Deskripsi Program</div>
                <div style="font-size:14px;color:#1e293b;line-height:1.8;white-space:pre-wrap">{{ $program->description }}</div>
            </div>
            @if($program->requirements)
            <div class="pt-4 border-top">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-bottom:8px">Persyaratan Pendaftar</div>
                <div style="font-size:14px;color:#1e293b;line-height:1.8;white-space:pre-wrap">{{ $program->requirements }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-inbox-fill text-primary me-2"></i>Lamaran Masuk</span>
            <a href="{{ route('company.applications.index') }}?program_id={{ $program->id }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr><th>Siswa</th><th>Sekolah</th><th>Status</th><th>Tanggal</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($recentApplications as $application)
                        <tr>
                            <td><div style="font-size:13px;font-weight:600">{{ $application->student->user->name }}</div><div style="font-size:11.5px;color:#94a3b8">{{ $application->student->major }}</div></td>
                            <td style="font-size:13px">{{ Str::limit(optional($application->student->school)->name ?? '-', 28) }}</td>
                            <td>
                                @php
                                    $applicationMap = ['pending' => ['Pending', 'warning'], 'accepted' => ['Diterima', 'success'], 'rejected' => ['Ditolak', 'danger'], 'cancelled' => ['Batal', 'secondary'], 'reviewed' => ['Ditinjau', 'info']];
                                    [$applicationLabel, $applicationColor] = $applicationMap[$application->status] ?? [$application->status, 'secondary'];
                                @endphp
                                <span class="badge-status bg-{{ $applicationColor }}-subtle text-{{ $applicationColor }}-emphasis">{{ $applicationLabel }}</span>
                            </td>
                            <td style="font-size:12px;color:#94a3b8">{{ \Carbon\Carbon::parse($application->applied_at)->format('d M Y') }}</td>
                            <td><a href="{{ route('company.applications.show', $application) }}" class="btn btn-sm btn-outline-secondary py-0">Tinjau</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4" style="color:#94a3b8;font-size:13px">Belum ada lamaran untuk program ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="col-xl-4 d-flex flex-column gap-3">
    <div class="card">
        <div class="card-header"><i class="bi bi-bar-chart text-primary me-2"></i>Statistik Lamaran</div>
        <div class="card-body" style="font-size:13px;padding:16px 20px">
            @foreach(['pending' => ['Menunggu', 'warning'], 'accepted' => ['Diterima', 'success'], 'rejected' => ['Ditolak', 'danger']] as $status => [$label, $color])
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span style="color:#94a3b8">{{ $label }}</span>
                <span class="badge bg-{{ $color }}-subtle text-{{ $color }}-emphasis rounded-pill">{{ $applicationStats[$status] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="bi bi-calendar3 text-warning me-2"></i>Jadwal</div>
        <div class="card-body" style="font-size:13px;padding:16px 20px">
            <div class="d-flex justify-content-between mb-2"><span style="color:#94a3b8">Mulai magang</span><span style="font-weight:600">{{ \Carbon\Carbon::parse($program->start_date)->format('d M Y') }}</span></div>
            <div class="d-flex justify-content-between mb-2"><span style="color:#94a3b8">Selesai magang</span><span style="font-weight:600">{{ \Carbon\Carbon::parse($program->end_date)->format('d M Y') }}</span></div>
            <div class="d-flex justify-content-between mb-2 pt-2 border-top mt-1"><span style="color:#94a3b8">Daftar dibuka</span><span style="font-weight:600">{{ \Carbon\Carbon::parse($program->registration_start)->format('d M Y') }}</span></div>
            <div class="d-flex justify-content-between"><span style="color:#94a3b8">Daftar ditutup</span><span style="font-weight:600">{{ \Carbon\Carbon::parse($program->registration_end)->format('d M Y') }}</span></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="bi bi-people text-success me-2"></i>Kuota Peserta</div>
        <div class="card-body text-center" style="padding:20px">
            @php
                $accepted = $applicationStats['accepted'];
                $remaining = $program->quota - $accepted;
                $percentage = $program->quota > 0 ? round(($accepted / $program->quota) * 100) : 0;
            @endphp
            <div style="font-size:40px;font-weight:800;color:#0f172a;letter-spacing:-1px">{{ $accepted }}</div>
            <div style="font-size:13px;color:#94a3b8;margin-bottom:12px">dari {{ $program->quota }} kuota terisi</div>
            <div class="progress mb-2" style="height:8px;border-radius:6px"><div class="progress-bar {{ $percentage >= 100 ? 'bg-danger' : 'bg-success' }}" style="width:{{ min($percentage, 100) }}%"></div></div>
            <div style="font-size:12px;color:{{ $remaining > 0 ? '#10b981' : '#ef4444' }};font-weight:600">{{ $remaining > 0 ? "$remaining slot tersisa" : 'Kuota penuh' }}</div>
        </div>
    </div>
</div>
</div>

@endsection
