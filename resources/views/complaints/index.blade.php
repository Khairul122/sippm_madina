@extends('layouts.dashboard')

@section('content')
<div class="sippm-card p-4">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ url('/pengaduan/ajukan') }}" class="btn btn-sippm"><i class="bi bi-plus-circle me-1"></i>Ajukan Pengaduan Baru</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Tiket</th><th>Judul</th><th>Kategori</th><th>Status</th><th>Diajukan</th><th></th></tr></thead>
            <tbody>
            @forelse($complaints as $complaint)
                <tr>
                    <td class="font-monospace small">{{ $complaint->ticket_number }}</td>
                    <td>{{ $complaint->title }}</td>
                    <td>{{ ucfirst($complaint->category) }}</td>
                    <td><span class="badge badge-status-{{ $complaint->status->value }}">{{ $complaint->status->label() }}</span></td>
                    <td class="small text-muted">{{ $complaint->created_at->translatedFormat('d M Y') }}</td>
                    <td><a href="{{ url('/pengaduan/'.$complaint->id) }}" class="btn btn-sm btn-sippm">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Anda belum pernah mengajukan pengaduan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $complaints->links() }}
</div>
@endsection
