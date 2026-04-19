@extends('layouts.app')

@section('title', 'Verifikasi Laporan Harian')
@section('page-title', 'Verifikasi Laporan Harian')
@section('page-subtitle', 'Tinjau dan setujui laporan siswa bimbingan')

@section('content')

{{-- ── Toolbar & filter ────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body d-flex flex-wrap gap-3 align-items-end">

        <form method="GET" action="{{ route('supervisor.reports.index') }}" class="d-flex flex-wrap gap-2 flex-fill">
            <select name="status" class="form-select" style="max-width:160px;font-size:13.5px">
                <option value="">Semua Status</option>
                @foreach(['submitted'=>'Menunggu','approved'=>'Disetujui','revision'=>'Perlu Revisi','draft'=>'Draft'] as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="internship_id" class="form-select" style="max-width:200px;font-size:13.5px">
                <option value="">Semua Siswa</option>
                @foreach($supervisedInternships as $i)
                    <option value="{{ $i->id }}" {{ request('internship_id') == $i->id ? 'selected' : '' }}>
                        {{ $i->application->student->user->name }}
                    </option>
                @endforeach
            </select>
            <button class="btn btn-outline-secondary" style="font-size:13.5px">
                <i class="bi bi-filter me-1"></i>Filter
            </button>
            @if(request()->hasAny(['status','internship_id']))
                <a href="{{ route('supervisor.reports.index') }}" class="btn btn-outline-danger" style="font-size:13.5px">
                    <i class="bi bi-x"></i> Reset
                </a>
            @endif
            <a href="{{ route('supervisor.reports.export', request()->query()) }}" class="btn btn-success">
                <i class="bi bi-download"></i> Export Excel <i class="bi bi-file-earmark-excel me-1"></i>
            </a>
        </form>

        {{-- Bulk approve --}}
        <button class="btn btn-success" id="bulkApproveBtn" style="display:none"
                onclick="bulkApprove()">
            <i class="bi bi-check-all me-2"></i>Setujui Terpilih (<span id="selectedCount">0</span>)
        </button>
    </div>
</div>

{{-- ── Tabel ───────────────────────────────────── --}}
<form id="bulkForm" method="POST" action="{{ route('supervisor.reports.bulk-approve') }}">
    @csrf
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width:40px">
                            <input type="checkbox" id="checkAll" class="form-check-input"
                                   onchange="toggleAll(this)">
                        </th>
                        <th>Siswa</th>
                        <th>Tanggal</th>
                        <th>Kegiatan</th>
                        <th>Status</th>
                        <th style="width:130px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr class="{{ $report->status === 'submitted' ? '' : 'opacity-75' }}">
                        <td>
                            @if($report->status === 'submitted')
                            <input type="checkbox" name="report_ids[]" value="{{ $report->id }}"
                                   class="form-check-input report-check"
                                   onchange="updateBulkBtn()">
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px;height:32px;border-radius:8px;background:#eff6ff;color:#1a56db;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">
                                    {{ strtoupper(substr($report->internship->application->student->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-size:13px;font-weight:600;color:#0f172a">
                                        {{ $report->internship->application->student->user->name }}
                                    </div>
                                    <div style="font-size:11px;color:#94a3b8">
                                        {{ $report->internship->application->program->company->name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:13px;white-space:nowrap">
                            {{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}
                            <div style="font-size:11px;color:#94a3b8">
                                {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('l') }}
                            </div>
                        </td>
                        <td style="font-size:13px">{{ Str::limit($report->activity, 55) }}</td>
                        <td>
                            @php
                                $map = ['draft'=>['Draft','secondary'],'submitted'=>['Menunggu','warning'],'approved'=>['Disetujui','success'],'revision'=>['Revisi','danger']];
                                [$lbl,$clr] = $map[$report->status] ?? [$report->status,'secondary'];
                            @endphp
                            <span class="badge-status bg-{{ $clr }}-subtle text-{{ $clr }}-emphasis">{{ $lbl }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('supervisor.reports.show', $report) }}"
                                   class="btn btn-sm btn-outline-secondary py-1" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($report->status === 'submitted')
                                <form method="POST" action="{{ route('supervisor.reports.approve', $report) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-success py-1" title="Setujui">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <button class="btn btn-sm btn-outline-danger py-1" title="Minta revisi"
                                        onclick="openRevisionModal({{ $report->id }})">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5" style="color:#94a3b8">
                            <i class="bi bi-journal-check" style="font-size:36px;display:block;margin-bottom:8px;color:#d1fae5"></i>
                            Tidak ada laporan yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reports->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2" style="padding:12px 20px">
        <div style="font-size:13px;color:#94a3b8">
            Menampilkan {{ $reports->firstItem() }}–{{ $reports->lastItem() }} dari {{ $reports->total() }} laporan
        </div>
        {{ $reports->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
</form>

{{-- ── Modal Revisi ────────────────────────────── --}}
<div class="modal fade" id="revisionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none">
            <div class="modal-header border-0" style="padding:20px 24px 0">
                <h6 class="modal-title fw-bold">Minta Revisi Laporan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="revisionForm">
                @csrf
                <div class="modal-body" style="padding:16px 24px">
                    <p style="font-size:13.5px;color:#64748b;margin-bottom:12px">
                        Tuliskan catatan atau arahan perbaikan untuk siswa:
                    </p>
                    <textarea name="feedback" rows="5" class="form-control"
                              placeholder="Contoh: Kegiatan yang dituliskan terlalu singkat. Mohon jelaskan lebih detail proses pengerjaan dan tools yang digunakan."
                              required minlength="10"
                              style="font-size:13.5px"></textarea>
                    <div class="form-text">Minimal 10 karakter.</div>
                </div>
                <div class="modal-footer border-0" style="padding:0 24px 20px;gap:8px">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-arrow-repeat me-2"></i>Minta Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleAll(el) {
    document.querySelectorAll('.report-check').forEach(c => c.checked = el.checked);
    updateBulkBtn();
}

function updateBulkBtn() {
    const checked = document.querySelectorAll('.report-check:checked').length;
    const btn = document.getElementById('bulkApproveBtn');
    btn.style.display = checked > 0 ? '' : 'none';
    document.getElementById('selectedCount').textContent = checked;
    document.getElementById('checkAll').indeterminate =
        checked > 0 && checked < document.querySelectorAll('.report-check').length;
}

function bulkApprove() {
    if (confirm('Setujui semua laporan yang dipilih?')) {
        document.getElementById('bulkForm').submit();
    }
}

function openRevisionModal(reportId) {
    const form = document.getElementById('revisionForm');
    form.action = `/supervisor/reports/${reportId}/revision`;
    new bootstrap.Modal(document.getElementById('revisionModal')).show();
}
</script>
@endpush

@endsection
