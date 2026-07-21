@extends('layouts.dashboard')

@section('content')
<div class="sippm-card-raised p-4 mb-4" x-data="{ files: [] }">
    <div class="border-bottom pb-2 mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-pencil-square text-sippm fs-4"></i>
        <h2 class="h5 mb-0 font-weight-bold" style="font-family: 'Poppins', sans-serif;">Ubah Kegiatan</h2>
    </div>
    
    <form method="post" action="{{ url('/dashboard/activities/'.$activity->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label class="form-label fw-semibold">Judul Kegiatan</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $activity->title) }}" required placeholder="Tuliskan nama/judul kegiatan publikasi...">
        </div>
        
        <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <div id="description-editor" style="height:150px;"></div>
            <textarea name="description" class="form-control d-none" required placeholder="Deskripsikan detail kegiatan secara singkat dan padat...">{{ old('description', $activity->description) }}</textarea>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ old('date', $activity->date->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Lokasi</label>
                <input type="text" name="location" class="form-control" value="{{ old('location', $activity->location) }}" placeholder="Contoh: Kantor Bupati, Kec. Panyabungan">
            </div>
        </div>

        <!-- Existing Photos Management -->
        @if($activity->documentations->isNotEmpty())
            <div class="mb-4">
                <label class="form-label fw-semibold text-danger">Kelola Foto Dokumentasi Saat Ini (Centang untuk HAPUS)</label>
                <div class="row g-3">
                    @foreach($activity->documentations as $doc)
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card border rounded shadow-sm overflow-hidden position-relative h-100">
                                <img src="{{ asset('storage/'.$doc->file_path) }}" class="w-100" style="height:120px; object-fit:cover;">
                                <div class="card-body p-2 bg-light d-flex align-items-center gap-2">
                                    <input type="checkbox" name="delete_documentations[]" value="{{ $doc->id }}" id="delDoc{{ $doc->id }}" class="form-check-input">
                                    <label for="delDoc{{ $doc->id }}" class="form-check-label text-danger small fw-semibold cursor-pointer">Hapus Foto ini</label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- New Photos Upload -->
        <div class="mb-4">
            <label class="form-label fw-semibold">Tambah Foto Dokumentasi</label>
            <div class="file-upload-zone">
                <i class="bi bi-images file-upload-icon d-block text-secondary opacity-75"></i>
                <p class="mb-1 fw-semibold text-sippm">Seret foto dokumentasi baru ke sini atau klik untuk memilih</p>
                <p class="text-muted small mb-0">Format: JPG, JPEG, PNG (Maks. total 5 foto)</p>
                <input type="file" name="documentations[]" class="form-control" multiple accept=".jpg,.jpeg,.png" @change="files = Array.from($event.target.files).map(f => ({ name: f.name, size: (f.size / 1024 / 1024).toFixed(2) + ' MB' }))">
            </div>
            <div class="form-text mt-1 text-muted small"><i class="bi bi-info-circle-fill"></i> <strong>Catatan Video:</strong> Anda tidak dapat mengunggah file video secara langsung. Silakan sertakan link video (misal YouTube atau Google Drive) di dalam kolom deskripsi kegiatan di atas.</div>

            <!-- File Preview -->
            <div class="mt-3" x-show="files.length > 0">
                <p class="small fw-semibold text-secondary mb-2">Foto Baru Terpilih:</p>
                <div class="row g-2">
                    <template x-for="(file, index) in files" :key="index">
                        <div class="col-md-6">
                            <div class="file-preview-card">
                                <div class="d-flex align-items-center gap-2 min-w-0">
                                    <i class="bi bi-image text-primary fs-5"></i>
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

        <div class="border-top pt-3 d-flex justify-content-end gap-2">
            <a href="{{ url('/dashboard/activities/'.$activity->id) }}" class="btn btn-light btn-sm px-3 rounded-3" style="border: 1px solid rgba(0,0,0,0.1);">Batal</a>
            <button type="submit" class="btn btn-sippm btn-sm px-4 rounded-3 fw-semibold">Simpan Perubahan</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        sippmInitRichText('description-editor', 'textarea[name="description"]');
    });
</script>
@endpush
@endsection
