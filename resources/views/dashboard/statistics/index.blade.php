@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-2">
    <!-- Welcome Header -->
    <div class="mb-4">
        <h1 class="h3 mb-1 fw-bold text-dark" style="font-family: 'Poppins', sans-serif; color: var(--sippm-navy) !important;">Statistik & Monitoring</h1>
        <p class="text-muted small mb-0">Selamat datang kembali, <strong>{{ auth()->user()->name }}</strong>. Berikut adalah ikhtisar laporan pengaduan masyarakat.</p>
    </div>

    @php
        $totalComplaints = array_sum($complaintsByStatus);
        $resolvedComplaints = $complaintsByStatus['selesai'] ?? 0;
        $pendingComplaints = $complaintsByStatus['diajukan'] ?? 0;
        $inProgressComplaints = ($complaintsByStatus['diverifikasi'] ?? 0) + ($complaintsByStatus['diproses'] ?? 0) + ($complaintsByStatus['ditindaklanjuti'] ?? 0);
        $totalActivities = array_sum($activitiesByStatus);
    @endphp

    <!-- Stats Overview Cards Grid -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Pengaduan -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-radius: var(--sippm-radius-lg); border-left: 4px solid var(--sippm-navy) !important;">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase tracking-wider">Total Pengaduan</span>
                        <h3 class="fw-bold mb-0 mt-1 text-dark" style="font-family: 'Poppins', sans-serif;">{{ number_format($totalComplaints) }}</h3>
                    </div>
                    <div class="stat-icon-box bg-navy-subtle text-navy rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(22, 52, 92, 0.08); color: var(--sippm-navy);">
                        <i class="bi bi-chat-square-text-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Pengaduan Selesai -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-radius: var(--sippm-radius-lg); border-left: 4px solid var(--sippm-green) !important;">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase tracking-wider">Laporan Selesai</span>
                        <h3 class="fw-bold mb-0 mt-1 text-success" style="font-family: 'Poppins', sans-serif;">{{ number_format($resolvedComplaints) }}</h3>
                    </div>
                    <div class="stat-icon-box bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(46, 125, 79, 0.08); color: var(--sippm-green);">
                        <i class="bi bi-patch-check-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Pengaduan Dalam Proses -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-radius: var(--sippm-radius-lg); border-left: 4px solid var(--sippm-amber) !important;">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase tracking-wider">Sedang Diproses</span>
                        <h3 class="fw-bold mb-0 mt-1 text-warning" style="font-family: 'Poppins', sans-serif;">{{ number_format($inProgressComplaints) }}</h3>
                    </div>
                    <div class="stat-icon-box bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(217, 142, 4, 0.08); color: var(--sippm-amber);">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Total Kegiatan -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-radius: var(--sippm-radius-lg); border-left: 4px solid var(--sippm-gold) !important;">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase tracking-wider">Total Kegiatan</span>
                        <h3 class="fw-bold mb-0 mt-1 text-secondary" style="font-family: 'Poppins', sans-serif;">{{ number_format($totalActivities) }}</h3>
                    </div>
                    <div class="stat-icon-box bg-gold-subtle text-gold rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(201, 162, 39, 0.08); color: var(--sippm-gold);">
                        <i class="bi bi-calendar-check-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Visualization section -->
    <div class="row g-4">
        <!-- Chart 1: Status Pengaduan -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: var(--sippm-radius-lg); background: #ffffff;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                    <h3 class="h6 mb-0 fw-bold text-dark" style="font-family: 'Poppins', sans-serif;"><i class="bi bi-pie-chart me-2 text-primary"></i>Status Pengaduan</h3>
                    <p class="text-muted small mb-0">Presentase pengaduan berdasarkan status penyelesaian.</p>
                </div>
                <div class="card-body p-4 d-flex align-items-center justify-content-center" style="height: 300px;">
                    <div class="w-100 h-100">
                        <canvas id="complaintsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Status Kegiatan -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: var(--sippm-radius-lg); background: #ffffff;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                    <h3 class="h6 mb-0 fw-bold text-dark" style="font-family: 'Poppins', sans-serif;"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Status Kegiatan</h3>
                    <p class="text-muted small mb-0">Grafik penyusunan laporan kegiatan pejabat wilayah.</p>
                </div>
                <div class="card-body p-4 d-flex align-items-center justify-content-center" style="height: 300px;">
                    <div class="w-100 h-100">
                        <canvas id="activitiesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 3: Distribusi Kategori -->
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: var(--sippm-radius-lg); background: #ffffff;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                    <h3 class="h6 mb-0 fw-bold text-dark" style="font-family: 'Poppins', sans-serif;"><i class="bi bi-bar-chart-steps me-2 text-warning"></i>Distribusi Pengaduan per Kategori</h3>
                    <p class="text-muted small mb-0">Jumlah laporan yang masuk dikelompokkan berdasarkan kategori bidang.</p>
                </div>
                <div class="card-body p-4" style="height: 350px;">
                    <div class="w-100 h-100">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
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
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    // Custom colors tailored for the Bright Skeuomorphism theme
    const palette = ['#16345c', '#2e7d4f', '#d98e04', '#c9a227', '#b23a3a', '#7c3aed'];
    const chartDefaults = { 
        maintainAspectRatio: false, 
        responsive: true,
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    boxWidth: 12,
                    font: {
                        family: 'Inter',
                        size: 11
                    },
                    padding: 12
                }
            }
        }
    };

    new Chart(document.getElementById('complaintsChart'), {
        type: 'doughnut',
        data: {
            labels: @json(array_map('ucfirst', array_keys($complaintsByStatus))),
            datasets: [{ 
                data: @json(array_values($complaintsByStatus)), 
                backgroundColor: palette,
                borderWidth: 2,
                borderColor: '#ffffff'
            }],
        },
        options: { ...chartDefaults },
    });

    new Chart(document.getElementById('activitiesChart'), {
        type: 'doughnut',
        data: {
            labels: @json(array_map('ucfirst', array_keys($activitiesByStatus))),
            datasets: [{ 
                data: @json(array_values($activitiesByStatus)), 
                backgroundColor: palette,
                borderWidth: 2,
                borderColor: '#ffffff'
            }],
        },
        options: { ...chartDefaults },
    });

    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: {
            labels: @json(array_map('ucfirst', array_keys($complaintsByCategory))),
            datasets: [{ 
                label: 'Jumlah Pengaduan', 
                data: @json(array_values($complaintsByCategory)), 
                backgroundColor: 'rgba(22, 52, 92, 0.85)',
                hoverBackgroundColor: 'rgba(22, 52, 92, 1)',
                borderRadius: 6,
                borderWidth: 0
            }],
        },
        options: { 
            ...chartDefaults, 
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
