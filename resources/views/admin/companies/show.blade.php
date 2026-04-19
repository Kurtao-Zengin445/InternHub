@extends('layouts.app')

@section('title', 'Detail Perusahaan — ' . $company->name)
@section('page-title', 'Detail Perusahaan')
@section('page-subtitle', $company->industry ?? 'Perusahaan Mitra')

@section('content')

<div class="row g-3">
    {{-- Sidebar info --}}
    <div class="col-xl-4">
        <div class="card mb-3" style="padding:24px">
            <div class="text-center mb-4">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" height="64" class="rounded-3 mb-3">
                @else
                    <div style="width:64px;height:64px;border-radius:16px;background:#fef3c7;color:#92400e;font-size:26px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                @endif
                <div style="font-size:16px;font-weight:700;color:#0f172a">{{ $company->name }}</div>
                @if($company->industry)
                    <span class="badge bg-warning-subtle text-warning-emphasis mt-1">{{ $company->industry }}</span>
                @endif
            </div>

            <div class="d-flex flex-column gap-3" style="font-size:13px">
                <div class="d-flex gap-2">
                    <i class="bi bi-geo-alt text-danger" style="width:18px;flex-shrink:0"></i>
                    <span style="color:#64748b">{{ $company->address }}</span>
                </div>
                @if($company->phone)
                <div class="d-flex gap-2">
                    <i class="bi bi-telephone text-success" style="width:18px"></i>
                    <span>{{ $company->phone }}</span>
                </div>
                @endif
                @if($company->email)
                <div class="d-flex gap-2">
                    <i class="bi bi-envelope text-primary" style="width:18px"></i>
                    <span>{{ $company->email }}</span>
                </div>
                @endif
                @if($company->website)
                <div class="d-flex gap-2">
                    <i class="bi bi-globe text-info" style="width:18px"></i>
                    <a href="{{ $company->website }}" target="_blank" rel="noopener" style="word-break:break-all">
                        {{ $company->website }}
                    </a>
                </div>
                @endif
                @if($company->contact_person)
                <div class="d-flex gap-2">
                    <i class="bi bi-person text-warning" style="width:18px"></i>
                    <span>{{ $company->contact_person }}
                        @if($company->contact_person_phone)
                            <span style="color:#94a3b8"> · {{ $company->contact_person_phone }}</span>
                        @endif
                    </span>
                </div>
                @endif
            </div>

            @if($company->description)
            <div class="mt-3 pt-3 border-top" style="font-size:13px;color:#64748b;line-height:1.65">
                {{ $company->description }}
            </div>
            @endif

            <div class="d-flex flex-column gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit Perusahaan
                </a>
                <form method="POST" action="{{ route('admin.companies.destroy', $company) }}"
                      onsubmit="return confirm('Hapus perusahaan ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash me-2"></i>Hapus Perusahaan
                    </button>
                </form>
            </div>
        </div>

        {{-- Statistik --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-bar-chart text-primary me-2"></i>Statistik</div>
            <div class="card-body">
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <div style="font-size:22px;font-weight:800;color:#1a56db">{{ $programStats['total'] }}</div>
                        <div style="font-size:11px;color:#94a3b8">Total Program</div>
                    </div>
                    <div class="col-4">
                        <div style="font-size:22px;font-weight:800;color:#10b981">{{ $programStats['open'] }}</div>
                        <div style="font-size:11px;color:#94a3b8">Buka</div>
                    </div>
                    <div class="col-4">
                        <div style="font-size:22px;font-weight:800;color:#94a3b8">{{ $programStats['completed'] }}</div>
                        <div style="font-size:11px;color:#94a3b8">Selesai</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Program magang --}}
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-briefcase-fill text-warning me-2"></i>Program Magang</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Program</th>
                                <th>Periode</th>
                                <th class="text-center">Kuota</th>
                                <th class="text-center">Lamar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPrograms as $program)
                            <tr>
                                <td>
                                    <div style="font-size:13.5px;font-weight:600;color:#0f172a">
                                        {{ Str::limit($program->title, 34) }}
                                    </div>
                                    @if($program->field)
                                        <div style="font-size:11.5px;color:#94a3b8">{{ $program->field }}</div>
                                    @endif
                                </td>
                                <td style="font-size:12.5px;color:#64748b;white-space:nowrap">
                                    {{ \Carbon\Carbon::parse($program->start_date)->format('d M Y') }}<br>
                                    {{ \Carbon\Carbon::parse($program->end_date)->format('d M Y') }}
                                </td>
                                <td class="text-center" style="font-size:13px">{{ $program->quota }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">
                                        {{ $program->applications_count }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $smap = ['draft'=>['Draft','secondary'],'open'=>['Buka','success'],'closed'=>['Tutup','warning'],'completed'=>['Selesai','primary']];
                                        [$sl,$sc] = $smap[$program->status] ?? [$program->status,'secondary'];
                                    @endphp
                                    <span class="badge-status bg-{{ $sc }}-subtle text-{{ $sc }}-emphasis">{{ $sl }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4" style="color:#94a3b8;font-size:13px">
                                    Belum ada program magang.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection