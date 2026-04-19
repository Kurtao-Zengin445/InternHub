@extends('layouts.app')

@section('title', 'Detail Peserta Magang')
@section('page-title', 'Detail Peserta Magang')
@section('page-subtitle', $internship->application->student->user->name . ' — ' . $internship->application->program->title)

@section('content')

@php
    $student         = $internship->application->student;
    $program         = $internship->application->program;
    $totalReports    = $internship->dailyReports->count();
    $approvedReports = $internship->dailyReports->where('status', 'approved')->count();
    $pct             = $internship->attendancePercentage();
    $daysRemaining   = now()->diffInDays($internship->end_date, false);
@endphp

<div class="row g-3">

{{-- Kolom kiri: info & aksi --}}
<div class="col-xl-4 d-flex flex-column gap-3">

    {{-- Profil peserta --}}
    <div class="card">
        <div class="card-body text-center" style="padding:28px">
            <div style="width:64px;height:64px;border-radius:16px;background:#ede9fe;color:#4c1d95;font-size:26px;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                {{ strtoupper(substr($student->user->name, 0, 1)) }}
            </div>
            <div style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:4px">
                {{ $student->user->name }}
            </div>
            <div style="font-size:13px;color:#64748b;margin-bottom:12px">
                {{ $student->nis }} · {{ $student->class }}
            </div>
            <div class="d-flex flex-column gap-2 text-start" style="font-size:13px">
                <div class="d-flex justify-content-between">
                    <span style="color:#94a3b8">Sekolah</span>
                    <span style="font-weight:500;text-align:right;max-width:160px">{{ $student->school->name }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span style="color:#94a3b8">Jurusan</span>
                    <span style="font-weight:500;text-align:right;max-width:160px">{{ $student->major ?? '-' }}</span>
                </div>
                @if($student->phone)
                <div class="d-flex justify-content-between">
                    <span style="color:#94a3b8">Telepon</span>
                    <span style="font-weight:500">{{ $student->phone }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Info program --}}
    <div class="card">
        <div class="card-header"><i class="bi bi-briefcase-fill text-warning me-2"></i>Info Program</div>
        <div class="card-body" style="font-size:13px;padding:16px 20px">
            <div style="font-weight:600;color:#0f172a;margin-bottom:8px">{{ $program->title }}</div>
            <div class="d-flex justify-content-between mb-1">
                <span style="color:#94a3b8">Mulai</span>
                <span style="font-weight:500">{{ \Carbon\Carbon::parse($internship->start_date)->format('d M Y') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span style="color:#94a3b8">Selesai</span>
                <span style="font-weight:500">{{ \Carbon\Carbon::parse($internship->end_date)->format('d M Y') }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span style="color:#94a3b8">Status</span>
                @php $smap=['active'=>['Aktif','success'],'completed'=>['Selesai','primary'],'terminated'=>['Dihentikan','danger']]; [$sl,$sc]=$smap[$internship->status]??[$internship->status,'secondary']; @endphp
                <span class="badge-status bg-{{ $sc }}-subtle text-{{ $sc }}-emphasis">{{ $sl }}</span>
            </div>
            @if($internship->status === 'active' && $daysRemaining > 0)
            <div class="mt-3 p-2 rounded-2 text-center" style="background:#eff6ff;font-size:12px;color:#1e40af">
                <strong>{{ $daysRemaining }}</strong> hari lagi selesai
            </div>
            @endif
        </div>
    </div>

    {{-- Aksi --}}
    @if($internship->status === 'active')
    <div class="card">
        <div class="card-header"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Tindakan</div>
        <div class="card-body d-flex flex-column gap-2" style="padding:16px 20px">
            {{-- Tandai selesai --}}
            <form method="POST" action="{{ route('company.internships.complete', $internship) }}"
                  onsubmit="return confirm('Tandai magang {{ addslashes($student->user->name) }} sebagai selesai?')">
                @csrf
                <button class="btn btn-success w-100">
                    <i class="bi bi-patch-check-fill me-2"></i>Tandai Selesai
                </button>
            </form>

            {{-- Beri penilaian --}}
            @php $companyEval = $internship->companyEvaluation(); @endphp
            @if($companyEval)
                <a href="{{ route('company.evaluations.show', $companyEval) }}" class="btn btn-outline-primary">
                    <i class="bi bi-patch-check me-2"></i>Lihat Penilaian
                </a>
            @else
                <a href="{{ route('company.evaluations.create', $internship) }}" class="btn btn-outline-primary">
                    <i class="bi bi-patch-check me-2"></i>Beri Penilaian
                </a>
            @endif

            {{-- Hentikan --}}
            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#terminateModal">
                <i class="bi bi-stop-circle me-2"></i>Hentikan Magang
            </button>
        </div>
    </div>
    @elseif($internship->status === 'completed')
    <div class="card">
        <div class="card-body d-flex flex-column gap-2" style="padding:16px 20px">
            @php $companyEval = $internship->companyEvaluation(); @endphp
            @if($companyEval)
                <a href="{{ route('company.evaluations.show', $companyEval) }}" class="btn btn-primary">
                    <i class="bi bi-patch-check-fill me-2"></i>Lihat Penilaian
                </a>
                <a href="{{ route('company.evaluations.edit', $companyEval) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-pencil me-2"></i>Edit Penilaian
                </a>
            @else
                <a href="{{ route('company.evaluations.create', $internship) }}" class="btn btn-primary">
                    <i class="bi bi-patch-check-fill me-2"></i>Beri Penilaian Akhir
                </a>
            @endif
        </div>
    </div>
    @endif

</div>

{{-- Kolom kanan: statistik & data --}}
<div class="col-xl-8 d-flex flex-column gap-3">

    {{-- Stat cards --}}
    <div class="row g-3">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#d1fae5;color:#065f46">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $pct }}%</div>
                    <div class="stat-label">Kehadiran</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#eff6ff;color:#1a56db">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $approvedReports }}</div>
                    <div class="stat-label">Laporan OK</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fef3c7;color:#92400e">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $totalReports }}</div>
                    <div class="stat-label">Total Laporan</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fee2e2;color:#991b1b">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div>
                    <div class="stat-value">
                        {{ $internship->attendance->where('status','absent')->count() }}
                    </div>
                    <div class="stat-label">Alpha</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Laporan harian terbaru --}}
    <div class="card">
        <div class="card-header"><i class="bi bi-journal-text text-primary me-2"></i>Laporan Harian Terbaru</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kegiatan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($internship->dailyReports->sortByDesc('report_date')->take(7) as $report)
                        <tr>
                            <td style="font-size:13px;white-space:nowrap;font-weight:600">
                                {{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}
                            </td>
                            <td style="font-size:13px">{{ Str::limit($report->activity, 60) }}</td>
                            <td>
                                @php $rmap=['draft'=>['Draft','secondary'],'submitted'=>['Menunggu','warning'],'approved'=>['Disetujui','success'],'revision'=>['Revisi','danger']]; [$rl,$rc]=$rmap[$report->status]??[$report->status,'secondary']; @endphp
                                <span class="badge-status bg-{{ $rc }}-subtle text-{{ $rc }}-emphasis">{{ $rl }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4" style="color:#94a3b8;font-size:13px">
                                Belum ada laporan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Presensi terbaru --}}
    <div class="card">
        <div class="card-header"><i class="bi bi-calendar-check text-success me-2"></i>Presensi Terbaru</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Durasi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($internship->attendance->sortByDesc('attendance_date')->take(7) as $att)
                        <tr>
                            <td style="font-size:13px;white-space:nowrap;font-weight:600">
                                {{ \Carbon\Carbon::parse($att->attendance_date)->format('d M Y') }}
                            </td>
                            <td style="font-size:13px">
                                {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '—' }}
                            </td>
                            <td style="font-size:13px">
                                {{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '—' }}
                            </td>
                            <td style="font-size:13px;color:#64748b">{{ $att->duration() ?? '—' }}</td>
                            <td>
                                @php $amap=['present'=>['Hadir','success'],'sick'=>['Sakit','warning'],'permission'=>['Izin','info'],'absent'=>['Alpha','danger']]; [$al,$ac]=$amap[$att->status]??[$att->status,'secondary']; @endphp
                                <span class="badge-status bg-{{ $ac }}-subtle text-{{ $ac }}-emphasis">{{ $al }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" style="color:#94a3b8;font-size:13px">
                                Belum ada data presensi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Dokumen --}}
    @if($internship->documents->count())
    <div class="card">
        <div class="card-header"><i class="bi bi-folder-fill text-warning me-2"></i>Dokumen</div>
        <div class="card-body p-0">
            @foreach($internship->documents as $doc)
            <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                <i class="bi bi-file-earmark-pdf text-danger" style="font-size:20px;flex-shrink:0"></i>
                <div class="flex-fill">
                    <div style="font-size:13.5px;font-weight:600;color:#0f172a">{{ $doc->title }}</div>
                    <div style="font-size:12px;color:#94a3b8">{{ $doc->file_name }} · {{ $doc->fileSizeFormatted() }}</div>
                </div>
                @php $dmap=['pending'=>['Menunggu','warning'],'approved'=>['Disetujui','success'],'rejected'=>['Ditolak','danger']]; [$dl,$dc]=$dmap[$doc->status]??[$doc->status,'secondary']; @endphp
                <span class="badge-status bg-{{ $dc }}-subtle text-{{ $dc }}-emphasis">{{ $dl }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tombol kembali --}}
    <div>
        <a href="{{ route('company.internships.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
        </a>
    </div>

</div>
</div>

{{-- Modal terminate --}}
@if($internship->status === 'active')
<div class="modal fade" id="terminateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none">
            <div class="modal-header border-0" style="padding:20px 24px 0">
                <h6 class="modal-title fw-bold text-danger">Hentikan Magang</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('company.internships.terminate', $internship) }}">
                @csrf
                <div class="modal-body" style="padding:16px 24px">
                    <div class="p-3 rounded-3 mb-3" style="background:#fef2f2;border:1px solid #fecaca;font-size:13px;color:#991b1b">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Tindakan ini akan menghentikan magang <strong>{{ $student->user->name }}</strong> sebelum waktunya.
                        Siswa dan pembimbing sekolah akan mendapat notifikasi.
                    </div>
                    <label class="form-label fw-semibold" style="font-size:13.5px">
                        Alasan Penghentian <span class="text-danger">*</span>
                    </label>
                    <textarea name="notes" rows="4" class="form-control" required minlength="20"
                              style="font-size:13.5px"
                              placeholder="Jelaskan alasan penghentian magang secara detail (min. 20 karakter)…"></textarea>
                </div>
                <div class="modal-footer border-0" style="padding:0 24px 20px;gap:8px">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Yakin ingin menghentikan magang ini?')">
                        <i class="bi bi-stop-circle me-2"></i>Hentikan Magang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
