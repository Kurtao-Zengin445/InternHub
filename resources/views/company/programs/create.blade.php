@extends('layouts.app')

@section('title', 'Buka Program Magang')
@section('page-title', 'Buka Program Magang Baru')
@section('page-subtitle', 'Isi detail program magang yang ingin Anda buka')

@push('styles')
<style>
.date-range-hint { font-size:12px; color:#94a3b8; margin-top:4px; }
.section-divider { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:#94a3b8; margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid #f1f5f9; }
</style>
@endpush

@section('content')
@include('company.programs.form')
@endsection

@push('scripts')
<script>
function updateDateHints() {
    const start = document.getElementById('startDate').value;
    const end = document.getElementById('endDate').value;
    if (!start || !end) return;
    const s = new Date(start);
    const e = new Date(end);
    const days = Math.round((e - s) / (1000 * 60 * 60 * 24));
    const weeks = Math.round(days / 7);
    if (days > 0) {
        document.getElementById('durationText').textContent = `${days} hari (sekitar ${weeks} minggu)`;
        document.getElementById('durationHint').style.display = '';
    }
}

document.addEventListener('DOMContentLoaded', updateDateHints);
</script>
@endpush
