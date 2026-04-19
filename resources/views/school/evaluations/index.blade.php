@extends('layouts.app')

@section('title', 'Penilaian Siswa')
@section('page-title', 'Penilaian Siswa')
@section('page-subtitle', 'Evaluasi akhir siswa magang')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            @if($evaluations->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-clipboard-check fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada penilaian</h5>
                        <p class="text-muted mb-4">Anda belum memberikan penilaian kepada siswa magang.</p>
                        <a href="{{ route('school.students.index') }}" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>Lihat Siswa Magang
                        </a>
                    </div>
                </div>
            @else
                <div class="row g-3">
                    @foreach($evaluations as $evaluation)
                        <div class="col-xl-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    {{-- Header siswa --}}
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div style="width:46px;height:46px;border-radius:12px;background:#eff6ff;color:#1a56db;font-size:18px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                            {{ strtoupper(substr($evaluation->internship->student->user->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-fill">
                                            <div style="font-size:14px;font-weight:700;color:#0f172a">
                                                {{ $evaluation->internship->student->user->name }}
                                            </div>
                                            <div style="font-size:12px;color:#94a3b8">
                                                {{ $evaluation->internship->student->class }} · {{ $evaluation->internship->program->company->name }}
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <div style="font-size:24px;font-weight:800;color:#1a56db;letter-spacing:-1px">
                                                {{ number_format($evaluation->final_score, 1) }}
                                            </div>
                                            <div style="font-size:11px;color:#94a3b8">Nilai Akhir</div>
                                        </div>
                                    </div>

                                    {{-- Status penilaian --}}
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="p-2 rounded-2 text-center"
                                                 style="background:#f0fdf4;border:1px solid #d1fae5">
                                                <div style="font-size:11px;color:#065f46;font-weight:600;margin-bottom:2px">
                                                    Nilai Sekolah
                                                </div>
                                                <div style="font-size:18px;font-weight:800;color:#065f46">
                                                    {{ number_format($evaluation->final_score, 1) }}
                                                </div>
                                                <div style="font-size:11px;color:#16a34a">Grade {{ $evaluation->grade_letter }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-2 rounded-2 text-center"
                                                 style="background:#f8fafc;border:1px solid #e2e8f0">
                                                <div style="font-size:11px;color:#94a3b8;font-weight:600;margin-bottom:2px">
                                                    Nilai Perusahaan
                                                </div>
                                                @php
                                                    $companyEval = $evaluation->internship->companyEvaluation();
                                                @endphp
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

                                    {{-- Tanggal penilaian --}}
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            Dinilai pada {{ $evaluation->evaluated_at_formatted }}
                                        </small>
                                    </div>

                                    {{-- Aksi --}}
                                    <div class="d-flex gap-2 pt-2 border-top">
                                        <a href="{{ route('school.evaluations.show', $evaluation) }}"
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-eye me-1"></i>Lihat Detail
                                        </a>
                                        <a href="{{ route('school.evaluations.edit', $evaluation) }}"
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-pencil me-1"></i>Edit Nilai
                                        </a>
                                        <a href="{{ route('school.internships.show', $evaluation->internship) }}"
                                           class="btn btn-outline-secondary btn-sm ms-auto">
                                            <i class="bi bi-person-workspace me-1"></i>Profil Siswa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $evaluations->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection