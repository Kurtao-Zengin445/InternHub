@extends('layouts.app')

@section('title', isset($evaluation) ? 'Edit Penilaian' : 'Beri Penilaian')
@section('page-title', isset($evaluation) ? 'Edit Penilaian' : 'Penilaian Akhir Magang')
@section('page-subtitle', isset($evaluation)
    ? $evaluation->internship->student->user->name
    : $internship->student->user->name)

@push('styles')
<style>
/* ── Score slider ──────────────────────────── */
.score-group { margin-bottom: 24px; }

.score-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.score-label .label-text  { font-size: 13.5px; font-weight: 600; color: #0f172a; }
.score-label .score-value {
    font-size: 20px;
    font-weight: 800;
    letter-spacing: -1px;
    min-width: 48px;
    text-align: right;
    transition: color .2s;
}

.score-weight {
    font-size: 11px;
    color: #94a3b8;
    margin-bottom: 6px;
}

input[type="range"] {
    width: 100%;
    height: 6px;
    border-radius: 4px;
    appearance: none;
    background: #e2e8f0;
    outline: none;
    cursor: pointer;
}

input[type="range"]::-webkit-slider-thumb {
    appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #1a56db;
    box-shadow: 0 2px 8px rgba(26,86,219,.35);
    cursor: pointer;
    transition: transform .15s;
}

input[type="range"]::-webkit-slider-thumb:hover { transform: scale(1.2); }

/* ── Score gauge ───────────────────────────── */
.score-gauge {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto;
}

.score-gauge svg { transform: rotate(-90deg); }

.gauge-bg   { fill: none; stroke: #f1f5f9; stroke-width: 10; }
.gauge-fill { fill: none; stroke-width: 10; stroke-linecap: round; transition: stroke-dashoffset .4s ease, stroke .4s ease; }

.gauge-text {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.gauge-score {
    font-size: 28px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -1px;
    line-height: 1;
}

.gauge-grade {
    font-size: 13px;
    font-weight: 700;
    margin-top: 2px;
}
</style>
@endpush

@section('content')

@php
    $targetInternship = isset($evaluation) ? $evaluation->internship : $internship;
    $student = $targetInternship->student;
@endphp

<div class="row g-3">
<div class="col-xl-8">

<form method="POST"
      action="{{ isset($evaluation) ? route('school.evaluations.update', $evaluation) : route('school.evaluations.store', $targetInternship) }}"
      class="needs-validation" novalidate>
    @csrf
    @if(isset($evaluation)) @method('PUT') @endif

    {{-- Info siswa --}}
    <div class="card mb-3">
        <div class="card-body d-flex align-items-center gap-3" style="padding:18px 24px;background:#f8fafc">
            <div style="width:48px;height:48px;border-radius:12px;background:#eff6ff;color:#1a56db;font-size:19px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                {{ strtoupper(substr($student->user->name, 0, 1)) }}
            </div>
            <div>
                <div style="font-size:15px;font-weight:700;color:#0f172a">{{ $student->user->name }}</div>
                <div style="font-size:12.5px;color:#64748b">
                    {{ $student->class }} · {{ $student->school->name }} ·
                    {{ $targetInternship->program->company->name }}
                </div>
            </div>
        </div>
    </div>

    {{-- Komponen penilaian --}}
    <div class="card mb-3">
        <div class="card-header">
            <i class="bi bi-sliders text-primary me-2"></i>Komponen Penilaian
            <span style="font-size:12px;color:#94a3b8;font-weight:400;margin-left:6px">(Skala 0–100)</span>
        </div>
        <div class="card-body" style="padding:24px">

            @php
                $components = [
                    ['name'=>'discipline_score',    'label'=>'Kedisiplinan',      'weight'=>'Bobot 20%', 'icon'=>'alarm'],
                    ['name'=>'skill_score',         'label'=>'Kemampuan Teknis',  'weight'=>'Bobot 30%', 'icon'=>'tools'],
                    ['name'=>'attitude_score',      'label'=>'Sikap & Etika',     'weight'=>'Bobot 20%', 'icon'=>'emoji-smile'],
                    ['name'=>'knowledge_score',     'label'=>'Pengetahuan',       'weight'=>'Bobot 15%', 'icon'=>'book'],
                    ['name'=>'communication_score', 'label'=>'Komunikasi',        'weight'=>'Bobot 15%', 'icon'=>'chat-dots'],
                ];
                $weights = [
                    'discipline_score'    => 0.20,
                    'skill_score'         => 0.30,
                    'attitude_score'      => 0.20,
                    'knowledge_score'     => 0.15,
                    'communication_score' => 0.15,
                ];
            @endphp

            @foreach($components as $comp)
            @php $defaultVal = old($comp['name'], isset($evaluation) ? $evaluation->{$comp['name']} : 75); @endphp
            <div class="score-group">
                <div class="score-label">
                    <div>
                        <span class="label-text">
                            <i class="bi bi-{{ $comp['icon'] }} me-2" style="color:#94a3b8"></i>{{ $comp['label'] }}
                        </span>
                        <div class="score-weight">{{ $comp['weight'] }}</div>
                    </div>
                    <span class="score-value" id="{{ $comp['name'] }}_display"
                          style="color:{{ $defaultVal >= 80 ? '#10b981' : ($defaultVal >= 70 ? '#f59e0b' : '#ef4444') }}">
                        {{ $defaultVal }}
                    </span>
                </div>
                <input type="range" name="{{ $comp['name'] }}" min="0" max="100" step="1"
                       value="{{ $defaultVal }}" id="{{ $comp['name'] }}"
                       oninput="updateScore('{{ $comp['name'] }}', this.value)" required>
                @error($comp['name']) <div class="text-danger" style="font-size:12px;margin-top:4px">{{ $message }}</div> @enderror
            </div>
            @endforeach

        </div>
    </div>

    {{-- Catatan --}}
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-chat-text text-warning me-2"></i>Catatan Evaluasi</div>
        <div class="card-body" style="padding:24px">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size:13.5px">
                        Kelebihan Siswa
                    </label>
                    <textarea name="strengths" rows="3" class="form-control"
                              style="font-size:13.5px"
                              placeholder="Tuliskan hal-hal positif yang menonjol dari siswa selama magang…">{{ old('strengths', $evaluation->strengths ?? '') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size:13.5px">
                        Hal yang Perlu Ditingkatkan
                    </label>
                    <textarea name="improvements" rows="3" class="form-control"
                              style="font-size:13.5px"
                              placeholder="Tuliskan saran atau hal yang perlu diperbaiki oleh siswa…">{{ old('improvements', $evaluation->improvements ?? '') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size:13.5px">
                        Catatan Tambahan
                    </label>
                    <textarea name="notes" rows="3" class="form-control"
                              style="font-size:13.5px"
                              placeholder="Catatan tambahan tentang performa siswa…">{{ old('notes', $evaluation->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- Aksi --}}
    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('school.evaluations.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-circle me-1"></i>Batal
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-1"></i>{{ isset($evaluation) ? 'Update' : 'Simpan' }} Penilaian
        </button>
    </div>

</form>

</div>

{{-- Preview nilai --}}
<div class="col-xl-4">
    <div class="card sticky-top" style="top:20px">
        <div class="card-header text-center">
            <i class="bi bi-trophy text-warning fs-4 mb-2"></i>
            <div style="font-size:14px;font-weight:600">Preview Nilai Akhir</div>
        </div>
        <div class="card-body text-center" style="padding:32px 24px">

            {{-- Score gauge --}}
            <div class="score-gauge mb-3">
                <svg width="120" height="120" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="50" class="gauge-bg"/>
                    <circle cx="60" cy="60" r="50" class="gauge-fill" id="gaugeFill"
                            stroke-dasharray="314" stroke-dashoffset="94.2"/>
                </svg>
                <div class="gauge-text">
                    <div class="gauge-score" id="finalScore">75.0</div>
                    <div class="gauge-grade" id="finalGrade">B</div>
                </div>
            </div>

            {{-- Komponen breakdown --}}
            <div class="text-start">
                <div style="font-size:12px;font-weight:600;color:#64748b;margin-bottom:8px">Breakdown Nilai:</div>
                @foreach($components as $comp)
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span style="font-size:11px;color:#64748b">{{ $comp['label'] }}</span>
                    <span style="font-size:11px;font-weight:600" id="{{ $comp['name'] }}_preview">75</span>
                </div>
                @endforeach
            </div>

        </div>
    </div>
</div>

</div>

@push('scripts')
<script>
function updateScore(component, value) {
    // Update display value
    const display = document.getElementById(component + '_display');
    const preview = document.getElementById(component + '_preview');
    display.textContent = value;
    preview.textContent = value;

    // Update color based on score
    const color = value >= 80 ? '#10b981' : (value >= 70 ? '#f59e0b' : '#ef4444');
    display.style.color = color;

    // Calculate final score
    calculateFinalScore();
}

function calculateFinalScore() {
    const weights = {
        'discipline_score': 0.20,
        'skill_score': 0.30,
        'attitude_score': 0.20,
        'knowledge_score': 0.15,
        'communication_score': 0.15,
    };

    let totalScore = 0;
    for (const [component, weight] of Object.entries(weights)) {
        const value = parseInt(document.getElementById(component).value) || 0;
        totalScore += value * weight;
    }

    // Update final score display
    const finalScoreEl = document.getElementById('finalScore');
    const finalGradeEl = document.getElementById('finalGrade');
    const gaugeFill = document.getElementById('gaugeFill');

    finalScoreEl.textContent = totalScore.toFixed(1);

    // Calculate grade
    let grade = 'E';
    let gradeColor = '#ef4444';
    if (totalScore >= 85) {
        grade = 'A';
        gradeColor = '#10b981';
    } else if (totalScore >= 75) {
        grade = 'B';
        gradeColor = '#3b82f6';
    } else if (totalScore >= 65) {
        grade = 'C';
        gradeColor = '#f59e0b';
    } else if (totalScore >= 55) {
        grade = 'D';
        gradeColor = '#f97316';
    }

    finalGradeEl.textContent = grade;
    finalGradeEl.style.color = gradeColor;

    // Update gauge
    const circumference = 314; // 2 * π * 50
    const offset = circumference - (totalScore / 100) * circumference;
    gaugeFill.style.strokeDashoffset = offset;
    gaugeFill.style.stroke = gradeColor;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateFinalScore();

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});
</script>
@endpush

@endsection