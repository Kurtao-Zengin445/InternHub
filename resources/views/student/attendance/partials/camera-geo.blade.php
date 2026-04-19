<style>
    .camera-overlay {
        pointer-events: none;
    }

    .camera-overlay button {
        pointer-events: auto;
    }

    .video {
        transform: scaleX(-1);
    }
</style>


{{--
    Partial: camera-geo
    Variables:
        $camId   — ID unik untuk elemen kamera (contoh: 'checkinCamera')
        $formId  — ID form yang akan menerima selfie + koordinat (contoh: 'checkinForm')
--}}

{{-- Kamera selfie --}}
<div class="mb-3">
    <label class="form-label fw-semibold" style="font-size:13.5px">
        Foto Selfie <span class="text-danger">*</span>
    </label>

    <div class="camera-wrap mb-2">
        {{-- Live feed kamera --}}
        <video id="{{ $camId }}_video" autoplay playsinline muted poster=""></video>

        {{-- Foto yang sudah diambil --}}
        <img id="{{ $camId }}_captured" class="cam-captured" alt="Selfie">

        {{-- Canvas tersembunyi untuk capture --}}
        <canvas id="{{ $camId }}_canvas" style="display:none"></canvas>

        {{-- Status kamera --}}
        <div class="cam-status">
            <span class="cam-dot" id="{{ $camId }}_dot"></span>
            <span id="{{ $camId }}_statusText">Memuat kamera…</span>
        </div>

        {{-- Tombol retake (muncul setelah foto diambil) --}}
        <button type="button" class="retake-btn" id="{{ $camId }}_retake">
            <i class="bi bi-arrow-counterclockwise"></i> Foto Ulang
        </button>

        {{-- Overlay + tombol rana --}}
        <div class="camera-overlay">
            <button type="button" class="shutter-btn" id="{{ $camId }}_shutter"
                disabled title="Ambil foto selfie">
                📷
            </button>
        </div>
    </div>

    <div style="font-size:12px;color:#94a3b8">
        <i class="bi bi-info-circle me-1"></i>
        Pastikan wajah Anda terlihat jelas, cukup cahaya, dan tidak menggunakan masker.
    </div>
</div>

{{-- Geolocation --}}
<div class="mb-2">
    <label class="form-label fw-semibold" style="font-size:13.5px">
        Lokasi Saat Ini <span class="text-danger">*</span>
    </label>
    <div class="geo-box" id="{{ $formId }}_geoBox">
        <div id="{{ $formId }}_geoTxt" class="d-flex align-items-center gap-2" style="flex:1">
            <span class="spinner" style="margin-right:4px"></span>
            Mendeteksi lokasi Anda…
        </div>
    </div>
    <div style="font-size:12px;color:#94a3b8;margin-top:5px">
        <i class="bi bi-shield-lock me-1"></i>
        Lokasi hanya digunakan untuk verifikasi presensi dan tidak disimpan secara permanen.
    </div>
</div>

{{-- Init JS setelah elemen ada di DOM --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        registerCamera('{{ $camId }}', '{{ $formId }}');
        initGeo('{{ $formId }}');
    });
</script>