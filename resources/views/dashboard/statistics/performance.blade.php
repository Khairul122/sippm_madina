@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-2" style="max-width: 1100px;">
    <!-- Header Page Title -->
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark" style="font-family: 'Poppins', sans-serif; color: var(--sippm-navy) !important;">Kinerja Pelayanan</h1>
            <p class="text-muted small mb-0">Ikhtisar tingkat responsivitas dan rasio penyelesaian laporan oleh dinas instansi.</p>
        </div>
        <div>
            <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm small">
                <i class="bi bi-funnel me-1 text-primary"></i>
                Data Real-time
            </span>
        </div>
    </div>

    <!-- Premium Widgets Row -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Pengaduan -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-radius: var(--sippm-radius-lg); border-left: 4px solid var(--sippm-navy) !important;">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase tracking-wider">Total Masuk</span>
                        <h3 class="fw-bold mb-0 mt-1 text-dark" style="font-family: 'Poppins', sans-serif; font-size: 1.8rem;">{{ number_format($totalComplaints) }}</h3>
                        <p class="text-muted small mb-0 mt-1">Laporan dari masyarakat</p>
                    </div>
                    <div class="stat-icon-box bg-navy-subtle text-navy rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background-color: rgba(22, 52, 92, 0.08); color: var(--sippm-navy);">
                        <i class="bi bi-inboxes-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Pengaduan Selesai -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-radius: var(--sippm-radius-lg); border-left: 4px solid var(--sippm-green) !important;">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase tracking-wider">Selesai Ditangani</span>
                        <h3 class="fw-bold mb-0 mt-1 text-success" style="font-family: 'Poppins', sans-serif; font-size: 1.8rem;">{{ number_format($resolvedComplaints) }}</h3>
                        <p class="text-muted small mb-0 mt-1">Terselesaikan penuh</p>
                    </div>
                    <div class="stat-icon-box bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background-color: rgba(46, 125, 79, 0.08); color: var(--sippm-green);">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Rasio Kinerja / Tingkat Penyelesaian -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-radius: var(--sippm-radius-lg); border-left: 4px solid var(--sippm-gold) !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase tracking-wider">Rasio Penyelesaian</span>
                            <h3 class="fw-bold mb-0 text-dark" style="font-family: 'Poppins', sans-serif; font-size: 1.8rem; color: var(--sippm-gold) !important;">{{ $resolutionRate }}%</h3>
                        </div>
                        <div class="stat-icon-box bg-gold-subtle text-gold rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background-color: rgba(201, 162, 39, 0.08); color: var(--sippm-gold);">
                            <i class="bi bi-speedometer2 fs-4"></i>
                        </div>
                    </div>
                    <!-- Elegant Progress Bar -->
                    <div class="progress" style="height: 6px; background-color: rgba(201, 162, 39, 0.1); border-radius: 10px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $resolutionRate }}%; background-color: var(--sippm-gold); border-radius: 10px;" aria-valuenow="{{ $resolutionRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Card -->
    <div class="card border-0 shadow-sm" style="border-radius: var(--sippm-radius-lg); background: #ffffff;">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
            <h3 class="h6 mb-0 fw-bold text-dark" style="font-family: 'Poppins', sans-serif;"><i class="bi bi-building-fill-gear me-2 text-primary"></i>Pengaduan per Tujuan Instansi</h3>
            <p class="text-muted small mb-0">Sebaran total laporan berdasarkan dinas / wilayah kecamatan tujuan.</p>
        </div>
        <div class="card-body p-4" style="height: 380px;">
            <div class="w-100 h-100">
                <canvas id="targetChart"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hover scale animations */
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
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('targetChart'), {
        type: 'bar',
        data: {
            labels: @json($targetLabels),
            datasets: [{ 
                label: 'Jumlah Pengaduan', 
                data: @json($targetTotals), 
                backgroundColor: 'rgba(22, 52, 92, 0.85)',
                hoverBackgroundColor: 'rgba(22, 52, 92, 1)',
                borderRadius: 6,
                borderWidth: 0
            }],
        },
        options: { 
            maintainAspectRatio: false, 
            responsive: true, 
            plugins: { 
                legend: { display: false } 
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        },
    });
</script>
@endpush
