@extends('layouts.app')

@section('title', 'Presensi Hari Ini')
@section('page-title', 'Presensi Hari Ini')
@section('page-subtitle', now()->translatedFormat('l, d F Y'))

@push('styles')
<style>
    .clock-display {
        font-size: 52px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -2px;
        line-height: 1;
        font-variant-numeric: tabular-nums;
    }

    .camera-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 4/3;
        background: #0f172a;
        border-radius: 16px;
        overflow: hidden;
    }

    video {
        transform: scaleX(-1);
    }

    #checkinCamera_video,
    #checkoutCamera_video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scaleX(-1);
    }

    .cam-captured {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
        position: absolute;
        inset: 0;
    }

    .camera-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        padding: 16px;
        background: linear-gradient(transparent 55%, rgba(0, 0, 0, .55));
    }

    .shutter-btn {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #fff;
        border: 4px solid rgba(255, 255, 255, .4);
        cursor: pointer;
        transition: .15s;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        color: #0f172a;
        box-shadow: 0 4px 20px rgba(0, 0, 0, .35);
    }

    .shutter-btn:hover {
        transform: scale(1.06);
    }

    .shutter-btn:active {
        transform: scale(.93);
    }

    .shutter-btn:disabled {
        opacity: .4;
        cursor: not-allowed;
    }

    .cam-status {
        position: absolute;
        top: 10px;
        left: 12px;
        background: rgba(0, 0, 0, .6);
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .cam-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #ef4444;
        animation: blink 1.2s ease infinite;
    }

    .retake-btn {
        position: absolute;
        top: 10px;
        right: 12px;
        background: rgba(0, 0, 0, .65);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: none;
        align-items: center;
        gap: 4px;
    }

    .geo-box {
        border-radius: 12px;
        padding: 12px 14px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 13px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        min-height: 52px;
        transition: .3s;
    }

    .done-card {
        border-radius: 16px;
        padding: 20px;
        text-align: center;
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1
        }

        50% {
            opacity: .25
        }
    }

    @keyframes spin {
        to {
            transform: rotate(360deg)
        }
    }

    .spinner {
        width: 14px;
        height: 14px;
        border: 2px solid currentColor;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin .7s linear infinite;
        display: inline-block;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')

<div class="row justify-content-center g-3">
    <div class="col-xl-9">

        {{-- Jam digital --}}
        <div class="card mb-3 text-center" style="padding:22px 28px">
            <div class="clock-display mb-1" id="liveClock">--:--:--</div>
            <div style="font-size:13.5px;color:#64748b">{{ now()->translatedFormat('l, d F Y') }}</div>
        </div>

        @if(!$hasActiveInternship)
        <div class="card text-center" style="padding:48px">
            <div style="font-size:48px;margin-bottom:12px">📋</div>
            <h5 style="font-weight:700">Belum Ada Magang Aktif</h5>
            <p style="color:#64748b;font-size:14px">Presensi hanya tersedia saat Anda memiliki magang yang sedang berjalan.</p>
            <a href="{{ route('student.applications.index') }}" class="btn btn-primary mx-auto" style="max-width:200px">
                <i class="bi bi-briefcase me-2"></i>Lihat Lowongan Magang
            </a>
        </div>

        @elseif($today)

        @if($today->status === 'present')
        <div class="row g-3 mb-3">
            {{-- Card check in --}}
            <div class="col-md-6">
                <div class="done-card" style="background:#f0fdf4;border:1.5px solid #d1fae5">
                    <div style="font-size:30px;margin-bottom:8px">✅</div>
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#16a34a;margin-bottom:4px">Check In</div>
                    <div style="font-size:38px;font-weight:800;color:#0f172a;letter-spacing:-1.5px">{{ \Carbon\Carbon::parse($today->check_in)->format('H:i') }}</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:2px">{{ \Carbon\Carbon::parse($today->check_in)->format('H:i:s') }}</div>
                    @if($today->check_in_photo)
                    <img src="{{ asset('storage/'.$today->check_in_photo) }}" class="mt-3 w-100 rounded-3" style="max-height:300px; object-fit:cover" alt="Selfie">
                    @endif
                    @if($today->checkin_address)
                    <div class="mt-3 text-start geo-box" style="padding:10px 12px;background:#f0fdf4;border-color:#d1fae5">
                        <i class="bi bi-geo-alt-fill text-success" style="font-size:14px;flex-shrink:0;margin-top:2px"></i>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:#0f172a">Lokasi Check In</div>
                            <div style="font-size:11.5px;color:#64748b;margin-top:1px">{{ Str::limit($today->checkin_address, 80) }}</div>
                            @if($today->checkin_distance !== null)
                            <div style="font-size:11px;color:#94a3b8;margin-top:1px">{{ $today->checkinDistanceLabel() }}</div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Card check out --}}
            <div class="col-md-6">
                @if($today->check_out)
                <div class="done-card" style="background:#eff6ff;border:1.5px solid #bfdbfe">
                    <div style="font-size:30px;margin-bottom:8px">🏁</div>
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#1e40af;margin-bottom:4px">Check Out</div>
                    <div style="font-size:38px;font-weight:800;color:#0f172a;letter-spacing:-1.5px">{{ \Carbon\Carbon::parse($today->check_out)->format('H:i') }}</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:2px">Durasi: <strong>{{ $today->duration() ?? '-' }}</strong></div>
                    @if($today->check_out_photo)
                    <img src="{{ asset('storage/'.$today->check_out_photo) }}" class="mt-3 w-100 rounded-3" style="max-height:300px;object-fit:cover" alt="Selfie">
                    @endif
                    @if($today->checkout_address)
                    <div class="mt-3 text-start geo-box" style="padding:10px 12px;background:#eff6ff;border-color:#bfdbfe">
                        <i class="bi bi-geo-alt-fill text-primary" style="font-size:14px;flex-shrink:0;margin-top:2px"></i>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:#0f172a">Lokasi Check Out</div>
                            <div style="font-size:11.5px;color:#64748b;margin-top:1px">{{ Str::limit($today->checkout_address, 80) }}</div>
                            @if($today->checkout_distance !== null)
                            <div style="font-size:11px;color:#94a3b8;margin-top:1px">{{ $today->checkoutDistanceLabel() }}</div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>





                @else
                {{-- Belum checkout — tampilkan form checkout --}}
                <div class="card">
                    <div class="card-header"><i class="bi bi-box-arrow-right text-primary me-2"></i>Check Out</div>
                    <div class="card-body" style="padding:20px">
                        <form method="POST" action="{{ route('student.attendance.checkout') }}"
                            id="checkoutForm" onsubmit="return validateForm('checkoutForm','checkoutCamera')">
                            @csrf
                            <input type="hidden" name="selfie" id="checkoutForm_selfie">
                            <input type="hidden" name="latitude" id="checkoutForm_lat">
                            <input type="hidden" name="longitude" id="checkoutForm_lng">
                            <input type="hidden" name="address" id="checkoutForm_address">
                            @include('student.attendance.partials.camera-geo', ['camId'=>'checkoutCamera','formId'=>'checkoutForm'])
                            <button type="submit" class="btn btn-primary w-100 py-3 mt-3" style="font-size:15px;font-weight:700">
                                <i class="bi bi-box-arrow-right me-2"></i>Check Out Sekarang
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>

            <div class="mt-3 p-4 text-center rounded-3" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #d1fae5">
                <div style="font-size:34px;margin-bottom:6px">🎉</div>
                <div style="font-size:15px;font-weight:700;color:#065f46">Presensi selesai!</div>
                <div style="font-size:13px;color:#16a34a;margin-top:4px">Total: <strong>{{ $today->duration() }}</strong></div>
                <a href="{{ route('student.reports.create') }}" class="btn btn-success mt-3">
                    <i class="bi bi-pencil-square me-2"></i>Buat Laporan Harian
                </a>
            </div>

            <div>
                <a href="{{ route('student.attendance.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-clock-history me-2"></i>Lihat Riwayat Presensi
                </a>
            </div>
        </div>

        @elseif(in_array($today->status, ['sick','permission']))
        <div class="card text-center" style="padding:40px;border-color:#fde68a;background:#fffbeb">
            <div style="font-size:42px;margin-bottom:12px">{{ $today->status === 'sick' ? '🤒' : '📋' }}</div>
            <div style="font-size:16px;font-weight:700;color:#92400e">{{ $today->status === 'sick' ? 'Sakit' : 'Izin' }} — Hari Ini</div>
            <div style="font-size:13px;color:#78350f;margin-top:6px">{{ $today->notes }}</div>
        </div>
        @else
        <div class="card text-center" style="padding:40px;border-color:#fecaca;background:#fef2f2">
            <div style="font-size:42px;margin-bottom:12px">⚠️</div>
            <div style="font-size:16px;font-weight:700;color:#991b1b">Alpha — Tidak Hadir</div>
        </div>
        @endif

        @else
        {{-- ══ BELUM PRESENSI ══ --}}
        <div class="row g-3">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header"><i class="bi bi-calendar-check-fill text-primary me-2"></i>Presensi Hari Ini</div>
                    <div class="card-body" style="padding:20px">

                        {{-- Tabs --}}
                        <div class="d-flex gap-2 mb-4">
                            <button class="btn btn-primary px-4" id="tab-hadir" onclick="switchTab('hadir')"><i class="bi bi-person-check me-1"></i>Hadir</button>
                            <button class="btn btn-outline-secondary px-4" id="tab-sakit" onclick="switchTab('sakit')"><i class="bi bi-thermometer me-1"></i>Sakit</button>
                            <button class="btn btn-outline-secondary px-4" id="tab-izin" onclick="switchTab('izin')"><i class="bi bi-file-earmark-text me-1"></i>Izin</button>
                        </div>

                        {{-- Form Check In --}}
                        <div id="form-hadir">
                            <form method="POST" action="{{ route('student.attendance.checkin') }}"
                                id="checkinForm" onsubmit="return validateForm('checkinForm','checkinCamera')">
                                @csrf
                                <input type="hidden" name="selfie" id="checkinForm_selfie">
                                <input type="hidden" name="latitude" id="checkinForm_lat">
                                <input type="hidden" name="longitude" id="checkinForm_lng">
                                <input type="hidden" name="address" id="checkinForm_address">

                                @include('student.attendance.partials.camera-geo', ['camId'=>'checkinCamera','formId'=>'checkinForm'])

                                <div class="mb-3 mt-3">
                                    <label class="form-label fw-semibold" style="font-size:13.5px">Catatan</label>
                                    <input type="text" name="notes" class="form-control" placeholder="Catatan tambahan (opsional)">
                                </div>

                                <button type="submit" class="btn btn-success w-100 py-3" style="font-size:15px;font-weight:700">
                                    <i class="bi bi-calendar-check me-2"></i>Check In Sekarang
                                </button>
                            </form>
                        </div>

                        {{-- Form Sakit/Izin --}}
                        <div id="form-leave" style="display:none">
                            <form method="POST" action="{{ route('student.attendance.leave') }}">
                                @csrf
                                <input type="hidden" name="status" id="leaveStatus" value="sick">
                                <div class="mb-3 p-3 rounded-3" style="background:#fffbeb;border:1px solid #fde68a">
                                    <div style="font-size:13px;font-weight:600;color:#92400e"><i class="bi bi-info-circle me-1"></i>Keterangan Tidak Hadir</div>
                                    <div style="font-size:12.5px;color:#78350f;margin-top:2px">Pastikan keterangan yang Anda berikan akurat dan jujur.</div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold" style="font-size:13.5px">Alasan <span class="text-danger">*</span></label>
                                    <textarea name="notes" rows="5" class="form-control @error('notes') is-invalid @enderror"
                                        placeholder="Jelaskan alasan sakit atau izin Anda dengan lengkap…"
                                        required minlength="10">{{ old('notes') }}</textarea>
                                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-text">Minimal 10 karakter.</div>
                                </div>
                                <button type="submit" class="btn btn-warning w-100 py-3" style="font-size:15px;font-weight:700">
                                    <i class="bi bi-send me-2"></i>Kirim Keterangan
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-xl-4 d-flex flex-column gap-3">
                <div class="card">
                    <div class="card-header"><i class="bi bi-building text-warning me-2"></i>Lokasi Magang</div>
                    <div class="card-body" style="font-size:13px;padding:16px 20px">
                        <div style="font-weight:700;color:#0f172a;margin-bottom:2px">{{ $internship->application->program->company->name }}</div>
                        <div style="color:#64748b;font-size:12.5px;margin-bottom:12px">{{ $internship->application->program->company->address }}</div>
                        @if($hasCompanyCoord)
                        <div class="d-flex align-items-center gap-2 p-2 rounded-2" style="background:#d1fae5;font-size:12px">
                            <i class="bi bi-shield-check text-success"></i>
                            <span style="color:#065f46;font-weight:600">Geofencing aktif · radius {{ $allowedRadius }}m</span>
                        </div>
                        @else
                        <div class="d-flex align-items-center gap-2 p-2 rounded-2" style="background:#fef3c7;font-size:12px">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                            <span style="color:#92400e;font-weight:600">Geofencing belum dikonfigurasi</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="card flex-fill">
                    <div class="card-header"><i class="bi bi-pie-chart text-success me-2"></i>Rekapitulasi</div>
                    <div class="card-body" style="font-size:13px;padding:16px 20px">
                        @php
                        $atts = $internship->attendance;
                        $present = $atts->where('status','present')->count();
                        $sick = $atts->where('status','sick')->count();
                        $perm = $atts->where('status','permission')->count();
                        $absent = $atts->where('status','absent')->count();
                        $total = max($present+$sick+$perm+$absent,1);
                        $pct = round(($present/$total)*100);
                        @endphp
                        <div class="d-flex justify-content-between mb-2"><span style="color:#94a3b8">Hadir</span><span class="text-success fw-semibold">{{ $present }} hari</span></div>
                        <div class="d-flex justify-content-between mb-2"><span style="color:#94a3b8">Sakit</span><span style="color:#f59e0b;font-weight:600">{{ $sick }} hari</span></div>
                        <div class="d-flex justify-content-between mb-2"><span style="color:#94a3b8">Izin</span><span style="color:#3b82f6;font-weight:600">{{ $perm }} hari</span></div>
                        <div class="d-flex justify-content-between mb-3"><span style="color:#94a3b8">Alpha</span><span class="text-danger fw-semibold">{{ $absent }} hari</span></div>
                        <div class="progress mb-2" style="height:8px;border-radius:6px">
                            <div class="progress-bar bg-success" style="width: {{ $pct }}%"></div>
                        </div>
                        <div class="text-center" style="font-size:12px;color:#94a3b8">Kehadiran: <strong style="color:#0f172a">{{ $pct }}%</strong></div>
                    </div>
                </div>

                <a href="{{ route('student.attendance.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-clock-history me-2"></i>Lihat Riwayat Presensi
                </a>
            </div>
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
    // ── Jam digital ──────────────────────────────────────
    (function() {
        const el = document.getElementById('liveClock');
        const tick = () => {
            const n = new Date();
            el.textContent = [n.getHours(), n.getMinutes(), n.getSeconds()].map(x => String(x).padStart(2, '0')).join(':');
        };
        tick();
        setInterval(tick, 1000);
    })();

    // ── Tab switch ───────────────────────────────────────
    function switchTab(tab) {
        document.getElementById('form-hadir').style.display = tab === 'hadir' ? '' : 'none';
        document.getElementById('form-leave').style.display = tab !== 'hadir' ? '' : 'none';
        if (document.getElementById('leaveStatus')) document.getElementById('leaveStatus').value = tab;
        ['hadir', 'sakit', 'izin'].forEach(t => {
            const b = document.getElementById('tab-' + t);
            if (b) b.className = t === tab ? 'btn btn-primary px-4' : 'btn btn-outline-secondary px-4';
        });
    }

    // ── Camera registry ──────────────────────────────────
    const _cameras = {};

    function snap(camId) {
        const c = _cameras[camId];
        if (!c) return;

        if (c.video.videoWidth === 0) {
            alert('Kamera belum siap');
            return;
        }

        c.canvas.width = c.video.videoWidth;
        c.canvas.height = c.video.videoHeight;

        const ctx = c.canvas.getContext('2d');
        // mirror horizontal
        ctx.save();
        ctx.scale(-1, 1);
        ctx.drawImage(
            c.video,
            -c.canvas.width,
            0,
            c.canvas.width,
            c.canvas.height
        );
        ctx.restore();

        const dataURL = c.canvas.toDataURL('image/jpeg');

        // ⬇️ INI YANG BENAR
        c.captured.src = dataURL;
        c.captured.style.display = 'block';

        c.video.style.display = 'none';
        c.shutter.style.display = 'none';
        c.retake.style.display = 'flex';

        c.selfieIn.value = dataURL;

        console.log("Captured OK");
    }

    function registerCamera(camId, formId) {
        _cameras[camId] = {
            video: document.getElementById(camId + '_video'),
            canvas: document.getElementById(camId + '_canvas'),
            captured: document.getElementById(camId + '_captured'),
            shutter: document.getElementById(camId + '_shutter'),
            retake: document.getElementById(camId + '_retake'),
            dot: document.getElementById(camId + '_dot'),
            statusText: document.getElementById(camId + '_statusText'),
            selfieIn: document.getElementById(formId + '_selfie'),
            stream: null,
        };
        const c = _cameras[camId];
        c.shutter.addEventListener('click', () => snap(camId));
        c.retake.addEventListener('click', () => doRetake(camId));
        initCamera(camId);
    }

    function initCamera(camId) {
        const c = _cameras[camId];
        if (!c) return;

        c.shutter.disabled = true;
        c.video.setAttribute('playsinline', true);

        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then(stream => {
                c.stream = stream;
                c.video.srcObject = stream;

                // ❗ EVENT PALING STABIL
                c.video.onloadedmetadata = () => {


                    c.dot.style.background = '#22c55e';
                    c.statusText.textContent = 'Kamera aktif';
                    c.shutter.disabled = false;
                };

                return c.video.play();
            })
            .catch(err => {
                console.error(err);
                c.dot.style.background = '#ef4444';
                c.statusText.textContent = 'Kamera tidak dapat diakses';
            });
    }

    function doRetake(camId) {
        const c = _cameras[camId];
        if (!c) return;
        console.log("RETAAAAAKE CLICKED");
        // tampilkan video lagi
        c.video.style.display = 'block';

        // sembunyikan hasil foto
        c.captured.style.display = 'none';

        // tombol shutter muncul lagi
        c.shutter.style.display = 'flex';

        // tombol retake disembunyikan
        c.retake.style.display = 'none';

        // reset input
        if (c.selfieIn) {
            c.selfieIn.value = '';
        }

        console.log("Retake OK");
    }

    // ── Geolocation ──────────────────────────────────────
    function initGeo(formId) {
        const box = document.getElementById(formId + '_geoBox');
        const txt = document.getElementById(formId + '_geoTxt');
        const latI = document.getElementById(formId + '_lat');
        const lngI = document.getElementById(formId + '_lng');
        const adrI = document.getElementById(formId + '_address');

        if (!navigator.geolocation) {
            box.style.cssText += 'background:#fef2f2;border-color:#fecaca';
            txt.innerHTML = '<i class="bi bi-x-circle" style="color:#ef4444"></i> <span style="color:#991b1b">GPS tidak didukung browser ini.</span>';
            return;
        }

        txt.innerHTML = '<span class="spinner" style="margin-right:6px"></span> Mendeteksi lokasi Anda…';

        navigator.geolocation.getCurrentPosition(async pos => {
            const lat = pos.coords.latitude,
                lng = pos.coords.longitude,
                acc = Math.round(pos.coords.accuracy);
            latI.value = lat;
            lngI.value = lng;
            box.style.cssText += 'background:#f0fdf4;border-color:#d1fae5';
            try {
                const r = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`, {
                    headers: {
                        'Accept-Language': 'id'
                    }
                });
                const d = await r.json();
                const addr = d.display_name || `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                adrI.value = addr.substring(0, 250);
                txt.innerHTML = `<i class="bi bi-geo-alt-fill" style="color:#16a34a;flex-shrink:0;margin-top:2px"></i>
                <div><div style="font-size:12px;font-weight:600;color:#065f46">${addr.substring(0,70)}…</div>
                <div style="font-size:11px;color:#94a3b8;margin-top:1px">Akurasi ±${acc}m</div></div>`;
            } catch {
                adrI.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                txt.innerHTML = `<i class="bi bi-geo-alt-fill" style="color:#16a34a"></i>
                <span style="color:#065f46;font-weight:600">${lat.toFixed(5)}, ${lng.toFixed(5)} · ±${acc}m</span>`;
            }
        }, err => {
            box.style.cssText += 'background:#fef2f2;border-color:#fecaca';
            const msgs = {
                1: 'Izin lokasi ditolak. Aktifkan di pengaturan browser.',
                2: 'Lokasi tidak dapat ditentukan.',
                3: 'Waktu habis. Coba lagi.'
            };
            txt.innerHTML = `<i class="bi bi-x-circle" style="color:#ef4444"></i> <span style="color:#991b1b">${msgs[err.code]||'GPS error.'}</span>`;
        }, {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        });
    }

    // ── Submit validation ────────────────────────────────
    function validateForm(formId, camId) {
        const selfieIn = document.getElementById(formId + '_selfie');
        if (!selfieIn || !selfieIn.value) {
            alert('📸 Foto selfie wajib diambil sebelum presensi!');
            return false;
        }
        if (!document.getElementById(formId + '_lat').value) {
            alert('Lokasi belum terdeteksi. Pastikan GPS aktif dan tunggu beberapa detik.');
            return false;
        }
        return true;
    }
</script>
@endpush

@endsection