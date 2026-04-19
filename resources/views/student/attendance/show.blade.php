@extends('layouts.app')

@section('title', 'Detail Presensi')
@section('page-title', 'Detail Presensi')
@section('page-subtitle', \Carbon\Carbon::parse($attendance->attendance_date)->translatedFormat('l, d F Y'))

@section('content')

<div class="row justify-content-center">
<div class="col-xl-7 col-lg-9">

    <div class="card mb-3">
        <div class="card-header d-flex align-items-center gap-2">
            <a href="{{ route('student.attendance.index') }}" class="btn btn-sm btn-outline-secondary py-0 px-2">
                <i class="bi bi-arrow-left"></i>
            </a>
            Detail Presensi — {{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d F Y') }}
        </div>
        <div class="card-body" style="padding:28px">

            {{-- Status besar --}}
            @php
                $statusMap = [
                    'present'    => ['Hadir',  '#f0fdf4', '#065f46', 'person-check-fill'],
                    'sick'       => ['Sakit',  '#fffbeb', '#92400e', 'thermometer'],
                    'permission' => ['Izin',   '#eff6ff', '#1e40af', 'file-earmark-text'],
                    'absent'     => ['Alpha',  '#fef2f2', '#991b1b', 'x-circle-fill'],
                    'holiday'    => ['Libur',  '#f8fafc', '#475569', 'calendar-x'],
                ];
                [$slabel, $sbg, $stxt, $sicon] = $statusMap[$attendance->status] ?? [$attendance->status,'#f8fafc','#475569','question'];
            @endphp

            <div class="text-center mb-4 p-4 rounded-3" style="background: {{ $sbg }}">
                <i class="bi bi-{{ $sicon }}" style="font-size:40px;color: {{ $stxt }};display:block;margin-bottom:10px"></i>
                <div style="font-size:20px;font-weight:800;color: {{ $stxt }}">{{ $slabel }}</div>
                <div style="font-size:13px;color: {{ $stxt }};opacity:.65;margin-top:4px">
                    {{ \Carbon\Carbon::parse($attendance->attendance_date)->translatedFormat('l, d F Y') }}
                </div>
            </div>

            @if($attendance->status === 'present')

            {{-- Waktu --}}
            <div class="row g-3 mb-4">
                <div class="col-6 text-center p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0">
                    <div style="font-size:11px;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px">Check In</div>
                    <div style="font-size:30px;font-weight:800;color:#065f46;letter-spacing:-1px">
                        {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '—' }}
                    </div>
                </div>
                <div class="col-6 text-center p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0">
                    <div style="font-size:11px;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px">Check Out</div>
                    <div style="font-size:30px;font-weight:800;color:#1e40af;letter-spacing:-1px">
                        {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '—' }}
                    </div>
                </div>
            </div>

            @if($attendance->duration())
            <div class="text-center mb-4 p-3 rounded-3" style="background:#eff6ff;border:1px solid #dbeafe">
                <div style="font-size:12px;color:#3b82f6;margin-bottom:2px">Total Jam Kerja</div>
                <div style="font-size:24px;font-weight:800;color:#1a56db">{{ $attendance->duration() }}</div>
            </div>
            @endif

            {{-- Foto selfie --}}
            @if($attendance->check_in_photo || $attendance->check_out_photo)
            <div class="mb-4">
                <div style="font-size:12px;color:#94a3b8;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px">Foto Selfie</div>
                <div class="row g-3">
                    @if($attendance->check_in_photo)
                    <div class="{{ $attendance->check_out_photo ? 'col-6' : 'col-12' }}">
                        <div style="font-size:12px;font-weight:600;color:#065f46;margin-bottom:6px">
                            <i class="bi bi-arrow-right-circle-fill me-1"></i>Check In
                        </div>
                        <img src="{{ asset('storage/'.$attendance->check_in_photo) }}"
                             class="w-100 rounded-3" style="object-fit:cover;aspect-ratio:4/3" alt="Selfie check in">
                    </div>
                    @endif
                    @if($attendance->check_out_photo)
                    <div class="{{ $attendance->check_in_photo ? 'col-6' : 'col-12' }}">
                        <div style="font-size:12px;font-weight:600;color:#1e40af;margin-bottom:6px">
                            <i class="bi bi-arrow-left-circle-fill me-1"></i>Check Out
                        </div>
                        <img src="{{ asset('storage/'.$attendance->check_out_photo) }}"
                             class="w-100 rounded-3" style="object-fit:cover;aspect-ratio:4/3" alt="Selfie check out">
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Geolocation detail --}}
            @if($attendance->checkin_latitude || $attendance->checkout_latitude)
            <div class="mb-4">
                <div style="font-size:12px;color:#94a3b8;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px">Data Lokasi</div>

                @if($attendance->checkin_latitude)
                <div class="p-3 rounded-3 mb-2" style="background:#f0fdf4;border:1px solid #d1fae5">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-geo-alt-fill text-success mt-1" style="flex-shrink:0"></i>
                        <div style="flex:1">
                            <div style="font-size:12.5px;font-weight:600;color:#065f46">Lokasi Check In</div>
                            @if($attendance->checkin_address)
                                <div style="font-size:12px;color:#374151;margin-top:2px">{{ $attendance->checkin_address }}</div>
                            @endif
                            <div style="font-size:11.5px;color:#94a3b8;margin-top:2px">
                                {{ $attendance->checkin_latitude }}, {{ $attendance->checkin_longitude }}
                            </div>
                            @if($attendance->checkin_distance !== null)
                                <div class="mt-1">
                                    <span class="badge-status {{ $attendance->checkin_distance <= 500 ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}"
                                          style="font-size:11px">
                                        <i class="bi bi-{{ $attendance->checkin_distance <= 500 ? 'check-circle' : 'exclamation-circle' }} me-1"></i>
                                        {{ $attendance->checkinDistanceLabel() }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <a href="https://maps.google.com/?q={{ $attendance->checkin_latitude }},{{ $attendance->checkin_longitude }}"
                           target="_blank" rel="noopener"
                           class="btn btn-sm btn-outline-success py-0" style="white-space:nowrap">
                            <i class="bi bi-map me-1"></i>Peta
                        </a>
                    </div>
                </div>
                @endif

                @if($attendance->checkout_latitude)
                <div class="p-3 rounded-3" style="background:#eff6ff;border:1px solid #bfdbfe">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-geo-alt-fill text-primary mt-1" style="flex-shrink:0"></i>
                        <div style="flex:1">
                            <div style="font-size:12.5px;font-weight:600;color:#1e40af">Lokasi Check Out</div>
                            @if($attendance->checkout_address)
                                <div style="font-size:12px;color:#374151;margin-top:2px">{{ $attendance->checkout_address }}</div>
                            @endif
                            <div style="font-size:11.5px;color:#94a3b8;margin-top:2px">
                                {{ $attendance->checkout_latitude }}, {{ $attendance->checkout_longitude }}
                            </div>
                            @if($attendance->checkout_distance !== null)
                                <div class="mt-1">
                                    <span class="badge-status {{ $attendance->checkout_distance <= 500 ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}"
                                          style="font-size:11px">
                                        <i class="bi bi-{{ $attendance->checkout_distance <= 500 ? 'check-circle' : 'exclamation-circle' }} me-1"></i>
                                        {{ $attendance->checkoutDistanceLabel() }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <a href="https://maps.google.com/?q={{ $attendance->checkout_latitude }},{{ $attendance->checkout_longitude }}"
                           target="_blank" rel="noopener"
                           class="btn btn-sm btn-outline-primary py-0" style="white-space:nowrap">
                            <i class="bi bi-map me-1"></i>Peta
                        </a>
                    </div>
                </div>
                @endif
            </div>
            @endif

            @endif {{-- end if present --}}

            {{-- Catatan --}}
            @if($attendance->notes)
            <div class="mb-4">
                <div style="font-size:12px;color:#94a3b8;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px">Catatan</div>
                <div class="p-3 rounded-3" style="background:#f8fafc;font-size:13.5px;color:#475569;border:1px solid #e2e8f0;line-height:1.65">
                    {{ $attendance->notes }}
                </div>
            </div>
            @endif

        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('student.attendance.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
        <a href="{{ route('student.attendance.today') }}" class="btn btn-primary ms-auto">
            <i class="bi bi-calendar-check me-2"></i>Presensi Hari Ini
        </a>
    </div>

</div>
</div>

@endsection