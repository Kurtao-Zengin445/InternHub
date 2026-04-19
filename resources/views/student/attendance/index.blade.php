@extends('layouts.app')

@section('title', 'Riwayat Presensi')
@section('page-title', 'Riwayat Presensi')
@section('page-subtitle', 'Rekap kehadiran selama magang')

@push('styles')
<style>
.summary-box {
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
}
.summary-icon {
    width: 46px; height: 46px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.attendance-row { transition: background .1s; }
.attendance-row:hover { background: #f8fafc; }
</style>
@endpush

@section('content')

@if(!$hasActiveInternship)
<div class="alert alert-info d-flex align-items-center" role="alert">
    <i class="bi bi-info-circle me-2"></i>
    <div>
        <strong>Anda belum memiliki magang aktif.</strong>
        <div class="mt-1" style="font-size:13px">Silakan pilih program magang terlebih dahulu untuk mulai melakukan presensi.</div>
    </div>
</div>
<div class="text-center py-5">
    <div style="font-size:64px;margin-bottom:16px">📋</div>
    <h5 style="font-weight:700;color:#0f172a">Belum Ada Magang Aktif</h5>
    <p style="color:#64748b;font-size:14px">Anda perlu memiliki magang aktif untuk mengakses fitur presensi.</p>
    <a href="{{ route('student.applications.index') }}" class="btn btn-primary">
        <i class="bi bi-briefcase me-2"></i>Lihat Lowongan Magang
    </a>
</div>
@else

{{-- ── Ringkasan kehadiran ─────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="summary-box bg-white border">
            <div class="summary-icon" style="background:#d1fae5;color:#065f46">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <div>
                <div class="stat-value">{{ $summary['present'] }}</div>
                <div class="stat-label">Hadir</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="summary-box bg-white border">
            <div class="summary-icon" style="background:#fef3c7;color:#92400e">
                <i class="bi bi-thermometer"></i>
            </div>
            <div>
                <div class="stat-value">{{ $summary['sick'] }}</div>
                <div class="stat-label">Sakit</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="summary-box bg-white border">
            <div class="summary-icon" style="background:#dbeafe;color:#1e40af">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <div>
                <div class="stat-value">{{ $summary['permission'] }}</div>
                <div class="stat-label">Izin</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="summary-box bg-white border">
            <div class="summary-icon" style="background:#fee2e2;color:#991b1b">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div>
                <div class="stat-value">{{ $summary['absent'] }}</div>
                <div class="stat-label">Alpha</div>
            </div>
        </div>
    </div>
</div>

{{-- Progress bar kehadiran --}}
<div class="card mb-4">
    <div class="card-body" style="padding:20px 24px">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span style="font-size:13.5px;font-weight:600;color:#0f172a">Tingkat Kehadiran</span>
            <span style="font-size:20px;font-weight:800;color:#1a56db">{{ $summary['percentage'] }}%</span>
        </div>
        @php
            $present    = $summary['present'];
            $sick       = $summary['sick'];
            $permission = $summary['permission'];
            $absent     = $summary['absent'];
            $total      = max($present + $sick + $permission + $absent, 1);
        @endphp
        <div class="progress" style="height:10px;border-radius:8px;background:#f1f5f9">
            <div class="progress-bar bg-success" style="width: {{ ($present/$total)*100 }}%" title="Hadir"></div>
            <div class="progress-bar bg-warning" style="width: {{ ($sick/$total)*100 }}%" title="Sakit"></div>
            <div class="progress-bar bg-info"    style="width: {{ ($permission/$total)*100 }}%" title="Izin"></div>
            <div class="progress-bar bg-danger"  style="width: {{ ($absent/$total)*100 }}%" title="Alpha"></div>
        </div>
        <div class="d-flex gap-4 mt-2" style="font-size:11.5px;color:#94a3b8">
            <span><span style="color:#10b981">●</span> Hadir</span>
            <span><span style="color:#f59e0b">●</span> Sakit</span>
            <span><span style="color:#3b82f6">●</span> Izin</span>
            <span><span style="color:#ef4444">●</span> Alpha</span>
        </div>
    </div>
</div>

{{-- ── Toolbar ─────────────────────────────────── --}}
<div class="card mb-3">
    <div class="card-body d-flex flex-wrap gap-3 align-items-center">
        <form method="GET" action="{{ route('student.attendance.index') }}" class="d-flex gap-2 flex-fill flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" style="max-width:220px;font-size:13.5px"
                   placeholder="Cari tanggal, status, atau catatan…">
            <select name="status" class="form-select" style="max-width:160px;font-size:13.5px"
                    onchange="this.form.submit()">
                <option value="">Semua Status</option>
                @foreach(['present'=>'Hadir','sick'=>'Sakit','permission'=>'Izin','absent'=>'Alpha','holiday'=>'Libur'] as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-secondary" style="font-size:13.5px">
                <i class="bi bi-search me-1"></i>Filter
            </button>
            @if(request('status') || request('search'))
                <a href="{{ route('student.attendance.index') }}" class="btn btn-outline-danger" style="font-size:13.5px">
                    <i class="bi bi-x"></i>
                </a>
            @endif
        </form>
        <div class="d-flex gap-2">
            <a href="{{ route('student.attendance.export') }}" class="btn btn-success">
                📊 Export Excel <i class="bi bi-file-earmark-excel me-1"></i>
            </a>
        </div>
    </div>
</div>

{{-- ── Tabel Riwayat ───────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Presensi</span>
        <a href="{{ route('student.attendance.today') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-calendar-check me-1"></i>Presensi Hari Ini
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Hari</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Durasi</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                    <tr class="attendance-row">
                        <td style="font-size:13px;font-weight:600;white-space:nowrap">
                            {{ \Carbon\Carbon::parse($att->attendance_date)->format('d M Y') }}
                        </td>
                        <td style="font-size:13px;color:#64748b">
                            {{ \Carbon\Carbon::parse($att->attendance_date)->translatedFormat('l') }}
                        </td>
                        <td style="font-size:13px">
                            @if($att->check_in)
                                <span style="font-weight:600;color:#065f46">
                                    <i class="bi bi-arrow-right-circle-fill text-success me-1" style="font-size:11px"></i>
                                    {{ \Carbon\Carbon::parse($att->check_in)->format('H:i') }}
                                </span>
                                @if($att->check_in_photo)
                                <br><a href="{{ asset('storage/'.$att->check_in_photo) }}" target="_blank" class="btn btn-sm btn-outline-success mt-1 py-0">
                                    <i class="bi bi-image me-1"></i>Preview Foto
                                </a>
                                @endif
                            @else
                                <span style="color:#cbd5e1">—</span>
                            @endif
                        </td>
                        <td style="font-size:13px">
                            @if($att->check_out)
                                <span style="font-weight:600;color:#1e40af">
                                    <i class="bi bi-arrow-left-circle-fill text-primary me-1" style="font-size:11px"></i>
                                    {{ \Carbon\Carbon::parse($att->check_out)->format('H:i') }}
                                </span>
                                @if($att->check_out_photo)
                                <br><a href="{{ asset('storage/'.$att->check_out_photo) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1 py-0">
                                    <i class="bi bi-image me-1"></i>Preview Foto
                                </a>
                                @endif
                            @else
                                <span style="color:#cbd5e1">—</span>
                            @endif
                        </td>
                        <td style="font-size:13px;color:#64748b">
                            {{ $att->duration() ?? '—' }}
                        </td>
                        <td>
                            @php
                                $statusMap = [
                                    'present'    => ['Hadir',  'success'],
                                    'sick'       => ['Sakit',  'warning'],
                                    'permission' => ['Izin',   'info'],
                                    'absent'     => ['Alpha',  'danger'],
                                    'holiday'    => ['Libur',  'secondary'],
                                ];
                                [$slabel, $scolor] = $statusMap[$att->status] ?? [$att->status, 'secondary'];
                            @endphp
                            <span class="badge-status bg-{{ $scolor }}-subtle text-{{ $scolor }}-emphasis">
                                {{ $slabel }}
                            </span>
                        </td>
                        <td style="font-size:12.5px;color:#94a3b8;max-width:180px">
                            {{ Str::limit($att->notes, 40) ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5" style="color:#94a3b8">
                            <i class="bi bi-calendar-x" style="font-size:36px;display:block;margin-bottom:8px"></i>
                            Belum ada data presensi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($attendances->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2" style="padding:12px 20px">
        <div style="font-size:13px;color:#94a3b8">
            Menampilkan {{ $attendances->firstItem() }}–{{ $attendances->lastItem() }}
            dari {{ $attendances->total() }} data
        </div>
        {{ $attendances->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endif

@endsection