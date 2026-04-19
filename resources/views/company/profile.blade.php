@extends('layouts.app')

@section('title', 'Pengaturan Lokasi Presensi')
@section('page-title', 'Pengaturan Lokasi Presensi')
@section('page-subtitle', $company->name)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <form method="POST" action="{{ route('company.profile.update') }}" class="needs-validation" novalidate>
                @csrf @method('PUT')
                <input type="hidden" name="name" value="{{ old('name', $company->name) }}">
<!-- 
                {{-- Company Information Card --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <a href="{{ route('company.dashboard') }}" class="btn btn-sm btn-outline-secondary py-1 px-2 d-none d-md-inline-flex">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                        <i class="bi bi-building text-primary me-1"></i>
                        <span class="fw-semibold">Informasi Perusahaan</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Nama Perusahaan <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $company->name) }}"
                                       class="form-control @error('name') is-invalid @enderror" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Telepon</label>
                                <input type="tel" name="phone" value="{{ old('phone', $company->phone) }}"
                                       class="form-control" placeholder="081234567890">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Email Kontak</label>
                                <input type="email" name="email" value="{{ old('email', $company->email) }}"
                                       class="form-control" placeholder="contact@company.com">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Website (opsional)</label>
                                <input type="url" name="website" value="{{ old('website', $company->website) }}"
                                       class="form-control" placeholder="https://company.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror"
                                          placeholder="Jl. Sudirman No. 123, Jakarta Selatan" required>{{ old('address', $company->address) }}</textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div> -->

                {{-- Geofencing Settings Card --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <a href="{{ route('company.dashboard') }}" class="btn btn-sm btn-outline-secondary py-1 px-2 d-none d-md-inline-flex">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                    <div class="card-header">
                        <i class="bi bi-geo-alt-fill text-success me-2"></i>
                        <span class="fw-semibold">Pengaturan Geofencing Presensi</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Cari alamat kantor</label>
                                <input type="text" name="address" id="addressSearch" class="form-control form-control-sm mb-3"
                                       placeholder="Cari alamat kantor..."
                                       value="{{ old('address', $company->address) }}" required>
                                @error('address') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                <label class="form-label fw-semibold">Koordinat Presensi</label>
                                <div class="row g-2">
                                    <div class="col-12 col-sm-6">
                                        <input type="number" step="any" name="latitude" id="latitudeInput"
                                               value="{{ old('latitude', $company->latitude) }}"
                                               class="form-control form-control-sm" placeholder="-6.2088" readonly>
                                        <small class="text-muted">Latitude</small>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <input type="number" step="any" name="longitude" id="longitudeInput"
                                               value="{{ old('longitude', $company->longitude) }}"
                                               class="form-control form-control-sm" placeholder="106.8456" readonly>
                                        <small class="text-muted">Longitude</small>
                                    </div>
                                </div><br>

                                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="detectLocation(event)">
                                    <i class="bi bi-crosshair2 me-1"></i>Deteksi Lokasi Kantor Saya
                                </button>

                                <div class="form-text mt-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Koordinat kantor akan terisi otomatis. Pastikan Anda berada di lokasi kantor saat mendeteksi.
                                </div>

                                <div id="locationChangeNotice" class="alert alert-info mt-3 d-none" role="alert">
                                    <i class="bi bi-bell-fill me-2"></i>
                                    Perubahan lokasi presensi terdeteksi. Simpan untuk memperbarui alamat, koordinat, atau radius.
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Radius Presensi (meter)</label>
                                <input type="range" name="allowed_radius" id="radiusSlider" min="100" max="2000" step="50"
                                       value="{{ old('allowed_radius', $company->allowed_radius ?? 500) }}"
                                       class="form-range" oninput="updateRadiusDisplay()">
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span id="radiusValue" class="fw-semibold text-primary">{{ $company->allowed_radius ?? 500 }} meter</span>
                                    <span class="badge bg-success">Aktif</span>
                                </div>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Siswa harus berada dalam radius ini dari lokasi kantor untuk presensi berhasil
                                </div>
                            </div>
                        </div>

                        {{-- Map Preview --}}
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="mb-0 fw-semibold">
                                    <i class="bi bi-map me-1"></i>Preview Lokasi
                                </label>
                                @if($company->latitude && $company->longitude)
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $company->latitude }},{{ $company->longitude }}"
                                       target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>Buka di Maps
                                    </a>
                                @endif
                            </div>
                            <div id="mapPreview" class="rounded border"
                                 style="height: 250px; background: #f8fafc; cursor: crosshair; position: relative;"
                                 onclick="mapClick(event)">
                                @if($company->latitude && $company->longitude)
                                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                        <div class="text-center">
                                            <i class="bi bi-geo-alt-fill fs-1 text-primary mb-2"></i>
                                            <div>Lokasi kantor telah ditentukan</div>
                                            <small>Klik untuk mengubah lokasi</small>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                        <div class="text-center">
                                            <i class="bi bi-geo-alt fs-1 mb-2"></i>
                                            <div>Klik peta untuk tentukan lokasi kantor</div>
                                            <small class="d-block">Atau gunakan tombol deteksi GPS</small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end">
                    <a href="{{ route('company.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initGoogleMap" async defer></script>
<script>
    let map, marker, circle, autocomplete;
    const originalLocation = {
        address: @json($company->address),
        latitude: @json($company->latitude),
        longitude: @json($company->longitude),
        allowed_radius: @json($company->allowed_radius ?? 500),
    };

    function checkLocationChange() {
        const address = document.getElementById('addressSearch')?.value.trim() || ''
        const lat = document.getElementById('latitudeInput')?.value || ''
        const lng = document.getElementById('longitudeInput')?.value || ''
        const radius = document.getElementById('radiusSlider')?.value || ''
        const notice = document.getElementById('locationChangeNotice')

        const originalAddress = originalLocation.address ?? ''
        const originalLatitude = originalLocation.latitude !== null && originalLocation.latitude !== undefined ? originalLocation.latitude.toString() : ''
        const originalLongitude = originalLocation.longitude !== null && originalLocation.longitude !== undefined ? originalLocation.longitude.toString() : ''
        const originalRadius = originalLocation.allowed_radius !== null && originalLocation.allowed_radius !== undefined ? originalLocation.allowed_radius.toString() : ''

        const changed = (
            address !== originalAddress ||
            lat !== originalLatitude ||
            lng !== originalLongitude ||
            radius !== originalRadius
        )

        if (notice) {
            notice.classList.toggle('d-none', !changed)
        }
    }

    // Form validation
    (function () {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()

    // Radius slider
    function updateRadiusDisplay() {
        const slider = document.getElementById('radiusSlider')
        const display = document.getElementById('radiusValue')
        display.textContent = slider.value + ' meter'

        if (window.google && map) {
            updateMap()
        }
    }

    function attachLocationChangeListeners() {
        const addressSearch = document.getElementById('addressSearch')
        const latitudeInput = document.getElementById('latitudeInput')
        const longitudeInput = document.getElementById('longitudeInput')
        const radiusSlider = document.getElementById('radiusSlider')

        if (addressSearch) {
            addressSearch.addEventListener('input', checkLocationChange)
        }
        if (latitudeInput) {
            latitudeInput.addEventListener('change', checkLocationChange)
        }
        if (longitudeInput) {
            longitudeInput.addEventListener('change', checkLocationChange)
        }
        if (radiusSlider) {
            radiusSlider.addEventListener('input', function() {
                updateRadiusDisplay()
                checkLocationChange()
            })
        }
    }

    // Initialize radius display
    document.addEventListener('DOMContentLoaded', function() {
        updateRadiusDisplay()
        attachLocationChangeListeners()
        checkLocationChange()
    })

    // Location detection
    function detectLocation(event) {
        if (!navigator.geolocation) {
            alert('Geolocation tidak didukung oleh browser Anda')
            return
        }

        const button = event.target.closest('button')
        const originalText = button.innerHTML
        button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Mendeteksi...'
        button.disabled = true

        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitudeInput').value = position.coords.latitude
                document.getElementById('longitudeInput').value = position.coords.longitude

                // Update map preview
                if (window.google && typeof initGoogleMap === 'function') {
                    if (!map) {
                        initGoogleMap()
                    } else {
                        updateMap()
                    }
                }

                button.innerHTML = originalText
                button.disabled = false
            },
            function(error) {
                let message = 'Gagal mendeteksi lokasi'
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        message = 'Akses lokasi ditolak. Izinkan akses lokasi di browser Anda.'
                        break
                    case error.POSITION_UNAVAILABLE:
                        message = 'Informasi lokasi tidak tersedia.'
                        break
                    case error.TIMEOUT:
                        message = 'Waktu mendeteksi lokasi habis.'
                        break
                }
                alert(message)
                button.innerHTML = originalText
                button.disabled = false
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000
            }
        )
    }

    // Map click handler
    function mapClick(event) {
        if (window.google && map && event.latLng) {
            document.getElementById('latitudeInput').value = event.latLng.lat().toFixed(6)
            document.getElementById('longitudeInput').value = event.latLng.lng().toFixed(6)
            updateMap()
            return
        }
        alert('Untuk implementasi peta interaktif, pastikan Google Maps API key benar dan script Google Maps sudah dimuat.')
    }

    function initGoogleMap() {
        const lat = parseFloat(document.getElementById('latitudeInput').value)
        const lng = parseFloat(document.getElementById('longitudeInput').value)
        const hasCoords = !isNaN(lat) && !isNaN(lng)
        const defaultCenter = { lat: -6.2088, lng: 106.8456 }

        map = new google.maps.Map(document.getElementById('mapPreview'), {
            center: hasCoords ? { lat, lng } : defaultCenter,
            zoom: hasCoords ? 16 : 5,
            streetViewControl: false,
            fullscreenControl: false,
        })

        if (hasCoords) {
            marker = new google.maps.Marker({
                position: { lat, lng },
                map,
                draggable: true,
            })

            marker.addListener('dragend', function(e) {
                document.getElementById('latitudeInput').value = e.latLng.lat().toFixed(6)
                document.getElementById('longitudeInput').value = e.latLng.lng().toFixed(6)
                updateMap()
            })
        }

        map.addListener('click', function(e) {
            document.getElementById('latitudeInput').value = e.latLng.lat().toFixed(6)
            document.getElementById('longitudeInput').value = e.latLng.lng().toFixed(6)
            updateMap()
        })

        initPlaces()
        if (hasCoords) {
            updateMap()
        }
    }

    function updateMap() {
        const lat = parseFloat(document.getElementById('latitudeInput').value)
        const lng = parseFloat(document.getElementById('longitudeInput').value)
        const radius = parseInt(document.getElementById('radiusSlider').value, 10)
        if (!map || isNaN(lat) || isNaN(lng)) return

        const position = { lat, lng }
        map.setCenter(position)

        if (!marker) {
            marker = new google.maps.Marker({ position, map, draggable: true })
            marker.addListener('dragend', function(e) {
                document.getElementById('latitudeInput').value = e.latLng.lat().toFixed(6)
                document.getElementById('longitudeInput').value = e.latLng.lng().toFixed(6)
                updateMap()
            })
        } else {
            marker.setPosition(position)
        }

        if (!circle) {
            circle = new google.maps.Circle({
                strokeColor: '#1a56db',
                strokeOpacity: 0.6,
                strokeWeight: 2,
                fillColor: '#bfdbfe',
                fillOpacity: 0.2,
                map,
                center: position,
                radius: radius,
            })
        } else {
            circle.setCenter(position)
            circle.setRadius(radius)
        }

        checkLocationChange()
    }

    function initPlaces() {
        const addressSearch = document.getElementById('addressSearch')
        if (!addressSearch || !window.google || !google.maps.places) return

        autocomplete = new google.maps.places.Autocomplete(addressSearch, {
            fields: ['formatted_address', 'geometry'],
            types: ['geocode'],
        })

        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace()
            if (!place.geometry || !place.geometry.location) {
                return
            }
            const lat = place.geometry.location.lat()
            const lng = place.geometry.location.lng()
            document.getElementById('latitudeInput').value = lat.toFixed(6)
            document.getElementById('longitudeInput').value = lng.toFixed(6)
            updateMap()
        })
    }
</script>
@endpush

@push('styles')
<style>
    .form-range::-webkit-slider-thumb {
        background: var(--primary);
    }

    .form-range::-moz-range-thumb {
        background: var(--primary);
    }

    .card {
        box-shadow: 0 2px 8px rgba(0,0,0,.08);
        border: 1px solid #e2e8f0;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,.15);
    }

    @media (max-width: 576px) {
        .card-body {
            padding: 1rem !important;
        }

        .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .d-flex.gap-2 {
            gap: 0.5rem !important;
        }
    }
</style>
@endpush

