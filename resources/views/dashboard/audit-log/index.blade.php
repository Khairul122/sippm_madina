@extends('layouts.dashboard')

@section('content')
<div class="sippm-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Model</th><th>IP</th></tr></thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="small">{{ $log->created_at->translatedFormat('d M Y, H:i:s') }}</td>
                    <td>{{ $log->user?->name ?? '-' }}</td>
                    <td><span class="sippm-badge sippm-badge-navy">{{ $log->action }}</span></td>
                    <td class="small font-monospace">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</td>
                    <td class="small text-muted">{{ $log->ip_address }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada catatan audit log.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $logs->links() }}
</div>
@endsection
