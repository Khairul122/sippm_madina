{{--
    Role-gated dashboard navigation, shared by the desktop sidebar and the
    mobile offcanvas sidebar in layouts/dashboard.blade.php (@include'd
    twice so both stay in sync — same RBAC guards, same active-state logic).
--}}
@php $user = auth()->user(); $path = request()->path(); @endphp

<nav class="nav flex-column flex-grow-1">
    @if($user->hasRole('masyarakat'))
        <div class="nav-header">Pengaduan Saya</div>
        <a class="nav-link {{ $path === 'pengaduan' ? 'active' : '' }}" href="{{ url('/pengaduan') }}"><i class="bi bi-list-check me-2"></i>Riwayat Pengaduan</a>
        <a class="nav-link {{ $path === 'pengaduan/ajukan' ? 'active' : '' }}" href="{{ url('/pengaduan/ajukan') }}"><i class="bi bi-plus-circle me-2"></i>Ajukan Pengaduan</a>
    @endif

    @if($user->hasAnyRole(['kominfo','opd','camat','bupati','wakil_bupati','sekda']))
        <div class="nav-header">Monitoring</div>
        <a class="nav-link {{ $path === 'dashboard/statistik' ? 'active' : '' }}" href="{{ url('/dashboard/statistik') }}"><i class="bi bi-bar-chart-line me-2"></i>Statistik</a>
    @endif

    @if($user->hasAnyRole(['kominfo','opd','camat']))
        <div class="nav-header">Pengaduan</div>
        <a class="nav-link {{ str_starts_with($path,'dashboard/complaints') ? 'active' : '' }}" href="{{ url('/dashboard/complaints') }}"><i class="bi bi-chat-square-text me-2"></i>Pengaduan</a>
    @endif

    @if($user->hasAnyRole(['kominfo','opd','camat','bupati','wakil_bupati','sekda']))
        @unless($user->hasAnyRole(['kominfo','opd','camat']))
            <div class="nav-header">Kegiatan</div>
        @endunless
        <a class="nav-link {{ str_starts_with($path,'dashboard/activities') ? 'active' : '' }}" href="{{ url('/dashboard/activities') }}"><i class="bi bi-calendar-event me-2"></i>Kegiatan</a>
    @endif

    @if($user->hasRole('kominfo'))
        <div class="nav-header">Administrasi</div>
        <a class="nav-link {{ str_starts_with($path,'dashboard/users') ? 'active' : '' }}" href="{{ url('/dashboard/users') }}"><i class="bi bi-people me-2"></i>Kelola Pengguna</a>
        <a class="nav-link {{ str_starts_with($path,'dashboard/audit-log') ? 'active' : '' }}" href="{{ url('/dashboard/audit-log') }}"><i class="bi bi-journal-text me-2"></i>Audit Log</a>

        <div class="nav-header">Data Wilayah</div>
        <a class="nav-link {{ str_starts_with($path,'dashboard/opd') ? 'active' : '' }}" href="{{ url('/dashboard/opd') }}"><i class="bi bi-building me-2"></i>Data OPD</a>
        <a class="nav-link {{ str_starts_with($path,'dashboard/kecamatan') ? 'active' : '' }}" href="{{ url('/dashboard/kecamatan') }}"><i class="bi bi-signpost-2 me-2"></i>Data Kecamatan</a>
        <a class="nav-link {{ str_starts_with($path,'dashboard/desa') ? 'active' : '' }}" href="{{ url('/dashboard/desa') }}"><i class="bi bi-houses me-2"></i>Data Desa</a>
    @endif

    @if($user->hasAnyRole(['kominfo','bupati','wakil_bupati','sekda']))
        <div class="nav-header">Pimpinan</div>
        <a class="nav-link {{ $path === 'dashboard/kinerja' ? 'active' : '' }}" href="{{ url('/dashboard/kinerja') }}"><i class="bi bi-graph-up-arrow me-2"></i>Kinerja OPD/Kecamatan</a>
    @endif
</nav>
