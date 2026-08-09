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
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--sipapa-navy);">
                    <i class="bi bi-database-fill-gear me-2 text-warning"></i>Backup Database Sistem
                </h4>
                <p class="text-muted small mb-2">
                    Kelola jadwal pencadangan otomatis, buat salinan database instan, dan unduh berkas cadangan kapan saja.
                </p>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                        <i class="bi bi-calendar-event me-1"></i> Jadwal Otomatis: <strong>{{ $nextBackup }}</strong>
                    </span>
                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 rounded-pill">
                        <i class="bi bi-shield-check me-1"></i> Driver: <strong>Spatie Laravel Backup</strong>
                    </span>
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <form action="{{ url('/dashboard/backup/run') }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="download" value="1">
                    <button type="submit" class="btn btn-outline-primary px-3 py-2 shadow-sm d-flex align-items-center gap-2 fw-semibold">
                        <i class="bi bi-cloud-download fs-5"></i>
                        <span>Buat & Unduh Langsung</span>
                    </button>
                </form>

                <form action="{{ url('/dashboard/backup/run') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm d-flex align-items-center gap-2 fw-semibold" style="background-color: var(--sipapa-navy); border-color: var(--sipapa-navy);" data-confirm="Apakah Anda yakin ingin membuat backup database baru sekarang di server?">
                        <i class="bi bi-database-add fs-5"></i>
                        <span>Buat Backup di Server</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Form Schedule Setting & Stats Cards --}}
    <div class="row g-4 mb-4">
        {{-- Form Pengaturan Jadwal --}}
        <div class="col-lg-6">
            <div class="sipapa-card p-4 h-100">
                <h5 class="fw-bold mb-3" style="color: var(--sipapa-navy);">
                    <i class="bi bi-sliders me-2"></i>Pengaturan Jadwal Backup Otomatis
                </h5>
                <form action="{{ url('/dashboard/backup/schedule') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small mb-1">Frekuensi Pencadangan</label>
                        <select name="backup_frequency" class="form-select @error('backup_frequency') is-invalid @enderror" required>
                            <option value="daily" {{ ($setting->backup_frequency ?? '') === 'daily' ? 'selected' : '' }}>Setiap Hari (Daily)</option>
                            <option value="every_12_hours" {{ ($setting->backup_frequency ?? '') === 'every_12_hours' ? 'selected' : '' }}>Setiap 12 Jam (Twice Daily)</option>
                            <option value="every_3_days" {{ ($setting->backup_frequency ?? '') === 'every_3_days' ? 'selected' : '' }}>Setiap 3 Hari</option>
                            <option value="weekly" {{ ($setting->backup_frequency ?? 'weekly') === 'weekly' ? 'selected' : '' }}>Setiap Minggu (Weekly)</option>
                            <option value="monthly" {{ ($setting->backup_frequency ?? '') === 'monthly' ? 'selected' : '' }}>Setiap Bulan (Monthly)</option>
                        </select>
                        @error('backup_frequency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small mb-1">Waktu / Jam Eksekusi (WIB)</label>
                        <input type="time" name="backup_time" class="form-control @error('backup_time') is-invalid @enderror" value="{{ $setting->backup_time ?? '01:00' }}" required>
                        @error('backup_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">
                            <i class="bi bi-info-circle me-1"></i>Jadwal ini dijalankan oleh scheduler server (`php artisan schedule:run`).
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-sipapa px-4 fw-semibold">
                            <i class="bi bi-check-lg me-1"></i> Simpan Pengaturan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="col-lg-6">
            <div class="row g-3 h-100">
                <div class="col-6">
                    <div class="sipapa-card p-3 h-100 d-flex align-items-center gap-3">
                        <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-archive fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Berkas Backup</div>
                            <div class="fs-4 fw-bold" style="color: var(--sipapa-navy);">{{ $totalFiles }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="sipapa-card p-3 h-100 d-flex align-items-center gap-3">
                        <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-hdd-stack fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Ukuran Total Ruang</div>
                            <div class="fs-4 fw-bold" style="color: var(--sipapa-text);">{{ $totalSize }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="sipapa-card p-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                                <i class="bi bi-calendar-check fs-3"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Backup Terakhir Dibuat</div>
                                <div class="fw-bold text-dark">{{ $latestBackup ?? 'Belum ada berkas backup' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Backup File List Table --}}
    <div class="sipapa-card p-4">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <h5 class="fw-bold mb-0" style="color: var(--sipapa-navy);">
                <i class="bi bi-list-nested me-2"></i>Daftar Berkas Backup Database Terseimpan
            </h5>
            <span class="text-muted small">Disimpan secara aman di <code>storage/app/private</code></span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Berkas Backup</th>
                        <th>Ukuran Berkas</th>
                        <th>Tanggal & Waktu Dibuat</th>
                        <th style="width: 220px;" class="text-end">Aksi</th>
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
                                <span>Belum ada berkas backup database. Klik tombol <strong>"Buat & Unduh Langsung"</strong> atau <strong>"Buat Backup di Server"</strong> di atas untuk membuat cadangan pertama.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
