@extends('layouts.dashboard')

@section('content')
<div class="container-fluid p-0">
    {{-- Top Header Card --}}
    <div class="sipapa-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--sipapa-navy);">
                    <i class="bi bi-shield-check me-2 text-primary"></i>Audit Log & Jejak Aktivitas Sistem
                </h4>
                <p class="text-muted small mb-0">
                    Mencatat secara real-time setiap aktivitas pengguna, waktu presisi, aksi, target data, dan alamat IP publik/klien secara aman & <em>immutable</em> (tidak dapat diubah/dihapus).
                </p>
            </div>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                <i class="bi bi-lock-fill me-1"></i> Log Permanen (Immutable)
            </span>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="sipapa-card p-3 mb-4">
        <form method="GET" action="{{ url('/dashboard/audit-log') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama pengguna, email, aksi, atau IP..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="model_type" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Kategori Target --</option>
                    <option value="user" {{ request('model_type') === 'user' ? 'selected' : '' }}>Pengguna / Akun (User)</option>
                    <option value="complaint" {{ request('model_type') === 'complaint' ? 'selected' : '' }}>Pengaduan (Complaint)</option>
                    <option value="activity" {{ request('model_type') === 'activity' ? 'selected' : '' }}>Laporan Kegiatan (Activity)</option>
                    <option value="backup" {{ request('model_type') === 'backup' ? 'selected' : '' }}>Backup Database</option>
                    <option value="backup_schedule" {{ request('model_type') === 'backup_schedule' ? 'selected' : '' }}>Jadwal Backup</option>
                    <option value="opd" {{ request('model_type') === 'opd' ? 'selected' : '' }}>Data OPD</option>
                    <option value="kecamatan" {{ request('model_type') === 'kecamatan' ? 'selected' : '' }}>Data Kecamatan</option>
                    <option value="desa" {{ request('model_type') === 'desa' ? 'selected' : '' }}>Data Desa</option>
                    <option value="category" {{ request('model_type') === 'category' ? 'selected' : '' }}>Kategori Pengaduan</option>
                    <option value="site_setting" {{ request('model_type') === 'site_setting' ? 'selected' : '' }}>Pengaturan Beranda</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sipapa w-100 fw-semibold">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>
                @if(request('search') || request('model_type'))
                    <a href="{{ url('/dashboard/audit-log') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Audit Log Table --}}
    <div class="sipapa-card p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 170px;">Waktu Kejadian</th>
                        <th>Pengguna</th>
                        <th>Aksi Aktivitas</th>
                        <th>Target Model</th>
                        <th>Alamat IP</th>
                        <th class="text-end" style="width: 100px;">Detail Data</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($logs as $log)
                    @php
                        $actionLower = strtolower($log->action ?? '');
                        $badgeClass = 'bg-secondary-subtle text-secondary border';
                        $iconClass = 'bi-info-circle';

                        if (str_contains($actionLower, 'login berhasil') || str_contains($actionLower, 'verifikasi') || str_contains($actionLower, 'selesai') || str_contains($actionLower, 'dibuat') || str_contains($actionLower, 'ditambahkan') || str_contains($actionLower, 'publikasi')) {
                            $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                            $iconClass = 'bi-check-circle-fill';
                        } elseif (str_contains($actionLower, 'disposisi') || str_contains($actionLower, 'diperbarui') || str_contains($actionLower, 'diubah') || str_contains($actionLower, 'tindak') || str_contains($actionLower, 'backup')) {
                            $badgeClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                            $iconClass = 'bi-pencil-square';
                        } elseif (str_contains($actionLower, 'ditolak') || str_contains($actionLower, 'dibatalkan') || str_contains($actionLower, 'gagal')) {
                            $badgeClass = 'bg-warning-subtle text-dark border border-warning-subtle';
                            $iconClass = 'bi-exclamation-triangle-fill';
                        } elseif (str_contains($actionLower, 'dihapus') || str_contains($actionLower, 'logout')) {
                            $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                            $iconClass = 'bi-trash-fill';
                        }
                    @endphp
                    <tr>
                        <td class="small text-muted">
                            <i class="bi bi-clock-history me-1"></i>
                            {{ $log->created_at ? $log->created_at->translatedFormat('d M Y, H:i:s') : '-' }}
                        </td>
                        <td>
                            @if($log->user)
                                <div class="fw-semibold text-dark">{{ $log->user->name }}</div>
                                <div class="small text-muted">{{ $log->user->email }}</div>
                            @else
                                <span class="text-muted fst-italic">Sistem / Tamu</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $badgeClass }} px-2 py-1 fs-6 fw-normal">
                                <i class="bi {{ $iconClass }} me-1"></i>{{ $log->action }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">
                                {{ ucfirst($log->model_type ?? 'Sistem') }}
                                @if($log->model_id) #{{ $log->model_id }} @endif
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-dark bg-opacity-10 text-dark border font-monospace">
                                <i class="bi bi-globe me-1"></i>{{ $log->ip_address ?? '127.0.0.1' }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($log->old_data || $log->new_data)
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $log->id }}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>

                                {{-- Modal Detail JSON Perubahan Data --}}
                                <div class="modal fade text-start" id="detailModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h6 class="modal-header-title fw-bold m-0">
                                                    <i class="bi bi-info-circle me-1 text-primary"></i>Detail Perubahan Data (Audit Log #{{ $log->id }})
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if($log->old_data)
                                                    <div class="mb-3">
                                                        <strong class="text-danger small d-block mb-1">Data Lama (Sebelumnya):</strong>
                                                        <pre class="bg-light p-2 rounded small text-wrap font-monospace border mb-0">{{ json_encode($log->old_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                                                    </div>
                                                @endif

                                                @if($log->new_data)
                                                    <div>
                                                        <strong class="text-success small d-block mb-1">Data Baru / Payload:</strong>
                                                        <pre class="bg-light p-2 rounded small text-wrap font-monospace border mb-0">{{ json_encode($log->new_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-shield-slash fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <span>Belum ada catatan audit log yang sesuai dengan kriteria pencarian.</span>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
