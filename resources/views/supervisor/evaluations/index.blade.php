@extends('layouts.app')

@section('title', 'Penilaian Siswa')
@section('page-title', 'Penilaian Siswa')
@section('page-subtitle', 'Evaluasi akhir siswa bimbingan')

@section('content')

<div class="row g-3">
    @forelse($internships as $internship)
    @php
        $student = $internship->application->student;
        $eval    = $internship->supervisor_evaluation;
        $companyEval = $internship->companyEvaluation();
        $finalScore  = $internship->finalScore();
    @endphp

    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-body" style="padding:22px">

                {{-- Header siswa --}}
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:46px;height:46px;border-radius:12px;background:#eff6ff;color:#1a56db;font-size:18px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        {{ strtoupper(substr($student->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-fill">
                        <div style="font-size:14px;font-weight:700;color:#0f172a">{{ $student->user->name }}</div>
                        <div style="font-size:12px;color:#94a3b8">
                            {{ $student->class }} · {{ $internship->application->program->company->name }}
                        </div>
                    </div>
                    @if($finalScore !== null)
                        <div class="text-center">
                            <div style="font-size:24px;font-weight:800;color:#1a56db;letter-spacing:-1px">
                                {{ number_format($finalScore, 1) }}
                            </div>
                            <div style="font-size:11px;color:#94a3b8">Nilai Akhir</div>
                        </div>
                    @endif
                </div>

                {{-- Status penilaian --}}
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 rounded-2 text-center"
                             style="background:{{ $eval ? '#f0fdf4' : '#f8fafc' }};border:1px solid {{ $eval ? '#d1fae5' : '#e2e8f0' }}">
                            <div style="font-size:11px;color:{{ $eval ? '#065f46' : '#94a3b8' }};font-weight:600;margin-bottom:2px">
                                Nilai Sekolah
                            </div>
                            @if($eval)
                                <div style="font-size:18px;font-weight:800;color:#065f46">
                                    {{ number_format($eval->final_score, 1) }}
                                </div>
                                <div style="font-size:11px;color:#16a34a">Grade {{ $eval->grade_letter }}</div>
                            @else
                                <div style="font-size:13px;color:#94a3b8">Belum dinilai</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded-2 text-center"
                             style="background:{{ $companyEval ? '#eff6ff' : '#f8fafc' }};border:1px solid {{ $companyEval ? '#bfdbfe' : '#e2e8f0' }}">
                            <div style="font-size:11px;color:{{ $companyEval ? '#1e40af' : '#94a3b8' }};font-weight:600;margin-bottom:2px">
                                Nilai Perusahaan
                            </div>
                            @if($companyEval)
                                <div style="font-size:18px;font-weight:800;color:#1e40af">
                                    {{ number_format($companyEval->final_score, 1) }}
                                </div>
                                <div style="font-size:11px;color:#3b82f6">Grade {{ $companyEval->grade_letter }}</div>
                            @else
                                <div style="font-size:13px;color:#94a3b8">Belum dinilai</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Aksi --}}
                <div class="d-flex gap-2 pt-2 border-top">
                    @if($eval)
                        <a href="{{ route('supervisor.evaluations.show', $eval) }}"
                           class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-eye me-1"></i>Lihat Nilai
                        </a>
                        <a href="{{ route('supervisor.evaluations.edit', $eval) }}"
                           class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i>Edit Nilai
                        </a>
                    @else
                        <a href="{{ route('supervisor.evaluations.create', $internship) }}"
                           class="btn btn-primary btn-sm">
                            <i class="bi bi-patch-check me-1"></i>Beri Penilaian
                        </a>
                    @endif
                    <a href="{{ route('supervisor.internships.show', $internship) }}"
                       class="btn btn-outline-secondary btn-sm ms-auto">
                        <i class="bi bi-person-workspace me-1"></i>Profil
                    </a>
                </div>

            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card text-center" style="padding:60px">
            <div style="font-size:48px;margin-bottom:12px">📊</div>
            <h5 style="font-weight:700;color:#0f172a">Belum ada siswa yang perlu dinilai</h5>
            <p style="color:#64748b;font-size:14px">Penilaian tersedia setelah siswa bimbingan memiliki magang aktif atau selesai.</p>
        </div>
    </div>
    @endforelse
</div>

@endsection
