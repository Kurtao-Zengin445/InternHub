@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')
@section('page-subtitle', 'Kelola seluruh akun pengguna sistem')

@section('content')

{{-- ── Toolbar ───────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body d-flex flex-wrap gap-3 align-items-end">
        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex flex-wrap gap-2 flex-fill">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" style="max-width:220px;font-size:13.5px"
                   placeholder="Cari nama atau email…">
            <select name="role" class="form-select" style="max-width:160px;font-size:13.5px">
                <option value="">Semua Role</option>
                @foreach(['admin'=>'Administrator','student'=>'Siswa','school'=>'Sekolah','supervisor'=>'Pembimbing','company'=>'Perusahaan'] as $val => $label)
                    <option value="{{ $val }}" {{ request('role') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="form-select" style="max-width:150px;font-size:13.5px">
                <option value="">Semua Status</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button class="btn btn-outline-secondary" style="font-size:13.5px">
                <i class="bi bi-search me-1"></i>Filter
            </button>
            @if(request()->hasAny(['search','role','status']))
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-danger" style="font-size:13.5px">
                    <i class="bi bi-x"></i> Reset
                </a>
            @endif
        </form>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary ms-auto">
            <i class="bi bi-person-plus-fill me-2"></i>Tambah Pengguna
        </a>
    </div>
</div>

{{-- ── Table ────────────────────────────────────── --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Terdaftar</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td style="color:#94a3b8;font-size:12px">{{ $users->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:36px;height:36px;border-radius:9px;background:#eff6ff;color:#1a56db;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-size:13.5px;font-weight:600;color:#0f172a">{{ $user->name }}</div>
                                    <div style="font-size:12px;color:#94a3b8">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $roleMap = [
                                    'admin'      => ['Administrator', '#fef3c7', '#92400e'],
                                    'student'    => ['Siswa',         '#dbeafe', '#1e40af'],
                                    'school'     => ['Sekolah',       '#d1fae5', '#065f46'],
                                    'supervisor' => ['Pembimbing',    '#ede9fe', '#4c1d95'],
                                    'company'    => ['Perusahaan',    '#fee2e2', '#991b1b'],
                                ];
                                [$rLabel, $rBg, $rColor] = $roleMap[$user->role] ?? [$user->role, '#f1f5f9', '#475569'];
                            @endphp
                            <span class="badge-status" style="background: {{ $rBg }}; color: {{ $rColor }}">
                                {{ $rLabel }}
                            </span>
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge-status bg-success-subtle text-success-emphasis">Aktif</span>
                            @else
                                <span class="badge-status bg-danger-subtle text-danger-emphasis">Nonaktif</span>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#94a3b8">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="btn btn-sm btn-outline-secondary py-1" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-sm btn-outline-primary py-1" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm py-1 {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}-circle"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm('Hapus pengguna {{ addslashes($user->name) }}? Tindakan ini tidak bisa dibatalkan.')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-1" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5" style="color:#94a3b8">
                            <i class="bi bi-people" style="font-size:36px;display:block;margin-bottom:8px"></i>
                            Tidak ada pengguna yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2" style="padding:12px 20px">
        <div style="font-size:13px;color:#94a3b8">
            Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} pengguna
        </div>
        {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection