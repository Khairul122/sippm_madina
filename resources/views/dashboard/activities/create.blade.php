@extends('layouts.dashboard')

@section('content')
<div class="sippm-card-raised p-4 mb-4" style="max-width:720px;" x-data="{ files: [] }">
    <div class="border-bottom pb-2 mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-calendar-plus text-sippm fs-4"></i>
        <h2 class="h5 mb-0 font-weight-bold" style="font-family: 'Poppins', sans-serif;">Input Kegiatan Baru</h2>
    </div>
    <form method="post" action="{{ url('/dashboard/activities') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Judul Kegiatan</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="Tuliskan nama/judul kegiatan publikasi...">
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <div id="description-editor" style="height:150px;"></div>
            <textarea name="description" class="form-control d-none" required placeholder="Deskripsikan detail kegiatan secara singkat dan padat...">{{ old('description') }}</textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ old('date') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Lokasi</label>
                <input type="text" name="location" class="form-control" value="{{ old('location') }}" placeholder="Contoh: Kantor Bupati, Kec. Panyabungan">
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label">Dokumentasi Foto</label>
            <div class="file-upload-zone">
                <i class="bi bi-images file-upload-icon d-block text-secondary opacity-75"></i>
                <p class="mb-1 fw-semibold text-sippm">Seret foto dokumentasi ke sini atau klik untuk memilih</p>
                <p class="text-muted small mb-0">Format: JPG, JPEG, PNG (bisa memilih lebih dari satu)</p>
                <input type="file" name="documentations[]" class="form-control" multiple accept=".jpg,.jpeg,.png" @change="files = Array.from($event.target.files).map(f => ({ name: f.name, size: (f.size / 1024 / 1024).toFixed(2) + ' MB' }))">
            </div>
            
            <!-- File Preview -->
            <div class="mt-3" x-show="files.length > 0">
                <p class="small fw-semibold text-secondary mb-2">Foto Terpilih:</p>
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
            <a href="{{ url('/dashboard/activities') }}" class="btn btn-light btn-sm px-3 rounded-3" style="border: 1px solid rgba(0,0,0,0.1);">Batal</a>
            <button type="submit" class="btn btn-sippm btn-sm px-4 rounded-3 fw-semibold">Simpan Kegiatan</button>
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
