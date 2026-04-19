@extends('layouts.app')

@section('title', 'Verifikasi Dokumen')
@section('page-title', 'Verifikasi Dokumen')
@section('page-subtitle', 'Tinjau dan setujui dokumen siswa bimbingan')

@section('content')

{{-- Toolbar --}}
<div class="card mb-4">
    <div class="card-body d-flex flex-wrap gap-3 align-items-end">
        <form method="GET" class="d-flex flex-wrap gap-2 flex-fill">
            <select name="status" class="form-select" style="max-width:160px;font-size:13.5px">
                <option value="">Semua Status</option>
                <option value="pending"  {{ request('status')==='pending'  ?'selected':'' }}>Menunggu</option>
                <option value="approved" {{ request('status')==='approved' ?'selected':'' }}>Disetujui</option>
                <option value="rejected" {{ request('status')==='rejected' ?'selected':'' }}>Ditolak</option>
            </select>
            <select name="type" class="form-select" style="max-width:200px;font-size:13.5px">
                <option value="">Semua Jenis</option>
                @foreach([
                    'introduction_letter' => 'Surat Pengantar',
                    'acceptance_letter'   => 'Surat Penerimaan',
                    'activity_plan'       => 'Rencana Kegiatan',
                    'progress_report'     => 'Laporan Kemajuan',
                    'final_report'        => 'Laporan Akhir',
                    'certificate'         => 'Sertifikat',
                    'other'               => 'Lainnya',
                ] as $val => $label)
                    <option value="{{ $val }}" {{ request('type')===$val?'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-secondary" style="font-size:13.5px">
                <i class="bi bi-filter me-1"></i>Filter
            </button>
            @if(request()->hasAny(['status','type']))
                <a href="{{ route('supervisor.documents.index') }}" class="btn btn-outline-danger" style="font-size:13.5px">
                    <i class="bi bi-x"></i> Reset
                </a>
            @endif
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Dokumen</th>
                        <th>Jenis</th>
                        <th>Ukuran</th>
                        <th>Status</th>
                        <th>Diunggah</th>
                        <th style="width:140px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                    <tr>
                        <td>
                            <div style="font-size:13px;font-weight:600;color:#0f172a">
                                {{ $doc->internship->application->student->user->name }}
                            </div>
                            <div style="font-size:11.5px;color:#94a3b8">
                                {{ $doc->internship->application->program->company->name }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-{{ in_array($doc->file_type,['jpg','jpeg','png','webp']) ? 'image' : 'pdf' }} text-danger"
                                   style="font-size:18px;flex-shrink:0"></i>
                                <div>
                                    <div style="font-size:13px;font-weight:600;color:#0f172a">
                                        {{ $doc->title }}
                                    </div>
                                    <div style="font-size:11.5px;color:#94a3b8">{{ $doc->file_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $typeLabels = [
                                    'introduction_letter' => ['Surat Pengantar',  '#eff6ff','#1e40af'],
                                    'acceptance_letter'   => ['Surat Penerimaan', '#d1fae5','#065f46'],
                                    'activity_plan'       => ['Rencana Kegiatan', '#fef3c7','#92400e'],
                                    'progress_report'     => ['Lap. Kemajuan',    '#ede9fe','#4c1d95'],
                                    'final_report'        => ['Lap. Akhir',       '#fee2e2','#991b1b'],
                                    'certificate'         => ['Sertifikat',       '#d1fae5','#065f46'],
                                    'other'               => ['Lainnya',          '#f1f5f9','#475569'],
                                ];
                                [$typeLabel, $typeBg, $typeColor] = $typeLabels[$doc->document_type] ?? ['Lainnya','#f1f5f9','#475569'];
                            @endphp
                            <span style="font-size:11.5px; font-weight:600; padding:3px 10px; border-radius:20px; background: {{ $typeBg }};color:{{ $typeColor }}">
                                {{ $typeLabel }}
                            </span>
                        </td>
                        <td style="font-size:13px;color:#64748b">
                            {{ $doc->fileSizeFormatted() }}
                        </td>
                        <td>
                            @php
                                $dmap=['pending'=>['Menunggu','warning'],'approved'=>['Disetujui','success'],'rejected'=>['Ditolak','danger']];
                                [$dl,$dc]=$dmap[$doc->status]??[$doc->status,'secondary'];
                            @endphp
                            <span class="badge-status bg-{{ $dc }}-subtle text-{{ $dc }}-emphasis">{{ $dl }}</span>
                        </td>
                        <td style="font-size:12px;color:#94a3b8;white-space:nowrap">
                            {{ \Carbon\Carbon::parse($doc->uploaded_at)->format('d M Y') }}<br>
                            {{ \Carbon\Carbon::parse($doc->uploaded_at)->diffForHumans() }}
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                {{-- Detail --}}
                                <a href="{{ route('supervisor.documents.show', $doc) }}"
                                   class="btn btn-sm btn-outline-secondary py-1" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                {{-- Download --}}
                                <a href="{{ route('supervisor.documents.download', $doc) }}"
                                   class="btn btn-sm btn-outline-primary py-1" title="Unduh">
                                    <i class="bi bi-download"></i>
                                </a>
                                {{-- Approve --}}
                                @if($doc->status === 'pending')
                                <form method="POST" action="{{ route('supervisor.documents.approve', $doc) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-success py-1" title="Setujui">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                {{-- Reject --}}
                                <button class="btn btn-sm btn-outline-danger py-1"
                                        title="Tolak"
                                        onclick="openRejectModal({{ $doc->id }}, '{{ addslashes($doc->title) }}')">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5" style="color:#94a3b8">
                            <i class="bi bi-folder-x" style="font-size:36px;display:block;margin-bottom:8px"></i>
                            Tidak ada dokumen yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($documents->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2"
         style="padding:12px 20px">
        <div style="font-size:13px;color:#94a3b8">
            Menampilkan {{ $documents->firstItem() }}–{{ $documents->lastItem() }}
            dari {{ $documents->total() }} dokumen
        </div>
        {{ $documents->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

{{-- Modal Tolak --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none">
            <div class="modal-header border-0" style="padding:20px 24px 0">
                <h6 class="modal-title fw-bold">Tolak Dokumen</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="rejectForm">
                @csrf
                <div class="modal-body" style="padding:16px 24px">
                    <p style="font-size:13.5px;color:#64748b;margin-bottom:12px">
                        Tolak dokumen <strong id="rejectDocTitle"></strong>?
                        Berikan alasan agar siswa dapat memperbaikinya.
                    </p>
                    <textarea name="rejection_reason" rows="4" class="form-control"
                              placeholder="Contoh: Dokumen tidak lengkap, mohon sertakan tanda tangan kepala sekolah."
                              required minlength="10"
                              style="font-size:13.5px"></textarea>
                    <div class="form-text">Minimal 10 karakter.</div>
                </div>
                <div class="modal-footer border-0" style="padding:0 24px 20px;gap:8px">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-lg me-2"></i>Tolak Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openRejectModal(docId, docTitle) {
    document.getElementById('rejectForm').action = `/supervisor/documents/${docId}/reject`;
    document.getElementById('rejectDocTitle').textContent = docTitle;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endpush

@endsection
