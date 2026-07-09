{{--
    Role-gated dashboard navigation, shared by the desktop sidebar and the
    mobile offcanvas sidebar in layouts/dashboard.blade.php (@include'd
    twice so both stay in sync — same RBAC guards, same active-state logic).
--}}
@php $user = auth()->user(); $path = request()->path(); @endphp

<style>
    /* Chevron rotation transition */
    .sippm-sidebar .nav-link[data-bs-toggle="collapse"] .bi-chevron-down {
        transition: transform 0.2s ease;
    }
    .sippm-sidebar .nav-link[data-bs-toggle="collapse"].collapsed .bi-chevron-down {
        transform: rotate(-90deg);
    }
    /* Indentation and style for submenu items */
    .sippm-sidebar .collapse .nav-link {
        font-size: 0.88rem;
        padding-left: 1.75rem;
        opacity: 0.85;
    }
    .sippm-sidebar .collapse .nav-link:hover,
    .sippm-sidebar .collapse .nav-link.active {
        opacity: 1;
    }
</style>

<nav class="nav flex-column flex-grow-1">
    @if($user->hasRole('masyarakat'))
        <a class="nav-link {{ $path === 'pengaduan' ? 'active' : '' }}" href="{{ url('/pengaduan') }}"><i class="bi bi-list-check me-2"></i>Riwayat Pengaduan</a>
        <a class="nav-link {{ $path === 'pengaduan/ajukan' ? 'active' : '' }}" href="{{ url('/pengaduan/ajukan') }}"><i class="bi bi-plus-circle me-2"></i>Ajukan Pengaduan</a>
    @endif

    @if($user->hasAnyRole(['kominfo','opd','camat','bupati','wakil_bupati','sekda']))
        <a class="nav-link {{ $path === 'dashboard/statistik' ? 'active' : '' }}" href="{{ url('/dashboard/statistik') }}"><i class="bi bi-bar-chart-line me-2"></i>Statistik</a>
    @endif

    @if($user->hasAnyRole(['kominfo','opd','camat']))
        <a class="nav-link {{ str_starts_with($path,'dashboard/complaints') ? 'active' : '' }}" href="{{ url('/dashboard/complaints') }}"><i class="bi bi-chat-square-text me-2"></i>Pengaduan</a>
    @endif

    @if($user->hasAnyRole(['kominfo','opd','camat','bupati','wakil_bupati','sekda']))
        <a class="nav-link {{ str_starts_with($path,'dashboard/activities') ? 'active' : '' }}" href="{{ url('/dashboard/activities') }}"><i class="bi bi-calendar-event me-2"></i>Kegiatan</a>
    @endif

    @if($user->hasRole('kominfo'))
        {{-- Administrasi Dropdown --}}
        @php
            $isAdministrasiActive = str_starts_with($path, 'dashboard/users') || str_starts_with($path, 'dashboard/audit-log') || str_starts_with($path, 'dashboard/laporan');
        @endphp
        <a class="nav-link d-flex align-items-center justify-content-between {{ $isAdministrasiActive ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#administrasiCollapse" role="button" aria-expanded="{{ $isAdministrasiActive ? 'true' : 'false' }}">
            <span><i class="bi bi-gear me-2"></i>Administrasi</span>
            <i class="bi bi-chevron-down ms-auto small"></i>
        </a>
        <div class="collapse {{ $isAdministrasiActive ? 'show' : '' }}" id="administrasiCollapse">
            <div class="d-flex flex-column">
                <a class="nav-link {{ str_starts_with($path,'dashboard/users') ? 'active' : '' }}" href="{{ url('/dashboard/users') }}"><i class="bi bi-people me-2"></i>Kelola Pengguna</a>
                <a class="nav-link {{ str_starts_with($path,'dashboard/audit-log') ? 'active' : '' }}" href="{{ url('/dashboard/audit-log') }}"><i class="bi bi-journal-text me-2"></i>Audit Log</a>
                <a class="nav-link {{ str_starts_with($path,'dashboard/laporan') ? 'active' : '' }}" href="{{ url('/dashboard/laporan') }}"><i class="bi bi-printer me-2"></i>Laporan</a>
            </div>
        </div>

        {{-- Data Wilayah Dropdown --}}
        @php
            $isWilayahActive = str_starts_with($path, 'dashboard/opd') || str_starts_with($path, 'dashboard/kecamatan') || str_starts_with($path, 'dashboard/desa');
        @endphp
        <a class="nav-link d-flex align-items-center justify-content-between {{ $isWilayahActive ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#wilayahCollapse" role="button" aria-expanded="{{ $isWilayahActive ? 'true' : 'false' }}">
            <span><i class="bi bi-geo-alt me-2"></i>Data Wilayah</span>
            <i class="bi bi-chevron-down ms-auto small"></i>
        </a>
        <div class="collapse {{ $isWilayahActive ? 'show' : '' }}" id="wilayahCollapse">
            <div class="d-flex flex-column">
                <a class="nav-link {{ str_starts_with($path,'dashboard/opd') ? 'active' : '' }}" href="{{ url('/dashboard/opd') }}"><i class="bi bi-building me-2"></i>Data OPD</a>
                <a class="nav-link {{ str_starts_with($path,'dashboard/kecamatan') ? 'active' : '' }}" href="{{ url('/dashboard/kecamatan') }}"><i class="bi bi-signpost-2 me-2"></i>Data Kecamatan</a>
                <a class="nav-link {{ str_starts_with($path,'dashboard/desa') ? 'active' : '' }}" href="{{ url('/dashboard/desa') }}"><i class="bi bi-houses me-2"></i>Data Desa</a>
            </div>
        </div>
    @endif

    @if($user->hasAnyRole(['kominfo','bupati','wakil_bupati','sekda']))
        <a class="nav-link {{ $path === 'dashboard/kinerja' ? 'active' : '' }}" href="{{ url('/dashboard/kinerja') }}"><i class="bi bi-graph-up-arrow me-2"></i>Kinerja OPD/Kecamatan</a>
    @endif

    {{-- Manual book: tersedia untuk SEMUA role, tanpa @if role-gate --}}
    <a class="nav-link {{ str_starts_with($path,'manual-book') ? 'active' : '' }}" href="{{ url('/manual-book') }}"><i class="bi bi-journal-richtext me-2"></i>Manual Book</a>
</nav>
