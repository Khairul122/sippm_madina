<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SIPAPA Madina' }} — SIPAPA Madina</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-sipapa.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        h1, h2, h3, h4, .navbar-brand { font-family: 'Poppins', sans-serif; }
        .sipapa-topbar-identity { background-color: #fafaf9; border-bottom: 1px solid var(--sipapa-border); font-size: 0.95rem; color: #64748b; }
        .sipapa-page-header { background-color: #ffffff; border-bottom: 1px solid rgba(22, 52, 92, 0.06); }
        .navbar-sipapa { background-color: var(--sipapa-navy); box-shadow: var(--sipapa-shadow-soft); padding: 0.75rem 0; }
        
        .navbar-sipapa .nav-link { 
            color: rgba(255,255,255,.85) !important; 
            font-weight: 500; 
            padding: 0.5rem 1rem !important;
            position: relative;
            transition: color 0.2s ease;
        }
        .navbar-sipapa .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: var(--sipapa-gold);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .navbar-sipapa .nav-link:hover::after, .navbar-sipapa .nav-link.active::after {
            width: 80%;
        }
        .navbar-sipapa .nav-link:hover, .navbar-sipapa .nav-link.active { color: var(--sipapa-gold) !important; }
        
        .btn-sipapa { 
            background-color: var(--sipapa-navy); 
            color: #fff; 
            border-radius: var(--sipapa-radius-sm); 
            box-shadow: var(--sipapa-shadow-soft); 
            border: 2px solid transparent; 
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-sipapa:hover { 
            background-color: var(--sipapa-navy-light); 
            color: #fff; 
            transform: translateY(-1px);
            box-shadow: var(--sipapa-shadow-raised);
        }
        .btn-sipapa:active {
            transform: translateY(0);
        }
        
        footer.sipapa-footer { background-color: var(--sipapa-navy); color: #f0ede4; }
        footer.sipapa-footer a { color: rgba(255,255,255,0.7); text-decoration: none; transition: color 0.2s ease; }
        footer.sipapa-footer a:hover { color: var(--sipapa-gold); }
        /* Sizing/weight/color/margin for .footer-col-title live in
           resources/css/app.css (.sipapa-footer .footer-col-title), which
           wins on specificity — only the decorative underline stays here. */
        .footer-col-title::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            width: 30px;
            height: 2px;
            background-color: var(--sipapa-gold);
        }
        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.1);
            color: #fff !important;
            transition: all 0.2s ease;
        }
        .social-icon:hover {
            background-color: var(--sipapa-gold);
            color: var(--sipapa-navy) !important;
            transform: scale(1.1);
        }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Tier 1: identity bar -->
    <div class="sipapa-topbar-identity py-2 d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center flex-wrap">
            <span class="fw-medium"><i class="bi bi-geo-alt-fill me-1 text-secondary"></i>Pemerintah Kabupaten Mandailing Natal</span>
        </div>
    </div>

    <!-- Tier 2: page header (logo + search) -->
    <div class="sipapa-page-header py-3 shadow-xs">
        <div class="container d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <a href="{{ url('/') }}" class="d-flex align-items-center text-decoration-none gap-3">
                <img src="{{ asset('images/logo-sipapa.png') }}" alt="Lambang Kabupaten Mandailing Natal" style="height:56px; width:auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
                <span>
                    <span class="d-block fw-bold fs-5 mb-0" style="color: var(--sipapa-navy); font-family: 'Poppins', sans-serif;">SIPAPA Madina</span>
                    <span class="d-block text-muted fw-medium">Sistem Informasi Pengaduan &amp; Pelaporan Kegiatan</span>
                </span>
            </a>
            <form method="get" action="{{ url('/lacak') }}" class="d-flex align-items-center" style="max-width: 320px; width: 100%;">
                <div class="input-group shadow-sm">
                    <input type="text" name="ticket_number" class="form-control bg-light border-end-0" placeholder="Cari nomor tiket...">
                    <button type="submit" class="btn btn-sipapa px-3"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tier 3: sticky nav -->
    <nav class="navbar navbar-expand-lg navbar-sipapa sticky-top" id="mainNav">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('lacak') ? 'active' : '' }}" href="{{ url('/lacak') }}">Lacak Pengaduan</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('kegiatan') ? 'active' : '' }}" href="{{ url('/kegiatan') }}">Kegiatan</a></li>
                    @auth
                        <li class="nav-item"><a class="nav-link" href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="nav-item">
                            <form method="post" action="{{ url('/logout') }}" class="d-inline" data-confirm="Apakah Anda yakin ingin keluar?">
                                @csrf
                                <button class="nav-link btn btn-link" type="submit">Keluar</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ url('/login') }}">Masuk</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('/register') }}">Daftar</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        @yield('content')
    </main>

    <footer class="sipapa-footer pt-5 pb-4 mt-5">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-5">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="{{ asset('images/logo-sipapa.png') }}" alt="Lambang Kabupaten Mandailing Natal" style="height:36px; width:auto;">
                        <span class="fw-bold fs-6">SIPAPA Madina</span>
                    </div>
                    <p class="mb-3">Sistem Informasi Pengaduan Masyarakat dan Pelaporan Kegiatan Kabupaten Mandailing Natal — layanan satu pintu melalui Dinas Komunikasi dan Informatika.</p>
                    <div class="d-flex gap-2">
                        <a href="https://www.facebook.com/PemkabMandailingNatal/" target="_blank" rel="noopener" class="social-icon" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="https://www.instagram.com/diskominfo.madina" target="_blank" rel="noopener" class="social-icon" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="https://www.tiktok.com/@diskominfomadina2" target="_blank" rel="noopener" class="social-icon" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                        <a href="https://www.youtube.com/@DISKOMINFOMADINA" target="_blank" rel="noopener" class="social-icon" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                        <a href="https://madina.go.id/" target="_blank" rel="noopener" class="social-icon" aria-label="Website Resmi"><i class="bi bi-globe2"></i></a>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="footer-col-title">Tautan Cepat</div>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ url('/') }}">Beranda</a></li>
                        <li class="mb-2"><a href="{{ url('/lacak') }}">Lacak Pengaduan</a></li>
                        <li class="mb-2"><a href="{{ url('/kegiatan') }}">Kegiatan</a></li>
                        <li class="mb-2"><a href="https://madina.go.id/" target="_blank" rel="noopener">Website Resmi Pemkab</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <div class="footer-col-title">Kontak</div>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>Komplek Perkantoran Payaloting Parbangunan Kec. Panyabungan Kode Pos 22978</li>
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i><a href="mailto:info@madina.go.id">info@madina.go.id</a></li>
                        <li class="mb-2"><i class="bi bi-envelope-at me-2"></i><a href="mailto:diskominfo@mail.madina.go.id">diskominfo@mail.madina.go.id</a></li>
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i>(0636) 326255, 326258</li>
                        <li class="mb-2"><i class="bi bi-printer me-2"></i>Fax: (0636) 326254</li>
                    </ul>
                </div>
            </div>
            <hr class="border-light opacity-25">
            <div class="text-center">
                &copy; {{ date('Y') }} Pemerintah Kabupaten Mandailing Natal · Dinas Komunikasi dan Informatika
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.addEventListener('scroll', () => {
            document.getElementById('mainNav')?.classList.toggle('shadow-sm', window.scrollY > 10);
        });

        @if(session('status'))
            Swal.fire({
                icon: 'success',
                title: @json(session('status')),
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
            });
        @endif
    </script>
    @stack('scripts')
</body>
</html>
