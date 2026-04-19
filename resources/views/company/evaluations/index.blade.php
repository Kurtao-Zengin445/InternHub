@extends('layouts.app')

@section('title', 'Penilaian Peserta')
@section('page-title', 'Penilaian Peserta Magang')
@section('page-subtitle', 'Evaluasi akhir peserta magang di perusahaan')

@section('content')

<div class="row g-3">
    @forelse($internships as $internship)
    @php
        $studentUser = $internship->application->student;
        $student = $internship->application->studentModel;
        $companyEval = $internship->company_evaluation;
        $supervisorEval = $internship->supervisorEvaluation();
        $finalScore  = $internship->finalScore();
    @endphp

    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-body" style="padding:22px">

                {{-- Header --}}
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:46px;height:46px;border-radius:12px;background:#ede9fe;color:#4c1d95;font-size:18px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        {{ strtoupper(substr($studentUser->name ?? '?', 0, 1)) }}
                    </div>
                    <div class="flex-fill">
                        <div style="font-size:14px;font-weight:700;color:#0f172a">{{ $studentUser->name ?? '-' }}</div>
                        <div style="font-size:12px;color:#94a3b8">
                            {{ $student?->school->name ?? '-' }} · {{ $internship->application->program->title }}
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

                {{-- Status nilai --}}
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 rounded-2 text-center"
                             style="background:{{ $companyEval ? '#eff6ff' : '#f8fafc' }};border:1px solid {{ $companyEval ? '#bfdbfe' : '#e2e8f0' }}">
                            <div style="font-size:11px;color:{{ $companyEval ? '#1e40af' : '#94a3b8' }};font-weight:600;margin-bottom:2px">Nilai Perusahaan</div>
                            @if($companyEval)
                                <div style="font-size:18px;font-weight:800;color:#1e40af">{{ number_format($companyEval->final_score, 1) }}</div>
                                <div style="font-size:11px;color:#3b82f6">Grade {{ $companyEval->grade_letter }}</div>
                            @else
                                <div style="font-size:13px;color:#94a3b8">Belum dinilai</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded-2 text-center"
                             style="background:{{ $supervisorEval ? '#f0fdf4' : '#f8fafc' }};border:1px solid {{ $supervisorEval ? '#d1fae5' : '#e2e8f0' }}">
                            <div style="font-size:11px;color:{{ $supervisorEval ? '#065f46' : '#94a3b8' }};font-weight:600;margin-bottom:2px">Nilai Sekolah</div>
                            @if($supervisorEval)
                                <div style="font-size:18px;font-weight:800;color:#065f46">{{ number_format($supervisorEval->final_score, 1) }}</div>
                                <div style="font-size:11px;color:#16a34a">Grade {{ $supervisorEval->grade_letter }}</div>
                            @else
                                <div style="font-size:13px;color:#94a3b8">Belum dinilai</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Aksi --}}
                <div class="d-flex gap-2 pt-2 border-top">
                    @if($companyEval)
                        <a href="{{ route('company.evaluations.show', $companyEval) }}"
                           class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-eye me-1"></i>Lihat Nilai
                        </a>
                        <a href="{{ route('company.evaluations.edit', $companyEval) }}"
                           class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                    @else
                        <a href="{{ route('company.evaluations.create', $internship) }}"
                           class="btn btn-primary btn-sm">
                            <i class="bi bi-patch-check me-1"></i>Beri Penilaian
                        </a>
                    @endif
                    <a href="{{ route('company.internships.show', $internship) }}"
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
            <h5 style="font-weight:700;color:#0f172a">Belum ada peserta yang perlu dinilai</h5>
            <p style="color:#64748b;font-size:14px">Penilaian tersedia setelah ada peserta magang yang aktif atau selesai.</p>
        </div>
    </div>
    @endforelse
</div>

@endsection
