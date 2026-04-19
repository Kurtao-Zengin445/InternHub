@extends('layouts.app')

@section('title', 'Program Magang')
@section('page-title', 'Program Magang')
@section('page-subtitle', 'Kelola program magang perusahaan Anda')

@section('content')
<div class="row g-3 mb-4">
    @php
        $company = auth()->user()->company;
        $counts = [
            ['label' => 'Total Program', 'val' => $company->programs()->count(), 'bg' => '#eff6ff', 'color' => '#1a56db', 'icon' => 'briefcase-fill'],
            ['label' => 'Sedang Buka', 'val' => $company->programs()->where('status', 'open')->count(), 'bg' => '#d1fae5', 'color' => '#065f46', 'icon' => 'megaphone-fill'],
            ['label' => 'Lamaran Masuk', 'val' => \App\Models\Application::whereHas('program', fn($q) => $q->where('company_id', $company->id))->count(), 'bg' => '#fef3c7', 'color' => '#92400e', 'icon' => 'inbox-fill'],
            ['label' => 'Sudah Selesai', 'val' => $company->programs()->where('status', 'completed')->count(), 'bg' => '#f1f5f9', 'color' => '#475569', 'icon' => 'patch-check-fill'],
        ];
    @endphp
    @foreach($counts as $count)
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3 bg-white rounded-3 border d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:{{ $count['bg'] }};color:{{ $count['color'] }}"><i class="bi bi-{{ $count['icon'] }}"></i></div>
            <div>
                <div class="stat-value">{{ $count['val'] }}</div>
                <div class="stat-label">{{ $count['label'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card mb-3">
    <div class="card-body d-flex flex-wrap gap-3 align-items-center">
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select" style="max-width:160px;font-size:13.5px" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                @foreach(['draft' => 'Draft', 'open' => 'Buka', 'closed' => 'Tutup', 'completed' => 'Selesai'] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('company.programs.create') }}" class="btn btn-primary ms-auto"><i class="bi bi-plus-lg me-2"></i>Buka Program Baru</a>
    </div>
</div>

<div class="row g-3">
    @forelse($programs as $program)
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-body" style="padding:22px">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div style="flex:1;margin-right:12px">
                        <div style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:4px">{{ $program->title }}</div>
                        @if($program->field)
                        <span style="font-size:12px;background:#eff6ff;color:#1a56db;padding:3px 10px;border-radius:20px;font-weight:600">{{ $program->field }}</span>
                        @endif
                    </div>
                    @php
                        $statusMap = ['draft' => ['Draft', 'secondary'], 'open' => ['Buka', 'success'], 'closed' => ['Tutup', 'warning'], 'completed' => ['Selesai', 'primary']];
                        [$statusLabel, $statusColor] = $statusMap[$program->status] ?? [$program->status, 'secondary'];
                    @endphp
                    <span class="badge-status bg-{{ $statusColor }}-subtle text-{{ $statusColor }}-emphasis">{{ $statusLabel }}</span>
                </div>
                <div class="d-flex flex-wrap gap-3 mb-3" style="font-size:12.5px;color:#64748b">
                    <span><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($program->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($program->end_date)->format('d M Y') }}</span>
                    <span><i class="bi bi-people me-1"></i>Kuota: {{ $program->quota }}</span>
                    <span><i class="bi bi-clock me-1"></i>Daftar s.d. {{ \Carbon\Carbon::parse($program->registration_end)->format('d M Y') }}</span>
                </div>
                @php
                    $accepted = $program->acceptedApplications()->count();
                    $percentage = $program->quota > 0 ? round(($accepted / $program->quota) * 100) : 0;
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1" style="font-size:12px">
                        <span style="color:#94a3b8">Peserta diterima</span>
                        <span style="font-weight:600;color:#0f172a">{{ $accepted }} / {{ $program->quota }}</span>
                    </div>
                    <div class="progress" style="height:6px;border-radius:4px">
                        <div class="progress-bar {{ $percentage >= 100 ? 'bg-danger' : 'bg-success' }}" style="width:{{ min($percentage, 100) }}%"></div>
                    </div>
                </div>
                <div class="d-flex gap-2 pt-2 border-top flex-wrap">
                    <a href="{{ route('company.programs.show', $program) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye me-1"></i>Detail</a>
                    @if($program->status !== 'completed')
                    <a href="{{ route('company.programs.edit', $program) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
                    @endif
                    @if($program->status === 'open')
                    <form method="POST" action="{{ route('company.programs.close', $program) }}" onsubmit="return confirm('Tutup pendaftaran program ini?')">
                        @csrf
                        <button class="btn btn-outline-warning btn-sm"><i class="bi bi-stop-circle me-1"></i>Tutup</button>
                    </form>
                    @endif
                    @if($program->applications_count === 0)
                    <form method="POST" action="{{ route('company.programs.destroy', $program) }}" class="ms-auto" onsubmit="return confirm('Hapus program ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                    </form>
                    @endif
                    <a href="{{ route('company.applications.index') }}?program_id={{ $program->id }}" class="btn btn-sm ms-auto" style="background:#eff6ff;color:#1a56db;font-weight:600"><i class="bi bi-inbox me-1"></i>{{ $program->applications_count }} Lamaran</a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card text-center" style="padding:60px">
            <div style="font-size:48px;margin-bottom:12px"><i class="bi bi-card-checklist"></i></div>
            <h5 style="font-weight:700;color:#0f172a">Belum ada program magang</h5>
            <p style="color:#64748b;font-size:14px;margin-bottom:20px">Buka program magang untuk mulai menerima siswa.</p>
            <a href="{{ route('company.programs.create') }}" class="btn btn-primary mx-auto" style="max-width:220px"><i class="bi bi-plus-lg me-2"></i>Buka Program Pertama</a>
        </div>
    </div>
    @endforelse
</div>

@if($programs->hasPages())
<div class="d-flex justify-content-center mt-4">{{ $programs->withQueryString()->links('pagination::bootstrap-5') }}</div>
@endif

@endsection
