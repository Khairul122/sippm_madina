@extends('layouts.app')

@push('styles')
<style>
    .hover-lift {
        transition: all 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: var(--sippm-shadow-raised) !important;
        border-color: var(--sippm-gold) !important;
    }
    
    /* Mobile Responsive Optimizations */
    @media (max-width: 575.98px) {
        .hero-title {
            font-size: 1.75rem !important;
            line-height: 1.3 !important;
        }
        .hero-lead {
            font-size: 0.9rem !important;
            line-height: 1.5 !important;
        }
        .sippm-card-raised {
            padding: 1.25rem !important;
        }
        .sippm-stat-number {
            font-size: 1.75rem !important;
        }
    }

    /* Vertical Flowchart Line for Mobile/Tablet Viewport */
    @media (max-width: 991.98px) {
        .flow-step-col:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 70px;
            left: 50%;
            width: 3px;
            height: 32px;
            background-color: var(--sippm-border);
            transform: translateX(-50%);
            z-index: 1;
        }
        .flow-step-col {
            margin-bottom: 2rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="py-5" style="background: linear-gradient(135deg, var(--sippm-navy) 0%, #112746 100%); color:#fff; position: relative; overflow: hidden; border-bottom: 4px solid var(--sippm-gold);">
    <!-- Subtle decorative pattern background -->
    <div class="position-absolute opacity-10" style="top:0; left:0; right:0; bottom:0; background-image: radial-gradient(var(--sippm-gold) 1px, transparent 0); background-size: 24px 24px;"></div>
    
    <div class="container py-4 position-relative" style="z-index: 2;">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 text-center text-lg-start">
                <span class="badge mb-3 px-3 py-2 fw-bold text-uppercase tracking-wider shadow-sm" style="background-color: var(--sippm-gold) !important; color: var(--sippm-text) !important; font-size: 0.72rem;">Portal Resmi Pemkab Mandailing Natal</span>
                <h1 class="display-5 hero-title fw-bold mb-3" style="line-height: 1.25; font-family: 'Poppins', sans-serif;">Sistem Informasi Pengaduan &amp; Pelaporan Kegiatan</h1>
                <p class="lead hero-lead opacity-90 mb-4" style="font-size: 1.05rem; line-height: 1.6;">Layanan satu pintu terintegrasi untuk menyampaikan aduan langsung ke OPD atau Kecamatan secara transparan dan memantau kegiatan pembangunan daerah secara terbuka.</p>
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                    <a href="{{ url('/register') }}" class="btn btn-lg px-4 py-3 shadow-lg btn-sippm" style="background-color: var(--sippm-gold) !important; color: var(--sippm-text) !important; font-size: 1rem;"><i class="bi bi-megaphone-fill me-2"></i>Ajukan Pengaduan</a>
                    <a href="{{ url('/lacak') }}" class="btn btn-outline-light btn-lg px-4 py-3" style="font-size: 1rem;"><i class="bi bi-search me-2"></i>Lacak Pengaduan</a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-center">
                <img src="{{ asset('images/hero-illustration.png') }}" alt="Ilustrasi Pelayanan Publik SIPPM Madina" class="img-fluid rounded-4 shadow-raised" style="max-height: 380px; width: auto; border: 3px solid rgba(255,255,255,0.15);">
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="container py-5" style="margin-top: 1rem; position: relative; z-index: 5;">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="sippm-card-raised p-4 d-flex align-items-center gap-4 bg-white" style="border-left: 5px solid var(--sippm-navy) !important;">
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center text-white" style="background-color: var(--sippm-navy); width: 60px; height: 60px; box-shadow: 0 4px 10px rgba(22, 52, 92, 0.2);">
                    <i class="bi bi-mailbox2 fs-3"></i>
                </div>
                <div>
                    <div class="sippm-stat-number fw-bold fs-2" data-count="{{ $totalComplaints }}" style="color: var(--sippm-navy); line-height: 1.2;">0</div>
                    <div class="text-muted small fw-semibold">Total Pengaduan Masuk</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="sippm-card-raised p-4 d-flex align-items-center gap-4 bg-white" style="border-left: 5px solid var(--sippm-green) !important;">
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center text-white" style="background-color: var(--sippm-green); width: 60px; height: 60px; box-shadow: 0 4px 10px rgba(46, 125, 79, 0.2);">
                    <i class="bi bi-shield-check fs-3"></i>
                </div>
                <div>
                    <div class="sippm-stat-number fw-bold fs-2" data-count="{{ $resolvedComplaints }}" style="color: var(--sippm-green); line-height: 1.2;">0</div>
                    <div class="text-muted small fw-semibold">Laporan Selesai Diproses</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="sippm-card-raised p-4 d-flex align-items-center gap-4 bg-white" style="border-left: 5px solid var(--sippm-gold) !important;">
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center text-white" style="background-color: var(--sippm-gold); width: 60px; height: 60px; box-shadow: 0 4px 10px rgba(201, 162, 39, 0.2);">
                    <i class="bi bi-journal-check fs-3" style="color: var(--sippm-text);"></i>
                </div>
                <div>
                    <div class="sippm-stat-number fw-bold fs-2" data-count="{{ $publishedActivities }}" style="color: var(--sippm-text); line-height: 1.2;">0</div>
                    <div class="text-muted small fw-semibold">Kegiatan Dipublikasikan</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Layanan Utama Section -->
<section class="container pb-5">
    <div class="text-center mb-5">
        <h2 class="h3 fw-bold text-sippm">Layanan Utama SIPPM</h2>
        <div class="bg-gold mx-auto mb-3" style="height: 3px; width: 60px; background-color: var(--sippm-gold);"></div>
        <p class="text-muted small">Layanan transparansi publik terintegrasi untuk seluruh masyarakat Mandailing Natal</p>
    </div>
    <div class="row g-4 justify-content-center">
        <div class="col-md-4">
            <div class="sippm-card p-4 text-center h-100 shadow-sm hover-lift">
                <div class="d-inline-flex p-3 rounded-4 mb-3" style="background-color: rgba(22, 52, 92, 0.06); color: var(--sippm-navy);">
                    <i class="bi bi-megaphone fs-1"></i>
                </div>
                <h3 class="h5 fw-bold text-sippm">Ajukan Pengaduan</h3>
                <p class="text-muted small mb-4" style="line-height: 1.5;">Sampaikan keluhan, aspirasi, atau aduan Anda secara langsung kepada instansi OPD atau Kecamatan yang bersangkutan secara online.</p>
                <a href="{{ url('/register') }}" class="btn btn-outline-primary btn-sm px-4 rounded-pill fw-semibold">Ajukan Sekarang <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="sippm-card p-4 text-center h-100 shadow-sm hover-lift">
                <div class="d-inline-flex p-3 rounded-4 mb-3" style="background-color: rgba(201, 162, 39, 0.08); color: var(--sippm-gold);">
                    <i class="bi bi-search fs-1"></i>
                </div>
                <h3 class="h5 fw-bold text-sippm">Lacak Status</h3>
                <p class="text-muted small mb-4" style="line-height: 1.5;">Pantau perkembangan penanganan laporan aduan Anda secara real-time kapan saja menggunakan nomor tiket pengaduan.</p>
                <a href="{{ url('/lacak') }}" class="btn btn-outline-primary btn-sm px-4 rounded-pill fw-semibold">Lacak Laporan <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="sippm-card p-4 text-center h-100 shadow-sm hover-lift">
                <div class="d-inline-flex p-3 rounded-4 mb-3" style="background-color: rgba(46, 125, 79, 0.08); color: var(--sippm-green);">
                    <i class="bi bi-journal-text fs-1"></i>
                </div>
                <h3 class="h5 fw-bold text-sippm">Kegiatan Pemerintah</h3>
                <p class="text-muted small mb-4" style="line-height: 1.5;">Lihat secara terbuka transparansi publik dan dokumentasi kegiatan pembangunan yang dilaksanakan oleh OPD dan Kecamatan.</p>
                <a href="{{ url('/kegiatan') }}" class="btn btn-outline-primary btn-sm px-4 rounded-pill fw-semibold">Lihat Kegiatan <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</section>

<!-- Flowchart Alur Aduan Section -->
<section class="container pb-5">
    <div class="sippm-card-raised p-4 p-md-5 bg-white text-center">
        <h2 class="h4 fw-bold text-sippm mb-2">Alur Penanganan Pengaduan</h2>
        <p class="text-muted small mb-5">Proses penanganan laporan aduan Anda mulai dari pengiriman hingga penyelesaian secara sistematis</p>
        
        <!-- Wrap in a relative container to bound the connecting line inside card limits -->
        <div class="position-relative">
            <!-- Connecting Line for desktop (aligned vertically at 59px to center within circle including 1.5rem grid column offset) -->
            <div class="position-absolute d-none d-lg-block" style="top: 59px; left: 12.5%; right: 12.5%; height: 2px; background-color: var(--sippm-border); z-index: 1;"></div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3 position-relative flow-step-col" style="z-index: 2;">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white border border-4 border-light shadow-sm mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-pencil-square fs-3 text-primary"></i>
                    </div>
                    <h4 class="h6 fw-bold mb-1">1. Tulis Laporan</h4>
                    <p class="text-muted small mb-0 px-3">Tulis dan laporkan aduan secara rinci melalui formulir di portal SIPPM.</p>
                </div>
                <div class="col-md-6 col-lg-3 position-relative flow-step-col" style="z-index: 2;">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white border border-4 border-light shadow-sm mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-shield-check fs-3 text-warning"></i>
                    </div>
                    <h4 class="h6 fw-bold mb-1">2. Verifikasi Kominfo</h4>
                    <p class="text-muted small mb-0 px-3">Dinas Kominfo memverifikasi kelayakan substansi berkas laporan aduan.</p>
                </div>
                <div class="col-md-6 col-lg-3 position-relative flow-step-col" style="z-index: 2;">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white border border-4 border-light shadow-sm mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-arrow-left-right fs-3 text-info"></i>
                    </div>
                    <h4 class="h6 fw-bold mb-1">3. Disposisi Instansi</h4>
                    <p class="text-muted small mb-0 px-3">Laporan diteruskan langsung ke OPD/Kecamatan yang berwenang menangani.</p>
                </div>
                <div class="col-md-6 col-lg-3 position-relative flow-step-col" style="z-index: 2;">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white border border-4 border-light shadow-sm mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-check-circle-fill fs-3 text-success"></i>
                    </div>
                    <h4 class="h6 fw-bold mb-1">4. Selesai Ditangani</h4>
                    <p class="text-muted small mb-0 px-3">Instansi memproses keluhan di lapangan dan memberikan jawaban resmi.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.sippm-stat-number').forEach((el) => {
            const target = parseInt(el.dataset.count, 10) || 0;
            const duration = 1500;
            const start = performance.now();

            function tick(now) {
                const progress = Math.min((now - start) / duration, 1);
                el.textContent = Math.floor(progress * target).toLocaleString('id-ID');
                if (progress < 1) requestAnimationFrame(tick);
            }

            requestAnimationFrame(tick);
        });
    });
</script>
@endpush
