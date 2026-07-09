<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SIPPM Madina' }} — SIPPM Madina</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-madina.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        h1, h2, h3, h4, .navbar-brand { font-family: 'Poppins', sans-serif; }
        .sippm-topbar-identity { background-color: #fafaf9; border-bottom: 1px solid var(--sippm-border); font-size: 0.8rem; color: #64748b; }
        .sippm-page-header { background-color: #ffffff; border-bottom: 1px solid rgba(22, 52, 92, 0.06); }
        .navbar-sippm { background-color: var(--sippm-navy); box-shadow: var(--sippm-shadow-soft); padding: 0.75rem 0; }
        
        .navbar-sippm .nav-link { 
            color: rgba(255,255,255,.85) !important; 
            font-weight: 500; 
            padding: 0.5rem 1rem !important;
            position: relative;
            transition: color 0.2s ease;
        }
        .navbar-sippm .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: var(--sippm-gold);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .navbar-sippm .nav-link:hover::after, .navbar-sippm .nav-link.active::after {
            width: 80%;
        }
        .navbar-sippm .nav-link:hover, .navbar-sippm .nav-link.active { color: var(--sippm-gold) !important; }
        
        .btn-sippm { 
            background-color: var(--sippm-navy); 
            color: #fff; 
            border-radius: var(--sippm-radius-sm); 
            box-shadow: var(--sippm-shadow-soft); 
            border: 2px solid transparent; 
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-sippm:hover { 
            background-color: var(--sippm-navy-light); 
            color: #fff; 
            transform: translateY(-1px);
            box-shadow: var(--sippm-shadow-raised);
        }
        .btn-sippm:active {
            transform: translateY(0);
        }
        
        footer.sippm-footer { background-color: var(--sippm-navy); color: #f0ede4; }
        footer.sippm-footer a { color: rgba(255,255,255,0.7); text-decoration: none; transition: color 0.2s ease; }
        footer.sippm-footer a:hover { color: var(--sippm-gold); }
        .footer-col-title { font-weight: bold; font-family: 'Poppins', sans-serif; font-size: 1rem; margin-bottom: 1.25rem; color: var(--sippm-gold); position: relative; }
        .footer-col-title::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            width: 30px;
            height: 2px;
            background-color: var(--sippm-gold);
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
            background-color: var(--sippm-gold);
            color: var(--sippm-navy) !important;
            transform: scale(1.1);
        }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Tier 1: identity bar -->
    <div class="sippm-topbar-identity py-2 d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center flex-wrap">
            <span class="fw-medium"><i class="bi bi-geo-alt-fill me-1 text-secondary"></i>Pemerintah Kabupaten Mandailing Natal</span>
            <div class="d-flex gap-3">
                <span><i class="bi bi-envelope-fill me-1 text-secondary"></i>diskominfo@madina.go.id</span>
                <span><i class="bi bi-telephone-fill me-1 text-secondary"></i>(0636) 000000</span>
            </div>
        </div>
    </div>

    <!-- Tier 2: page header (logo + search) -->
    <div class="sippm-page-header py-3 shadow-xs">
        <div class="container d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <a href="{{ url('/') }}" class="d-flex align-items-center text-decoration-none gap-3">
                <img src="{{ asset('images/logo-madina.png') }}" alt="Lambang Kabupaten Mandailing Natal" style="height:56px; width:auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
                <span>
                    <span class="d-block fw-bold fs-5 mb-0" style="color: var(--sippm-navy); font-family: 'Poppins', sans-serif;">SIPPM Madina</span>
                    <span class="d-block small text-muted fw-medium">Sistem Informasi Pengaduan &amp; Pelaporan Kegiatan</span>
                </span>
            </a>
            <form method="get" action="{{ url('/lacak') }}" class="d-flex align-items-center" style="max-width: 320px; width: 100%;">
                <div class="input-group shadow-sm">
                    <input type="text" name="ticket_number" class="form-control bg-light border-end-0" placeholder="Cari nomor tiket..." style="font-size: 0.875rem;">
                    <button type="submit" class="btn btn-sippm px-3"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tier 3: sticky nav -->
    <nav class="navbar navbar-expand-lg navbar-sippm sticky-top" id="mainNav">
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

    <footer class="sippm-footer pt-5 pb-4 mt-5">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-5">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="{{ asset('images/logo-madina.png') }}" alt="Lambang Kabupaten Mandailing Natal" style="height:36px; width:auto;">
                        <span class="fw-bold fs-6">SIPPM Madina</span>
                    </div>
                    <p class="small mb-3">Sistem Informasi Pengaduan Masyarakat dan Pelaporan Kegiatan Kabupaten Mandailing Natal — layanan satu pintu melalui Dinas Komunikasi dan Informatika.</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="social-icon" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-icon" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-icon" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="footer-col-title">Tautan Cepat</div>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ url('/') }}">Beranda</a></li>
                        <li class="mb-2"><a href="{{ url('/lacak') }}">Lacak Pengaduan</a></li>
                        <li class="mb-2"><a href="{{ url('/kegiatan') }}">Kegiatan</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <div class="footer-col-title">Kontak</div>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>Pemerintah Kabupaten Mandailing Natal</li>
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i>diskominfo@madina.go.id</li>
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i>(0636) 000000</li>
                    </ul>
                </div>
            </div>
            <hr class="border-light opacity-25">
            <div class="text-center small">
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
