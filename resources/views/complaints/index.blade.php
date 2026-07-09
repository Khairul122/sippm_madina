@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-2" style="max-width: 1000px;">
    <!-- Welcome Header & Action Button -->
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark" style="font-family: 'Poppins', sans-serif; color: var(--sippm-navy) !important;">Riwayat Pengaduan Saya</h1>
            <p class="text-muted small mb-0">Pantau status, penanganan, dan penyelesaian laporan pengaduan Anda.</p>
        </div>
        <div>
            <a href="{{ url('/pengaduan/ajukan') }}" class="btn btn-sippm px-4 py-2.5 rounded-pill fw-semibold shadow-sm d-flex align-items-center gap-1 text-white border-0" style="background-color: var(--sippm-navy);">
                <i class="bi bi-plus-circle-fill"></i> Ajukan Pengaduan Baru
            </a>
        </div>
    </div>

    @php
        $userComplaintsQuery = auth()->user()->complaints();
        $totalCount = (clone $userComplaintsQuery)->count();
        $resolvedCount = (clone $userComplaintsQuery)->where('status', 'selesai')->count();
        $inProgressCount = (clone $userComplaintsQuery)->whereIn('status', ['diverifikasi', 'diproses', 'ditindaklanjuti'])->count();
        $pendingCount = (clone $userComplaintsQuery)->where('status', 'diajukan')->count();
    @endphp

    <!-- Interactive Citizen Stats Overview Grid -->
    <div class="row g-3 mb-4">
        <!-- Total Pengaduan -->
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-radius: var(--sippm-radius-lg); border-left: 4px solid var(--sippm-navy) !important;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase tracking-wider" style="font-size: 0.72rem;">Total Laporan</span>
                        <h3 class="fw-bold mb-0 mt-0.5 text-dark" style="font-family: 'Poppins', sans-serif;">{{ number_format($totalCount) }}</h3>
                    </div>
                    <div class="stat-icon-box bg-navy-subtle text-navy rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background-color: rgba(22, 52, 92, 0.08); color: var(--sippm-navy);">
                        <i class="bi bi-folder-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Diajukan / Pending -->
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-radius: var(--sippm-radius-lg); border-left: 4px solid #2563eb !important;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase tracking-wider" style="font-size: 0.72rem;">Baru Diajukan</span>
                        <h3 class="fw-bold mb-0 mt-0.5 text-primary" style="font-family: 'Poppins', sans-serif;">{{ number_format($pendingCount) }}</h3>
                    </div>
                    <div class="stat-icon-box rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background-color: rgba(37, 99, 235, 0.08); color: #2563eb;">
                        <i class="bi bi-file-earmark-plus-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dalam Proses -->
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-radius: var(--sippm-radius-lg); border-left: 4px solid var(--sippm-amber) !important;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase tracking-wider" style="font-size: 0.72rem;">Sedang Diproses</span>
                        <h3 class="fw-bold mb-0 mt-0.5 text-warning" style="font-family: 'Poppins', sans-serif;">{{ number_format($inProgressCount) }}</h3>
                    </div>
                    <div class="stat-icon-box bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background-color: rgba(217, 142, 4, 0.08); color: var(--sippm-amber);">
                        <i class="bi bi-gear-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Laporan Selesai -->
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-radius: var(--sippm-radius-lg); border-left: 4px solid var(--sippm-green) !important;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase tracking-wider" style="font-size: 0.72rem;">Laporan Selesai</span>
                        <h3 class="fw-bold mb-0 mt-0.5 text-success" style="font-family: 'Poppins', sans-serif;">{{ number_format($resolvedCount) }}</h3>
                    </div>
                    <div class="stat-icon-box bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background-color: rgba(46, 125, 79, 0.08); color: var(--sippm-green);">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table complaints list -->
    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: var(--sippm-radius-lg); background: #ffffff;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary small uppercase tracking-wider" style="border-bottom: 2px solid rgba(22, 52, 92, 0.08);">
                    <tr>
                        <th class="py-3 px-4" style="width: 20%;">Nomor Tiket</th>
                        <th class="py-3" style="width: 40%;">Judul Laporan</th>
                        <th class="py-3" style="width: 15%;">Kategori</th>
                        <th class="py-3" style="width: 15%;">Status</th>
                        <th class="py-3" style="width: 15%;">Diajukan Pada</th>
                        <th class="py-3 text-end px-4" style="width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($complaints as $complaint)
                    <tr class="complaint-row" style="transition: all 0.2s ease;">
                        <!-- Ticket number with custom design -->
                        <td class="py-3 px-4">
                            <span class="font-monospace px-2.5 py-1 rounded bg-light border text-navy fw-semibold small" style="color: var(--sippm-navy) !important; font-size: 0.85rem;">
                                {{ $complaint->ticket_number }}
                            </span>
                        </td>
                        <!-- Title -->
                        <td class="py-3 fw-semibold text-dark" style="font-size: 0.95rem;">
                            {{ Str::limit($complaint->title, 60) }}
                        </td>
                        <!-- Category -->
                        <td class="py-3">
                            <span class="badge bg-light text-dark border px-2.5 py-1 rounded small">
                                {{ ucfirst($complaint->category) }}
                            </span>
                        </td>
                        <!-- Status Badge -->
                        <td class="py-3">
                            <span class="badge rounded-pill px-3 py-1.5 fw-semibold badge-status-{{ $complaint->status->value }}" style="font-size: 0.72rem; letter-spacing: 0.03em;">
                                {{ $complaint->status->label() }}
                            </span>
                        </td>
                        <!-- Submited Date -->
                        <td class="py-3 text-muted small">
                            {{ $complaint->created_at->translatedFormat('d M Y') }}
                        </td>
                        <!-- Action link -->
                        <td class="py-3 text-end px-4">
                            <a href="{{ url('/pengaduan/'.$complaint->id) }}" class="btn btn-sm px-3 rounded-pill fw-semibold text-white d-inline-flex align-items-center gap-1 shadow-sm transition-all" style="background-color: var(--sippm-navy); border: none; font-size: 0.8rem;">
                                Detail <i class="bi bi-chevron-right small"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <div class="d-flex flex-column align-items-center gap-3">
                                <i class="bi bi-chat-square-dots text-secondary opacity-50" style="font-size: 48px;"></i>
                                <div>
                                    <h5 class="fw-bold mb-1">Belum Ada Pengaduan</h5>
                                    <p class="text-muted small mb-0">Anda belum pernah mengajukan laporan pengaduan.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginator footer -->
        @if($complaints->hasPages())
            <div class="card-footer bg-transparent border-top py-3 px-4 d-flex justify-content-center">
                {{ $complaints->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    /* Premium Widgets styling */
    .stat-card {
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(22, 52, 92, 0.08) !important;
    }
    .tracking-wider {
        letter-spacing: 0.06em;
    }
    /* Hover highlight rows */
    .complaint-row:hover {
        background-color: rgba(22, 52, 92, 0.02) !important;
    }
    /* Custom button transition effects */
    .btn-sippm:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }
</style>
@endsection
