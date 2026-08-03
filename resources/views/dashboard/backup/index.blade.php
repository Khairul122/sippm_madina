@extends('layouts.dashboard')

@section('content')
<div class="container-fluid p-0">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Top Header Card --}}
    <div class="sipapa-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--sipapa-navy);">
                    <i class="bi bi-database-fill-gear me-2 text-warning"></i>Backup Database Sistem
                </h4>
                <p class="text-muted small mb-2">
                    Kelola berkas cadangan database otomatis mingguan dan buat cadangan manual secara instan kapan saja.
                </p>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                        <i class="bi bi-clock-history me-1"></i> Jadwal Otomatis: <strong>Setiap Hari Minggu (01:00 WIB)</strong>
                    </span>
                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 rounded-pill">
                        <i class="bi bi-shield-check me-1"></i> Driver: <strong>Spatie Laravel Backup</strong>
                    </span>
                </div>
            </div>
            <div>
                <form action="{{ url('/dashboard/backup/run') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm d-flex align-items-center gap-2 fw-semibold" style="background-color: var(--sipapa-navy); border-color: var(--sipapa-navy);" data-confirm="Apakah Anda yakin ingin membuat backup database baru sekarang?">
                        <i class="bi bi-database-add fs-5"></i>
                        <span>Buat Backup Sekarang</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Summary Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="sipapa-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-archive fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Berkas Backup</div>
                    <div class="fs-4 fw-bold" style="color: var(--sipapa-navy);">{{ $totalFiles }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="sipapa-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-hdd-stack fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Ukuran Ruang</div>
                    <div class="fs-4 fw-bold" style="color: var(--sipapa-text);">{{ $totalSize }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="sipapa-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                    <i class="bi bi-calendar-check fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Backup Terakhir</div>
                    <div class="fw-bold text-dark">{{ $latestBackup ?? 'Belum ada' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Backup File List Table --}}
    <div class="sipapa-card p-4">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <h5 class="fw-bold mb-0" style="color: var(--sipapa-navy);">
                <i class="bi bi-list-nested me-2"></i>Daftar Berkas Backup Database
            </h5>
            <span class="text-muted small">Disimpan secara aman di <code>storage/app/private/SIPAPA Madina</code></span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Nama Berkas Backup</th>
                        <th>Ukuran Berkas</th>
                        <th>Tanggal & Waktu Dibuat</th>
                        <th style="width: 200px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $index => $backup)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-file-earmark-zip text-warning fs-4"></i>
                                    <span class="fw-mono small font-monospace fw-semibold">{{ $backup['name'] }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-dark border px-2 py-1">
                                    {{ $backup['size'] }}
                                </span>
                            </td>
                            <td class="small text-muted">
                                <i class="bi bi-clock me-1"></i>{{ $backup['date'] }}
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ url('/dashboard/backup/download/' . $backup['name']) }}" class="btn btn-sm btn-outline-primary" title="Unduh File Backup">
                                        <i class="bi bi-download me-1"></i> Unduh
                                    </a>
                                    <form action="{{ url('/dashboard/backup/' . $backup['name']) }}" method="POST" class="d-inline" data-confirm="Apakah Anda yakin ingin menghapus berkas backup {{ $backup['name'] }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus File Backup">
                                            <i class="bi bi-trash me-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-database-exclamation fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                <span>Belum ada berkas backup database. Klik tombol <strong>"Buat Backup Sekarang"</strong> di atas untuk membuat cadangan pertama.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
