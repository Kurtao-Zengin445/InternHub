@extends('layouts.app')

@section('title', isset($evaluation) ? 'Edit Penilaian' : 'Beri Penilaian')
@section('page-title', isset($evaluation) ? 'Edit Penilaian' : 'Penilaian Akhir Magang')
@section('page-subtitle', isset($evaluation)
    ? $evaluation->internship->application->student->user->name
    : $internship->application->student->user->name)

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
    $student = $targetInternship->application->student;
@endphp

<div class="row g-3">
<div class="col-xl-8">

<form method="POST"
      action="{{ isset($evaluation) ? route('supervisor.evaluations.update', $evaluation) : route('supervisor.evaluations.store', $targetInternship) }}">
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
                    {{ $targetInternship->application->program->company->name }}
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
                       oninput="updateScore('{{ $comp['name'] }}', this.value)">
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
                    <label class="form-label fw-semibold" style="font-size:13.5px">Catatan Tambahan</label>
                    <textarea name="notes" rows="2" class="form-control"
                              style="font-size:13.5px"
                              placeholder="Catatan lain yang ingin Anda sampaikan…">{{ old('notes', $evaluation->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-patch-check-fill me-2"></i>
            {{ isset($evaluation) ? 'Perbarui Penilaian' : 'Simpan Penilaian' }}
        </button>
        <a href="{{ route('supervisor.evaluations.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>

</form>
</div>

{{-- Sidebar: preview nilai --}}
<div class="col-xl-4">
    <div class="card" style="position:sticky;top:80px">
        <div class="card-header"><i class="bi bi-speedometer2 text-primary me-2"></i>Preview Nilai</div>
        <div class="card-body" style="padding:24px">

            {{-- Gauge --}}
            <div class="score-gauge mb-3">
                <svg viewBox="0 0 100 100" width="120" height="120">
                    <circle class="gauge-bg" cx="50" cy="50" r="40"/>
                    <circle class="gauge-fill" id="gaugeFill" cx="50" cy="50" r="40"
                            stroke="#1a56db"
                            stroke-dasharray="251.2"
                            stroke-dashoffset="62.8"/>
                </svg>
                <div class="gauge-text">
                    <div class="gauge-score" id="gaugeScore">75</div>
                    <div class="gauge-grade" id="gaugeGrade" style="color:#f59e0b">B</div>
                </div>
            </div>

            {{-- Breakdown --}}
            <div class="d-flex flex-column gap-2" style="font-size:13px">
                @foreach($components as $comp)
                <div class="d-flex justify-content-between align-items-center">
                    <span style="color:#64748b">{{ $comp['label'] }}</span>
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:60px;height:5px;border-radius:3px;background:#f1f5f9;overflow:hidden">
                            <div id="{{ $comp['name'] }}_bar"
                                 style="height:100%;background:#1a56db;transition:.3s;width:75%"></div>
                        </div>
                        <span id="{{ $comp['name'] }}_preview" style="font-weight:600;min-width:28px;text-align:right">75</span>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Grade table --}}
            <div class="mt-3 pt-3 border-top">
                <div style="font-size:11px;color:#94a3b8;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px">Tabel Grade</div>
                <div class="row g-1" style="font-size:12px">
                    @foreach(['A'=>['≥ 90','success'],'B'=>['80–89','primary'],'C'=>['70–79','warning'],'D'=>['60–69','secondary'],'E'=>['< 60','danger']] as $grade=>[$range,$color])
                    <div class="col-auto">
                        <span class="badge bg-{{ $color }}-subtle text-{{ $color }}-emphasis">
                            {{ $grade }}: {{ $range }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
const weights = {
    discipline_score: 0.20,
    skill_score: 0.30,
    attitude_score: 0.20,
    knowledge_score: 0.15,
    communication_score: 0.15,
};

function getGrade(score) {
    if (score >= 90) return ['A', '#10b981'];
    if (score >= 80) return ['B', '#1a56db'];
    if (score >= 70) return ['C', '#f59e0b'];
    if (score >= 60) return ['D', '#64748b'];
    return ['E', '#ef4444'];
}

function getScoreColor(score) {
    if (score >= 80) return '#10b981';
    if (score >= 70) return '#f59e0b';
    return '#ef4444';
}

function updateScore(name, value) {
    const v = parseInt(value);

    // Update display di slider
    const display = document.getElementById(name + '_display');
    display.textContent = v;
    display.style.color = getScoreColor(v);

    // Update sidebar preview
    document.getElementById(name + '_preview').textContent = v;
    document.getElementById(name + '_bar').style.width = v + '%';

    // Hitung weighted final score
    let total = 0;
    Object.entries(weights).forEach(([key, w]) => {
        const el = document.getElementById(key);
        total += (el ? parseInt(el.value) : 75) * w;
    });

    const final = Math.round(total * 10) / 10;
    const [grade, gradeColor] = getGrade(final);

    document.getElementById('gaugeScore').textContent = final.toFixed(1);
    document.getElementById('gaugeGrade').textContent = grade;
    document.getElementById('gaugeGrade').style.color  = gradeColor;

    // Update gauge arc
    const circumference = 251.2;
    const offset = circumference - (final / 100) * circumference;
    const fill = document.getElementById('gaugeFill');
    fill.style.strokeDashoffset = offset;
    fill.style.stroke = gradeColor;
}

// Init semua komponen saat load
document.addEventListener('DOMContentLoaded', () => {
    Object.keys(weights).forEach(name => {
        const el = document.getElementById(name);
        if (el) updateScore(name, el.value);
    });
});
</script>
@endpush

@endsection
