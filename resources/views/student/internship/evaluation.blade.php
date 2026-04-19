@extends('layouts.app')

@section('title', 'Hasil Penilaian')
@section('page-title', 'Hasil Penilaian Akhir')
@section('page-subtitle', 'Rekap evaluasi dari sekolah dan perusahaan')

@push('styles')
<style>
.score-bar-wrap{display:flex;align-items:center;gap:12px;margin-bottom:12px;}
.score-bar-label{font-size:13px;color:#64748b;min-width:140px;}
.score-bar-track{flex:1;height:7px;border-radius:5px;background:#f1f5f9;overflow:hidden;}
.score-bar-fill{height:100%;border-radius:5px;}
.score-bar-val{font-size:13.5px;font-weight:700;min-width:32px;text-align:right;}
</style>
@endpush

@section('content')

@if(!$internship)
<div class="card text-center" style="padding:60px">
    <div style="font-size:48px;margin-bottom:12px">📊</div>
    <h5 style="font-weight:700;color:#0f172a">Belum ada data magang</h5>
    <p style="color:#64748b;font-size:14px">Hasil penilaian akan tersedia setelah Anda menyelesaikan program magang.</p>
</div>
@else

{{-- ── Nilai akhir gabungan ──────────────────────── --}}
@if($finalScore !== null)
@php
    [$gradeColor, $gradeBg, $gradeLabel] = match(true) {
        $finalScore >= 90 => ['#065f46', '#d1fae5', 'Sangat Baik'],
        $finalScore >= 80 => ['#1e40af', '#dbeafe', 'Baik'],
        $finalScore >= 70 => ['#92400e', '#fef3c7', 'Cukup'],
        $finalScore >= 60 => ['#374151', '#f3f4f6', 'Kurang'],
        default           => ['#991b1b', '#fee2e2', 'Sangat Kurang'],
    };
    $grade = match(true) {
        $finalScore >= 90 => 'A',
        $finalScore >= 80 => 'B',
        $finalScore >= 70 => 'C',
        $finalScore >= 60 => 'D',
        default           => 'E',
    };
@endphp

<div class="card mb-4" style="background: {{ $gradeBg }};border-color:transparent">
    <div class="card-body text-center" style="padding:36px">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color: {{ $gradeColor }};opacity:.7;margin-bottom:12px">
            Nilai Akhir Magang
        </div>
        <div style="font-size:72px;font-weight:800;color: {{ $gradeColor }};letter-spacing:-4px;line-height:1">
            {{ number_format($finalScore, 1) }}
        </div>
        <div style="font-size:20px;font-weight:700; color: {{ $gradeColor }};margin-top:6px">
            Grade {{ $grade }} — {{ $gradeLabel }}
        </div>
        <div style="font-size:13px; color: {{ $gradeColor }};opacity:.6;margin-top:8px">
            Dihitung dari 40% nilai sekolah + 60% nilai perusahaan
        </div>
    </div>
</div>

@else
<div class="alert alert-info mb-4" style="border-radius:12px;font-size:14px">
    <i class="bi bi-info-circle me-2"></i>
    Nilai akhir akan dihitung setelah <strong>kedua</strong> pihak (sekolah dan perusahaan) memberikan penilaian.
</div>
@endif

{{-- ── Dua kolom nilai ───────────────────────────── --}}
<div class="row g-3">

    {{-- Nilai Sekolah --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-building text-success me-2"></i>Nilai Sekolah</span>
                @if($supervisorEval)
                    <span class="badge bg-success-subtle text-success-emphasis">
                        {{ number_format($supervisorEval->final_score, 1) }} · Grade {{ $supervisorEval->grade_letter }}
                    </span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Belum dinilai</span>
                @endif
            </div>
            <div class="card-body" style="padding:20px">
                @if($supervisorEval)
                @php
                    $components = [
                        ['key'=>'discipline_score',    'label'=>'Kedisiplinan',    'w'=>20],
                        ['key'=>'skill_score',         'label'=>'Kemampuan Teknis','w'=>30],
                        ['key'=>'attitude_score',      'label'=>'Sikap & Etika',   'w'=>20],
                        ['key'=>'knowledge_score',     'label'=>'Pengetahuan',     'w'=>15],
                        ['key'=>'communication_score', 'label'=>'Komunikasi',      'w'=>15],
                    ];
                @endphp
                @foreach($components as $comp)
                @php $val=$supervisorEval->{$comp['key']}??0; $bc=$val>=80?'#10b981':($val>=70?'#f59e0b':'#ef4444'); @endphp
                <div class="score-bar-wrap">
                    <div class="score-bar-label">
                        {{ $comp['label'] }}
                        <span style="font-size:10.5px;color:#94a3b8"> {{ $comp['w'] }}%</span>
                    </div>
                    <div class="score-bar-track">
                        <div class="score-bar-fill" style="width: {{ $val }}%; background: {{ $bc }}"></div>
                    </div>
                    <div class="score-bar-val" style="color: {{ $bc }}">{{ number_format($val, 0) }}</div>
                </div>
                @endforeach

                @if($supervisorEval->strengths)
                <div class="mt-4 pt-3 border-top">
                    <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Kelebihan</div>
                    <div style="font-size:13px;color:#1e293b;line-height:1.65">{{ $supervisorEval->strengths }}</div>
                </div>
                @endif
                @if($supervisorEval->improvements)
                <div class="mt-3">
                    <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Perlu Ditingkatkan</div>
                    <div style="font-size:13px;color:#1e293b;line-height:1.65">{{ $supervisorEval->improvements }}</div>
                </div>
                @endif

                @if($supervisorEval->evaluated_at)
                <div class="mt-3 pt-3 border-top" style="font-size:12px;color:#94a3b8">
                    Dinilai {{ \Carbon\Carbon::parse($supervisorEval->evaluated_at)->translatedFormat('d F Y') }}
                    oleh {{ $supervisorEval->evaluator->name }}
                </div>
                @endif

                @else
                <div class="text-center py-4" style="color:#94a3b8">
                    <i class="bi bi-clock" style="font-size:32px;display:block;margin-bottom:8px"></i>
                    <div style="font-size:13.5px">Pembimbing sekolah belum memberikan penilaian.</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Nilai Perusahaan --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-briefcase-fill text-warning me-2"></i>Nilai Perusahaan</span>
                @if($companyEval)
                    <span class="badge bg-primary-subtle text-primary-emphasis">
                        {{ number_format($companyEval->final_score, 1) }} · Grade {{ $companyEval->grade_letter }}
                    </span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Belum dinilai</span>
                @endif
            </div>
            <div class="card-body" style="padding:20px">
                @if($companyEval)
                @foreach($components as $comp)
                @php $val=$companyEval->{$comp['key']}??0; $bc=$val>=80?'#10b981':($val>=70?'#f59e0b':'#ef4444'); @endphp
                <div class="score-bar-wrap">
                    <div class="score-bar-label">
                        {{ $comp['label'] }}
                        <span style="font-size:10.5px;color:#94a3b8"> {{ $comp['w'] }}%</span>
                    </div>
                    <div class="score-bar-track">
                        <div class="score-bar-fill" style="width: {{ $val }}%; background: {{ $bc }}"></div>
                    </div>
                    <div class="score-bar-val" style="color: {{ $bc }}">{{ number_format($val, 0) }}</div>
                </div>
                @endforeach

                @if($companyEval->strengths)
                <div class="mt-4 pt-3 border-top">
                    <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Kelebihan</div>
                    <div style="font-size:13px;color:#1e293b;line-height:1.65">{{ $companyEval->strengths }}</div>
                </div>
                @endif
                @if($companyEval->improvements)
                <div class="mt-3">
                    <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Perlu Ditingkatkan</div>
                    <div style="font-size:13px;color:#1e293b;line-height:1.65">{{ $companyEval->improvements }}</div>
                </div>
                @endif

                @if($companyEval->evaluated_at)
                <div class="mt-3 pt-3 border-top" style="font-size:12px;color:#94a3b8">
                    Dinilai {{ \Carbon\Carbon::parse($companyEval->evaluated_at)->translatedFormat('d F Y') }}
                    oleh {{ $companyEval->evaluator->name }}
                </div>
                @endif

                @else
                <div class="text-center py-4" style="color:#94a3b8">
                    <i class="bi bi-clock" style="font-size:32px;display:block;margin-bottom:8px"></i>
                    <div style="font-size:13.5px">Perusahaan belum memberikan penilaian.</div>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endif

@endsection
