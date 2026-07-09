@extends('layouts.dashboard')

@section('content')
<div class="d-flex justify-content-end gap-2 mb-3">
    <a href="{{ url('/dashboard/statistik/export/pdf') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark-pdf me-1"></i>Export PDF</a>
    <a href="{{ url('/dashboard/statistik/export/excel') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Export Excel</a>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="sippm-card p-4">
            <h3 class="h6 mb-3">Pengaduan per Status</h3>
            <div class="sippm-chart-box">
                <canvas id="complaintsChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="sippm-card p-4">
            <h3 class="h6 mb-3">Kegiatan per Status</h3>
            <div class="sippm-chart-box">
                <canvas id="activitiesChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="sippm-card p-4">
            <h3 class="h6 mb-3">Pengaduan per Kategori</h3>
            <div class="sippm-chart-box">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const palette = ['#16345c', '#0d9488', '#d98e04', '#7c3aed', '#2e7d4f', '#b23a3a'];
    const chartDefaults = { maintainAspectRatio: false, responsive: true };

    new Chart(document.getElementById('complaintsChart'), {
        type: 'doughnut',
        data: {
            labels: @json(array_map('ucfirst', array_keys($complaintsByStatus))),
            datasets: [{ data: @json(array_values($complaintsByStatus)), backgroundColor: palette }],
        },
        options: { ...chartDefaults },
    });

    new Chart(document.getElementById('activitiesChart'), {
        type: 'doughnut',
        data: {
            labels: @json(array_map('ucfirst', array_keys($activitiesByStatus))),
            datasets: [{ data: @json(array_values($activitiesByStatus)), backgroundColor: palette }],
        },
        options: { ...chartDefaults },
    });

    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: {
            labels: @json(array_map('ucfirst', array_keys($complaintsByCategory))),
            datasets: [{ label: 'Jumlah', data: @json(array_values($complaintsByCategory)), backgroundColor: '#16345c' }],
        },
        options: { ...chartDefaults, plugins: { legend: { display: false } } },
    });
</script>
@endpush
