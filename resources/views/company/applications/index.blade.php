@extends('layouts.app')

@section('title', 'Lamaran Masuk')
@section('page-title', 'Lamaran Masuk')
@section('page-subtitle', 'Seleksi dan proses lamaran magang')

@section('content')
<div class="card mb-4">
    <div class="card-body d-flex flex-wrap gap-3 align-items-end">
        <form method="GET" class="d-flex flex-wrap gap-2 flex-fill">
            <select name="status" class="form-select" style="max-width:160px;font-size:13.5px">
                <option value="">Semua Status</option>
                @foreach(['pending' => 'Pending', 'reviewed' => 'Ditinjau', 'accepted' => 'Diterima', 'rejected' => 'Ditolak', 'cancelled' => 'Dibatalkan'] as $value => $label)
                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="program_id" class="form-select" style="max-width:220px;font-size:13.5px">
                <option value="">Semua Program</option>
                @foreach($programs as $program)
                <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>{{ Str::limit($program->title, 32) }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-secondary" style="font-size:13.5px"><i class="bi bi-filter me-1"></i>Filter</button>
            @if(request()->hasAny(['status', 'program_id']))
            <a href="{{ route('company.applications.index') }}" class="btn btn-outline-danger" style="font-size:13.5px"><i class="bi bi-x"></i></a>
            @endif
        </form>
        <button id="bulkRejectBtn" style="display:none" class="btn btn-outline-danger" onclick="openBulkRejectModal()"><i class="bi bi-x-lg me-2"></i>Tolak Terpilih (<span id="selectedCount">0</span>)</button>
    </div>
</div>

<form id="bulkForm" method="POST" action="{{ route('company.applications.bulk-reject') }}">
    @csrf
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pelamar</th>
                            <th>Program</th>
                            <th>Status</th>
                            <th>Tanggal Lamar</th>
                            <th style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                        @php
                        $studentUser = $application->student_id ? $application->student->user : null;
                        $student = $application->studentModel;
                        @endphp
                        <tr>
                            <td style="color:#94a3b8;font-size:12px">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:36px;height:36px;border-radius:9px;background:#eff6ff;color:#1a56db;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0">{{ strtoupper(substr($studentUser->name ?? '?', 0, 1)) }}</div>
                                    <div>
                                        <div style="font-size:13px;font-weight:600;color:#0f172a">{{ $studentUser->name ?? '-' }}</div> <!--tandain -->
                                        <div style="font-size:11.5px;color:#94a3b8">{{ $student?->school->name ?? '-' }} - {{ $student?->major ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size:13px">{{ Str::limit($application->program->title, 30) }}</td>
                            <td>
                                @php
                                $applicationMap = ['pending' => ['Pending', 'warning'], 'reviewed' => ['Ditinjau', 'info'], 'accepted' => ['Diterima', 'success'], 'rejected' => ['Ditolak', 'danger'], 'cancelled' => ['Batal', 'secondary']];
                                [$applicationLabel, $applicationColor] = $applicationMap[$application->status] ?? [$application->status, 'secondary'];
                                @endphp
                                <span class="badge-status bg-{{ $applicationColor }}-subtle text-{{ $applicationColor }}-emphasis">{{ $applicationLabel }}</span>
                            </td>
                            <td style="font-size:12px;color:#94a3b8;white-space:nowrap">{{ \Carbon\Carbon::parse($application->applied_at)->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('company.applications.show', $application) }}" class="btn btn-sm btn-outline-secondary py-1"><i class="bi bi-eye"></i></a>
                                    @if($application->isPending())
                                    <form method="POST" action="{{ route('company.applications.accept', $application) }}" onsubmit="return confirm('Terima lamaran {{ addslashes($application->student->user->name) }}?')">
                                        @csrf
                                        <button class="btn btn-sm btn-success py-1" title="Terima"><i class="bi bi-check-lg"></i></button>
                                    </form>
                                    <button class="btn btn-sm btn-outline-danger py-1" onclick="openRejectModal({{ $application->student_id }}, '{{ addslashes($application->student->user->name) }}')" title="Tolak"><i class="bi bi-x-lg"></i></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5" style="color:#94a3b8"><i class="bi bi-inbox" style="font-size:36px;display:block;margin-bottom:8px"></i>Tidak ada lamaran yang ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($applications->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2" style="padding:12px 20px">
            <div style="font-size:13px;color:#94a3b8">Menampilkan {{ $applications->firstItem() }}-{{ $applications->lastItem() }} dari {{ $applications->total() }} lamaran</div>
            {{ $applications->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</form>

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none">
            <div class="modal-header border-0" style="padding:20px 24px 0">
                <h6 class="modal-title fw-bold">Tolak Lamaran</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="rejectForm">
                @csrf
                <div class="modal-body" style="padding:16px 24px">
                    <p style="font-size:13.5px;color:#64748b;margin-bottom:12px">Tolak lamaran dari <strong id="rejectStudentName"></strong>? Berikan alasan penolakan:</p>
                    <textarea name="rejection_reason" rows="4" class="form-control" placeholder="Contoh: Kuota untuk program ini sudah terpenuhi. Terima kasih telah mendaftar." required minlength="10" style="font-size:13.5px"></textarea>
                    <div class="form-text">Minimal 10 karakter. Alasan ini akan dikirim ke siswa.</div>
                </div>
                <div class="modal-footer border-0" style="padding:0 24px 20px;gap:8px"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger"><i class="bi bi-x-lg me-2"></i>Tolak Lamaran</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="bulkRejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none">
            <div class="modal-header border-0" style="padding:20px 24px 0">
                <h6 class="modal-title fw-bold">Tolak Banyak Lamaran</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:16px 24px">
                <p style="font-size:13.5px;color:#64748b;margin-bottom:12px">Tolak <strong id="bulkCount"></strong> lamaran yang dipilih? Berikan alasan penolakan:</p>
                <textarea id="bulkReason" rows="4" class="form-control" placeholder="Contoh: Kuota program telah terpenuhi." minlength="10" style="font-size:13.5px"></textarea>
                <div class="form-text">Minimal 10 karakter. Alasan yang sama dikirim ke semua pelamar.</div>
            </div>
            <div class="modal-footer border-0" style="padding:0 24px 20px;gap:8px"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-danger" onclick="submitBulkReject()"><i class="bi bi-x-lg me-2"></i>Tolak Semua</button></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleAll(element) {
        document.querySelectorAll('.app-check').forEach((checkbox) => checkbox.checked = element.checked);
        updateBulkBtn();
    }

    function updateBulkBtn() {
        const checked = document.querySelectorAll('.app-check:checked').length;
        const button = document.getElementById('bulkRejectBtn');
        button.style.display = checked > 0 ? '' : 'none';
        document.getElementById('selectedCount').textContent = checked;
    }

    function openRejectModal(applicationId, studentName) {
        document.getElementById('rejectForm').action = `/company/applications/${applicationId}/reject`;
        document.getElementById('rejectStudentName').textContent = studentName;
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    }

    function openBulkRejectModal() {
        const count = document.querySelectorAll('.app-check:checked').length;
        document.getElementById('bulkCount').textContent = `${count} lamaran`;
        new bootstrap.Modal(document.getElementById('bulkRejectModal')).show();
    }

    function submitBulkReject() {
        const reason = document.getElementById('bulkReason').value.trim();
        if (reason.length < 10) {
            alert('Alasan penolakan minimal 10 karakter.');
            return;
        }
        const form = document.getElementById('bulkForm');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'rejection_reason';
        input.value = reason;
        form.appendChild(input);
        form.submit();
    }
</script>
@endpush

@endsection