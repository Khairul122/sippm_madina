@extends('layouts.dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="sippm-card p-4 text-center">
            <div class="display-6 fw-bold" style="color: var(--sippm-navy);">{{ $totalComplaints }}</div>
            <div class="text-muted small">Total Pengaduan</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sippm-card p-4 text-center">
            <div class="display-6 fw-bold" style="color: var(--sippm-green);">{{ $resolvedComplaints }}</div>
            <div class="text-muted small">Pengaduan Selesai</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sippm-card p-4 text-center">
            <div class="display-6 fw-bold" style="color: var(--sippm-gold);">{{ $resolutionRate }}%</div>
            <div class="text-muted small">Tingkat Penyelesaian</div>
        </div>
    </div>
</div>

<div class="sippm-card p-4">
    <h3 class="h6 mb-3">Pengaduan per Tujuan Awal</h3>
    <div class="sippm-chart-box">
        <canvas id="targetChart"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('targetChart'), {
        type: 'bar',
        data: {
            labels: @json($targetLabels),
            datasets: [{ label: 'Jumlah', data: @json($targetTotals), backgroundColor: '#16345c' }],
        },
        options: { maintainAspectRatio: false, responsive: true, plugins: { legend: { display: false } } },
    });
</script>
@endpush
