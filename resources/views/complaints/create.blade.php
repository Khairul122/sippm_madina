@extends('layouts.dashboard')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #locationMap { height: 300px; border-radius: var(--sippm-radius-lg); box-shadow: var(--sippm-shadow-soft); border: 1px solid var(--sippm-border); }
    
    /* Stepper Styling */
    .stepper-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        padding: 16px 20px;
        border-radius: var(--sippm-radius-lg);
        border: 1px solid var(--sippm-border);
        box-shadow: var(--sippm-shadow-soft);
    }
    .stepper-step {
        flex: 1;
        text-align: center;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 2;
    }
    .stepper-step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 20px;
        left: 50%;
        width: 100%;
        height: 3px;
        background-color: #e2e8f0;
        z-index: -1;
        transition: background-color 0.3s ease;
    }
    .stepper-step.completed:not(:last-child)::after {
        background-color: var(--sippm-green);
    }
    .stepper-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #fff;
        border: 3px solid #e2e8f0;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        transition: all 0.3s ease;
        box-shadow: var(--sippm-shadow-soft);
    }
    .stepper-step.active .stepper-icon {
        border-color: var(--sippm-navy);
        color: var(--sippm-navy);
        background-color: #fff;
        transform: scale(1.1);
        box-shadow: 0 0 10px rgba(22, 52, 92, 0.2);
    }
    .stepper-step.completed .stepper-icon {
        border-color: var(--sippm-green);
        background-color: var(--sippm-green);
        color: #fff;
    }
    .stepper-label {
        font-size: 0.78rem;
        font-weight: 600;
        margin-top: 8px;
        color: #64748b;
        transition: color 0.3s ease;
    }
    .stepper-step.active .stepper-label {
        color: var(--sippm-navy);
    }
    .stepper-step.completed .stepper-label {
        color: var(--sippm-green);
    }
</style>
@endpush

@section('content')
<div class="sippm-card p-4 mb-4" x-data="complaintForm()">
    <!-- Step Progress Bar -->
    <div class="stepper-container mb-5">
        <div class="stepper-step" :class="{ active: step === 1, completed: step > 1 }">
            <div class="stepper-icon">
                <span x-show="step <= 1">1</span>
                <i x-show="step > 1" class="bi bi-check-lg"></i>
            </div>
            <div class="stepper-label">Tujuan</div>
        </div>
        <div class="stepper-step" :class="{ active: step === 2, completed: step > 2 }">
            <div class="stepper-icon">
                <span x-show="step <= 2">2</span>
                <i x-show="step > 2" class="bi bi-check-lg"></i>
            </div>
            <div class="stepper-label">Detail</div>
        </div>
        <div class="stepper-step" :class="{ active: step === 3, completed: step > 3 }">
            <div class="stepper-icon">
                <span x-show="step <= 3">3</span>
                <i x-show="step > 3" class="bi bi-check-lg"></i>
            </div>
            <div class="stepper-label">Lokasi</div>
        </div>
        <div class="stepper-step" :class="{ active: step === 4 }">
            <div class="stepper-icon">
                <span>4</span>
            </div>
            <div class="stepper-label">Lampiran</div>
        </div>
    </div>

    <form method="post" action="{{ url('/pengaduan') }}" enctype="multipart/form-data" @submit="submitting = true">
        @csrf

        <div x-show="step === 1">
            <h3 class="h6 mb-3 border-bottom pb-2 text-sippm fw-bold"><i class="bi bi-card-list me-1"></i>Langkah 1: Kategori &amp; Tujuan</h3>
            <div class="mb-3">
                <label class="form-label">Kategori Laporan</label>
                <input type="text" name="category" class="form-control" x-model="category" placeholder="Contoh: Infrastruktur, Jalan Raya, Kebersihan..." required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Tujuan Pengaduan</label>
                <input type="hidden" name="target_type" :value="targetType">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="p-3 border rounded-3 text-center cursor-pointer transition-all shadow-sm" :class="targetType === 'opd' ? 'border-primary bg-primary bg-opacity-10 text-primary fw-bold' : 'border-light bg-white text-muted'" @click="targetType = 'opd'" style="cursor: pointer; transition: all 0.2s ease;">
                            <i class="bi bi-building fs-3 mb-2 d-block"></i>
                            <span class="small fw-semibold">Dinas / Instansi (OPD)</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 border rounded-3 text-center cursor-pointer transition-all shadow-sm" :class="targetType === 'camat' ? 'border-primary bg-primary bg-opacity-10 text-primary fw-bold' : 'border-light bg-white text-muted'" @click="targetType = 'camat'" style="cursor: pointer; transition: all 0.2s ease;">
                            <i class="bi bi-flag fs-3 mb-2 d-block"></i>
                            <span class="small fw-semibold">Kecamatan</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3" x-show="targetType === 'opd'" x-cloak>
                <label class="form-label">Pilih Instansi OPD</label>
                <select name="target_id" class="form-select">
                    @foreach($opds as $opd)<option value="{{ $opd->id }}">{{ $opd->name }}</option>@endforeach
                </select>
            </div>
            <div class="mb-3" x-show="targetType === 'camat'" x-cloak>
                <label class="form-label">Pilih Kecamatan</label>
                <select name="target_id" class="form-select">
                    @foreach($kecamatans as $kecamatan)<option value="{{ $kecamatan->id }}">{{ $kecamatan->name }}</option>@endforeach
                </select>
            </div>
        </div>

        <div x-show="step === 2">
            <h3 class="h6 mb-3">Langkah 2: Detail Pengaduan</h3>
            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" name="title" class="form-control" x-model="title" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <div id="description-editor" style="height:150px;"></div>
                <textarea name="description" class="form-control d-none" required></textarea>
            </div>
        </div>

        <div x-show="step === 3" x-cloak>
            <h3 class="h6 mb-3 border-bottom pb-2 text-sippm fw-bold"><i class="bi bi-geo-alt-fill me-1"></i>Langkah 3: Lokasi Kejadian (opsional)</h3>

            <!-- Dynamic class binding fixes empty red alert box from Bootstrap's !important d-flex -->
            <div class="alert alert-danger small align-items-center gap-2" :class="locationError ? 'd-flex' : 'd-none'" x-cloak>
                <i class="bi bi-exclamation-triangle-fill fs-6 text-danger"></i>
                <div x-text="locationError"></div>
            </div>

            <div class="row g-3">
                <div class="col-lg-8">
                    <!-- Map Wrapper with Info Overlay -->
                    <div class="position-relative">
                        <div id="locationMap" class="shadow-sm border rounded-3" style="height: 350px; border: 1px solid var(--sippm-border) !important;"></div>
                        <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-50 text-white text-center py-1 small rounded-bottom" style="z-index: 1000; font-size: 0.72rem;">
                            <i class="bi bi-info-circle me-1"></i>Sentuh atau klik pada peta untuk menandai lokasi kejadian.
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <!-- Location Info Card -->
                    <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between" style="background-color: #fafaf9 !important; border: 1px solid var(--sippm-border) !important;">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-compass-fill text-sippm fs-5"></i>
                                <h4 class="fs-6 fw-bold mb-0">Informasi Koordinat</h4>
                            </div>
                            <p class="small text-muted mb-3" style="line-height: 1.4;">Tentukan lokasi dengan mengklik langsung pada peta, atau tekan tombol GPS di bawah ini.</p>
                            
                            <div class="mb-2">
                                <label class="small text-muted mb-1 d-block fw-semibold">Garis Lintang (Latitude)</label>
                                <div class="p-2 border rounded bg-white font-monospace text-secondary small shadow-sm d-flex align-items-center justify-content-between">
                                    <span x-text="latitude ? latitude.toFixed(6) : 'Belum ditentukan'"></span>
                                    <i class="bi bi-pin-angle text-muted"></i>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="small text-muted mb-1 d-block fw-semibold">Garis Bujur (Longitude)</label>
                                <div class="p-2 border rounded bg-white font-monospace text-secondary small shadow-sm d-flex align-items-center justify-content-between">
                                    <span x-text="longitude ? longitude.toFixed(6) : 'Belum ditentukan'"></span>
                                    <i class="bi bi-pin-angle-fill text-muted"></i>
                                </div>
                            </div>

                            <!-- Success indicator -->
                            <div class="mt-3 p-2 rounded bg-success bg-opacity-10 text-success small border border-success border-opacity-25 d-flex align-items-center gap-2 justify-content-center" x-show="latitude || longitude" x-cloak>
                                <i class="bi bi-check-circle-fill"></i>
                                <span class="fw-semibold">Lokasi berhasil ditandai!</span>
                            </div>
                        </div>
                        
                        <div class="d-flex flex-column gap-2 mt-2">
                            <button type="button" class="btn btn-sippm btn-sm w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2 shadow-sm" @click="useMyLocation()" :disabled="locating">
                                <template x-if="!locating">
                                    <span class="d-flex align-items-center gap-1"><i class="bi bi-crosshair"></i> Gunakan Lokasi Saya</span>
                                </template>
                                <template x-if="locating">
                                    <span class="d-flex align-items-center gap-1"><span class="spinner-border spinner-border-sm"></span> Mencari GPS...</span>
                                </template>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm w-100 py-2 d-flex align-items-center justify-content-center gap-1" x-show="latitude || longitude" @click="clearLocation()" x-cloak>
                                <i class="bi bi-trash"></i> Hapus Koordinat
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
        </div>

        <div x-show="step === 4" x-cloak>
            <h3 class="h6 mb-3">Langkah 4: Lampiran &amp; Kirim</h3>
            <div class="mb-3">
                <label class="form-label">Foto / Dokumen Pendukung</label>
                <div class="file-upload-zone">
                    <i class="bi bi-cloud-upload-fill file-upload-icon d-block text-secondary opacity-75"></i>
                    <p class="mb-1 fw-semibold text-sippm">Seret berkas ke sini atau klik untuk memilih</p>
                    <p class="text-muted small mb-0">Format: JPG, PNG, PDF (Maks. 5MB per berkas)</p>
                    <input type="file" name="attachments[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf" @change="handleFileChange($event)">
                </div>
                
                <!-- File Preview List -->
                <div class="mt-3" x-show="files.length > 0">
                    <p class="small fw-semibold text-secondary mb-2">Berkas Terpilih:</p>
                    <div class="row g-2">
                        <template x-for="(file, index) in files" :key="index">
                            <div class="col-md-6">
                                <div class="file-preview-card">
                                    <div class="d-flex align-items-center gap-2 min-w-0">
                                        <i class="bi fs-5" :class="file.name.endsWith('.pdf') ? 'bi-file-earmark-pdf-fill text-danger' : 'bi-file-earmark-image-fill text-primary'"></i>
                                        <div class="min-w-0">
                                            <div class="small fw-semibold text-truncate" style="max-width: 180px;" x-text="file.name"></div>
                                            <div class="text-muted" style="font-size: 0.7rem;" x-text="file.size"></div>
                                        </div>
                                    </div>
                                    <span class="badge bg-success rounded-pill px-2 py-1">Siap</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="alert alert-info small d-flex align-items-center gap-2 mt-4">
                <i class="bi bi-info-circle-fill fs-5 text-primary"></i>
                <div>Periksa kembali data Anda sebelum mengirim. Nomor tiket akan diberikan otomatis setelah pengaduan dikirim.</div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-outline-secondary" x-show="step > 1" @click="step--">Sebelumnya</button>
            <div class="ms-auto">
                <button type="button" class="btn btn-sippm" x-show="step < 4" @click="step++">Selanjutnya</button>
                <button type="submit" class="btn btn-sippm" x-show="step === 4" :disabled="submitting">
                    <span x-show="!submitting">Kirim Pengaduan</span>
                    <span x-show="submitting">Mengirim...</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    function complaintForm() {
        return {
            step: 1,
            category: '',
            targetType: 'opd',
            title: '',
            submitting: false,
            mapInitialized: false,
            descriptionEditorInitialized: false,
            map: null,
            marker: null,
            locating: false,
            locationLabel: '',
            locationError: '',
            latitude: '',
            longitude: '',
            files: [],
            init() {
                this.$watch('step', (value) => {
                    if (value === 2 && !this.descriptionEditorInitialized) {
                        this.descriptionEditorInitialized = true;
                        this.$nextTick(() => sippmInitRichText('description-editor', 'textarea[name="description"]'));
                    }
                    if (value === 3 && !this.mapInitialized) {
                        this.mapInitialized = true;
                        this.$nextTick(() => this.initMap());
                    }
                });
            },
            initMap() {
                this.map = L.map('locationMap').setView([0.5333, 99.4167], 10); // Panyabungan, Madina
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);
                this.map.on('click', (e) => this.setLocationPin(e.latlng.lat, e.latlng.lng, 14));
                setTimeout(() => this.map.invalidateSize(), 200);
            },
            setLocationPin(lat, lng, zoom = null) {
                this.latitude = lat;
                this.longitude = lng;
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;

                if (this.marker) this.map.removeLayer(this.marker);
                this.marker = L.marker([lat, lng]).addTo(this.map)
                    .bindPopup("<b>Lokasi Kejadian</b><br>Koordinat terpilih.")
                    .openPopup();

                if (zoom !== null) {
                    this.map.setView([lat, lng], zoom);
                } else {
                    this.map.panTo([lat, lng]);
                }

                this.locationLabel = `Lokasi ditandai: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            },
            clearLocation() {
                this.latitude = '';
                this.longitude = '';
                document.getElementById('latitude').value = '';
                document.getElementById('longitude').value = '';
                if (this.marker) {
                    this.map.removeLayer(this.marker);
                    this.marker = null;
                }
                this.locationLabel = '';
                this.map.setView([0.5333, 99.4167], 10);
            },
            useMyLocation() {
                this.locationError = '';

                if (!navigator.geolocation) {
                    this.locationError = 'Perangkat/browser Anda tidak mendukung fitur lokasi (GPS).';
                    return;
                }

                if (!this.mapInitialized) {
                    this.mapInitialized = true;
                    this.initMap();
                }

                this.locating = true;

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.locating = false;
                        this.setLocationPin(position.coords.latitude, position.coords.longitude, 16);
                    },
                    (error) => {
                        this.locating = false;
                        this.locationError = error.code === error.PERMISSION_DENIED
                            ? 'Izin akses lokasi ditolak. Aktifkan izin lokasi di browser, atau tandai lokasi secara manual pada peta.'
                            : 'Gagal mendapatkan lokasi Anda. Coba lagi atau tandai lokasi secara manual pada peta.';
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            },
            handleFileChange(e) {
                this.files = Array.from(e.target.files).map(file => ({
                    name: file.name,
                    size: (file.size / 1024 / 1024).toFixed(2) + ' MB'
                }));
            },
        };
    }
</script>
@endpush
