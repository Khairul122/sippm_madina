@extends('layouts.dashboard')

@section('content')
    <div class="sipapa-card-raised p-4 mb-4">
        <div class="border-bottom pb-2 mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-image text-sipapa fs-4"></i>
            <h2 class="h5 mb-0 font-weight-bold" style="font-family: 'Poppins', sans-serif;">Pengaturan Beranda</h2>
        </div>

        <p class="text-muted small mb-4">Foto ini tampil di sisi kanan halaman beranda publik (mis. foto Bupati/Wakil Bupati aktif). Ganti kapan saja saat periode jabatan berakhir, tanpa perlu bantuan developer.</p>

        <div class="row g-4">
            <div class="col-md-5">
                <div class="text-center p-3" style="background: linear-gradient(135deg, var(--sipapa-navy) 0%, #112746 100%); border-radius: var(--sipapa-radius-sm);">
                    @if($siteSetting?->hero_image_path)
                        <img src="{{ Storage::disk('public')->url($siteSetting->hero_image_path) }}" alt="Foto hero saat ini" class="img-fluid rounded-3" style="max-height: 260px;">
                    @else
                        <img src="{{ asset('images/hero-illustration.png') }}" alt="Ilustrasi bawaan" class="img-fluid rounded-3" style="max-height: 260px;">
                        <p class="text-white-50 small mt-2 mb-0">Belum ada foto kustom — memakai ilustrasi bawaan.</p>
                    @endif
                </div>

                @if($siteSetting?->hero_image_path)
                    <form method="post" action="{{ url('/dashboard/pengaturan/hero-image') }}" class="mt-3" data-confirm="Kembalikan ke ilustrasi bawaan?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Kembalikan ke Ilustrasi Bawaan
                        </button>
                    </form>
                @endif
            </div>

            <div class="col-md-7">
                <form method="post" action="{{ url('/dashboard/pengaturan') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ganti Foto</label>
                        <input type="file" name="hero_image" accept="image/png,image/jpeg,image/webp" class="form-control">
                        <div class="form-text">JPG/PNG/WEBP, maks 5 MB. Kosongkan jika hanya ingin mengubah keterangan.</div>
                        @error('hero_image')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan (opsional)</label>
                        <input type="text" name="hero_caption" value="{{ old('hero_caption', $siteSetting?->hero_caption) }}" class="form-control" maxlength="150" placeholder="Contoh: Bupati Mandailing Natal 2026-2031">
                        @error('hero_caption')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-sipapa px-4 rounded-3 fw-semibold">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
