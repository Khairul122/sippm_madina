@extends('layouts.dashboard')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="sippm-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="font-monospace text-muted small">{{ $complaint->ticket_number }}</span>
                    <h2 class="h4 mb-0">{{ $complaint->title }}</h2>
                </div>
                <span class="badge badge-status-{{ $complaint->status->value }} fs-6">{{ $complaint->status->label() }}</span>
            </div>
            <div class="mb-0 rich-text-content">{!! $complaint->description !!}</div>
            @if($complaint->rejection_reason)
                <div class="alert alert-danger mt-3 mb-0">Alasan ditolak: {{ $complaint->rejection_reason }}</div>
            @endif
        </div>

        @if($complaint->response)
        <div class="sippm-card p-4 mb-4 border-start border-4" style="border-color: var(--sippm-green) !important;">
            <h3 class="h6 mb-2"><i class="bi bi-check-circle text-success me-1"></i>Jawaban Resmi</h3>
            <p class="mb-0">{{ $complaint->response->response_text }}</p>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="sippm-card p-4">
            <h3 class="h6 mb-3">Riwayat Status</h3>
            <ul class="list-unstyled small">
                @foreach($complaint->statusHistories->sortBy('id') as $history)
                    <li class="mb-3 pb-2 border-bottom">
                        <span class="badge badge-status-{{ $history->status->value }}">{{ $history->status->label() }}</span>
                        <div class="text-muted mt-1">{{ $history->created_at->translatedFormat('d F Y, H:i') }}</div>
                        @if($history->note && in_array($history->status->value, ['selesai', 'ditolak']))
                            <div class="mt-1 text-secondary small">{{ strip_tags($history->note) }}</div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
