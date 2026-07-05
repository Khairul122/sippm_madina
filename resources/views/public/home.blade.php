@extends('layouts.app')

@section('content')
<section class="py-5" style="background: linear-gradient(180deg, var(--sippm-navy) 0%, var(--sippm-navy-light) 100%); color:#fff;">
    <div class="container py-4 text-center">
        <h1 class="display-6 fw-bold mb-3">Sistem Informasi Pengaduan Masyarakat<br>dan Pelaporan Kegiatan</h1>
        <p class="lead">Kabupaten Mandailing Natal &mdash; Layanan satu pintu melalui Dinas Komunikasi dan Informatika.</p>
        <div class="d-flex justify-content-center gap-3 mt-4">
            <a href="{{ url('/register') }}" class="btn btn-lg" style="background: var(--sippm-gold); color: var(--sippm-text);">Ajukan Pengaduan</a>
            <a href="{{ url('/lacak') }}" class="btn btn-lg btn-outline-light">Lacak Pengaduan</a>
        </div>
    </div>
</section>

<section class="container py-5">
    <div class="row g-4 text-center">
        <div class="col-md-4">
            <div class="sippm-card p-4">
                <div class="sippm-stat-number" data-count="{{ $totalComplaints }}">0</div>
                <div class="text-muted small mt-1">Total Pengaduan Masuk</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="sippm-card p-4">
                <div class="sippm-stat-number" data-count="{{ $resolvedComplaints }}">0</div>
                <div class="text-muted small mt-1">Pengaduan Selesai Ditangani</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="sippm-card p-4">
                <div class="sippm-stat-number" data-count="{{ $publishedActivities }}">0</div>
                <div class="text-muted small mt-1">Kegiatan Dipublikasikan</div>
            </div>
        </div>
    </div>
</section>

<section class="container pb-5">
    <div class="row g-4 text-center">
        <div class="col-md-4">
            <div class="sippm-card p-4 h-100">
                <i class="bi bi-megaphone fs-1" style="color: var(--sippm-navy);"></i>
                <h3 class="h5 mt-3">Ajukan Pengaduan</h3>
                <p class="text-muted small mb-0">Sampaikan aduan Anda ke Bupati, Wakil Bupati, Sekda, OPD, atau Camat secara online.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="sippm-card p-4 h-100">
                <i class="bi bi-search fs-1" style="color: var(--sippm-navy);"></i>
                <h3 class="h5 mt-3">Lacak Status</h3>
                <p class="text-muted small mb-0">Pantau status penanganan pengaduan Anda kapan saja menggunakan nomor tiket.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="sippm-card p-4 h-100">
                <i class="bi bi-calendar-check fs-1" style="color: var(--sippm-navy);"></i>
                <h3 class="h5 mt-3">Kegiatan Pemerintah</h3>
                <p class="text-muted small mb-0">Lihat dokumentasi kegiatan OPD dan Kecamatan yang telah dipublikasikan.</p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.sippm-stat-number').forEach((el) => {
        const target = parseInt(el.dataset.count, 10) || 0;
        const duration = 1200;
        const start = performance.now();

        function tick(now) {
            const progress = Math.min((now - start) / duration, 1);
            el.textContent = Math.floor(progress * target).toLocaleString('id-ID');
            if (progress < 1) requestAnimationFrame(tick);
        }

        requestAnimationFrame(tick);
    });
</script>
@endpush
