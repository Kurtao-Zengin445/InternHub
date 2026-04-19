@extends('layouts.app')

@section('title', 'Detail Penilaian')
@section('page-title', 'Detail Penilaian')
@section('page-subtitle', $evaluation->internship->application->student->user->name)

@push('styles')
<style>
.score-bar-wrap { display:flex; align-items:center; gap:12px; margin-bottom:14px; }
.score-bar-label { font-size:13px; color:#64748b; min-width:140px; }
.score-bar-track { flex:1; height:8px; border-radius:6px; background:#f1f5f9; overflow:hidden; }
.score-bar-fill  { height:100%; border-radius:6px; transition:.5s ease; }
.score-bar-val   { font-size:14px; font-weight:700; min-width:36px; text-align:right; }
.big-score { font-size:64px; font-weight:800; letter-spacing:-3px; line-height:1; }
</style>
@endpush

@section('content')

<div class="row g-3">
<div class="col-xl-8">

    {{-- Score header --}}
    @php
        $score = $evaluation->final_score;
        $grade = $evaluation->grade_letter;
        [$gradeColor, $gradeBg] = match(true) {
            $score >= 90 => ['#065f46', '#d1fae5'],
            $score >= 80 => ['#1e40af', '#dbeafe'],
            $score >= 70 => ['#92400e', '#fef3c7'],
            $score >= 60 => ['#374151', '#f3f4f6'],
            default      => ['#991b1b', '#fee2e2'],
        };
    @endphp

    <div class="card mb-3" style="background:{{ $gradeBg }};border-color:transparent">
        <div class="card-body d-flex align-items-center gap-4" style="padding:28px">
            <div class="text-center">
                <div class="big-score" style="color:{{ $gradeColor }}">{{ number_format($score, 1) }}</div>
                <div style="font-size:13px;color:{{ $gradeColor }};opacity:.7;margin-top:4px">Nilai Akhir</div>
            </div>
            <div style="width:1px;height:80px;background:{{ $gradeColor }};opacity:.2"></div>
            <div>
                <div style="font-size:56px;font-weight:800;color:{{ $gradeColor }};line-height:1">{{ $grade }}</div>
                <div style="font-size:13px;color:{{ $gradeColor }};opacity:.7">Grade</div>
            </div>
            <div class="ms-auto text-end">
                <div style="font-size:12.5px;color:{{ $gradeColor }};opacity:.7;margin-bottom:4px">Dinilai oleh</div>
                <div style="font-size:14px;font-weight:700;color:{{ $gradeColor }}">{{ $evaluation->evaluator->name }}</div>
                <div style="font-size:12px;color:{{ $gradeColor }};opacity:.6;margin-top:2px">
                    {{ $evaluation->evaluator_type === 'supervisor' ? 'Pembimbing Sekolah' : 'Pembimbing Perusahaan' }}
                </div>
                @if($evaluation->evaluated_at)
                <div style="font-size:11.5px;color:{{ $gradeColor }};opacity:.5;margin-top:2px">
                    {{ \Carbon\Carbon::parse($evaluation->evaluated_at)->translatedFormat('d F Y') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Breakdown komponen --}}
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Rincian Nilai</div>
        <div class="card-body" style="padding:24px">
            @php
                $components = [
                    ['key'=>'discipline_score',    'label'=>'Kedisiplinan',     'weight'=>20],
                    ['key'=>'skill_score',         'label'=>'Kemampuan Teknis', 'weight'=>30],
                    ['key'=>'attitude_score',      'label'=>'Sikap & Etika',    'weight'=>20],
                    ['key'=>'knowledge_score',     'label'=>'Pengetahuan',      'weight'=>15],
                    ['key'=>'communication_score', 'label'=>'Komunikasi',       'weight'=>15],
                ];
            @endphp
            @foreach($components as $comp)
            @php
                $val = $evaluation->{$comp['key']} ?? 0;
                $barColor = $val >= 80 ? '#10b981' : ($val >= 70 ? '#f59e0b' : '#ef4444');
            @endphp
            <div class="score-bar-wrap">
                <div class="score-bar-label">
                    {{ $comp['label'] }}
                    <span style="font-size:11px;color:#94a3b8">({{ $comp['weight'] }}%)</span>
                </div>
                <div class="score-bar-track">
                    <div class="score-bar-fill" style="width:{{ $val }}%;background:{{ $barColor }}"></div>
                </div>
                <div class="score-bar-val" style="color:{{ $barColor }}">{{ number_format($val, 0) }}</div>
            </div>
            @endforeach

            {{-- Garis total --}}
            <div class="pt-3 border-top mt-1 d-flex justify-content-between align-items-center">
                <span style="font-size:13.5px;font-weight:700;color:#0f172a">Nilai Akhir (Weighted)</span>
                <span style="font-size:20px;font-weight:800;color:{{ $gradeColor }}">
                    {{ number_format($score, 1) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Catatan evaluasi --}}
    @if($evaluation->strengths || $evaluation->improvements || $evaluation->notes)
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-chat-text text-warning me-2"></i>Catatan Evaluasi</div>
        <div class="card-body" style="padding:24px">
            @if($evaluation->strengths)
            <div class="mb-4">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-bottom:8px">Kelebihan</div>
                <div style="font-size:14px;color:#1e293b;line-height:1.75;white-space:pre-wrap">{{ $evaluation->strengths }}</div>
            </div>
            @endif
            @if($evaluation->improvements)
            <div class="{{ $evaluation->strengths ? 'pt-4 border-top mb-4' : 'mb-4' }}">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-bottom:8px">Hal yang Perlu Ditingkatkan</div>
                <div style="font-size:14px;color:#1e293b;line-height:1.75;white-space:pre-wrap">{{ $evaluation->improvements }}</div>
            </div>
            @endif
            @if($evaluation->notes)
            <div class="{{ ($evaluation->strengths || $evaluation->improvements) ? 'pt-4 border-top' : '' }}">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-bottom:8px">Catatan Tambahan</div>
                <div style="font-size:14px;color:#1e293b;line-height:1.75;white-space:pre-wrap">{{ $evaluation->notes }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="d-flex gap-2">
        <a href="{{ route('supervisor.evaluations.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
        <a href="{{ route('supervisor.evaluations.edit', $evaluation) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-2"></i>Edit Penilaian
        </a>
    </div>

</div>

{{-- Sidebar --}}
<div class="col-xl-4 d-flex flex-column gap-3">
    @php $student = $evaluation->internship->application->student; @endphp

    <div class="card">
        <div class="card-header"><i class="bi bi-person-fill text-primary me-2"></i>Info Siswa</div>
        <div class="card-body" style="font-size:13px;padding:16px 20px">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:44px;height:44px;border-radius:11px;background:#eff6ff;color:#1a56db;font-size:17px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    {{ strtoupper(substr($student->user->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight:700;color:#0f172a">{{ $student->user->name }}</div>
                    <div style="color:#94a3b8;font-size:12px">{{ $student->nis }} · {{ $student->class }}</div>
                </div>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span style="color:#94a3b8">Sekolah</span>
                <span style="font-weight:500;text-align:right;max-width:160px">{{ $student->school->name }}</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span style="color:#94a3b8">Perusahaan</span>
                <span style="font-weight:500;text-align:right;max-width:160px">
                    {{ $evaluation->internship->application->program->company->name }}
                </span>
            </div>
            <div class="d-flex justify-content-between">
                <span style="color:#94a3b8">Periode</span>
                <span style="font-weight:500;font-size:12px">
                    {{ \Carbon\Carbon::parse($evaluation->internship->start_date)->format('d M') }} –
                    {{ \Carbon\Carbon::parse($evaluation->internship->end_date)->format('d M Y') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Nilai dari sisi lain --}}
    @php
        $otherEval = $evaluation->evaluator_type === 'supervisor'
            ? $evaluation->internship->companyEvaluation()
            : $evaluation->internship->supervisorEvaluation();
        $otherLabel = $evaluation->evaluator_type === 'supervisor' ? 'Perusahaan' : 'Sekolah';
    @endphp
    <div class="card">
        <div class="card-header">
            <i class="bi bi-arrow-left-right text-secondary me-2"></i>Nilai {{ $otherLabel }}
        </div>
        <div class="card-body text-center" style="padding:20px">
            @if($otherEval)
                <div style="font-size:36px;font-weight:800;color:#1a56db;letter-spacing:-1px">
                    {{ number_format($otherEval->final_score, 1) }}
                </div>
                <div style="font-size:14px;font-weight:700;color:#64748b;margin-top:2px">
                    Grade {{ $otherEval->grade_letter }}
                </div>
            @else
                <div style="font-size:13px;color:#94a3b8">Belum dinilai oleh {{ $otherLabel }}</div>
            @endif
        </div>
    </div>

    {{-- Nilai akhir gabungan --}}
    @php $finalScore = $evaluation->internship->finalScore(); @endphp
    @if($finalScore !== null)
    <div class="card" style="border-color:#bfdbfe;background:#eff6ff">
        <div class="card-body text-center" style="padding:20px">
            <div style="font-size:11px;color:#3b82f6;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">
                Nilai Akhir Gabungan
            </div>
            <div style="font-size:40px;font-weight:800;color:#1e40af;letter-spacing:-1.5px">
                {{ number_format($finalScore, 1) }}
            </div>
            <div style="font-size:12px;color:#3b82f6;margin-top:4px">
                40% Sekolah + 60% Perusahaan
            </div>
        </div>
    </div>
    @endif

</div>
</div>

@endsection
