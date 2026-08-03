@extends('layouts.dashboard')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="sipapa-card p-4 mb-4">
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

        @if($complaint->handlings->isNotEmpty())
        <div class="sipapa-card p-4 mb-4">
            <h3 class="h6 mb-3 border-bottom pb-2 text-sipapa fw-bold" style="font-family: 'Poppins', sans-serif;"><i class="bi bi-wrench me-1"></i>Tindak Lanjut dari OPD / Kecamatan</h3>
            @foreach($complaint->handlings->sortBy('created_at') as $handling)
                <div class="mb-3 p-3 border rounded-3" style="background-color: #fcfbf9; border: 1px solid var(--sipapa-border) !important;">
                    <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                        <div>
                            <span class="fw-semibold text-sipapa small"><i class="bi bi-person-fill text-secondary"></i> {{ $handling->handledBy?->name ?? 'Petugas' }}</span>
                            @if($handling->handledBy?->opd)
                                <span class="badge bg-secondary ms-1 small">{{ $handling->handledBy->opd->name }}</span>
                            @elseif($handling->handledBy?->kecamatan)
                                <span class="badge bg-secondary ms-1 small">Kec. {{ $handling->handledBy->kecamatan->name }}</span>
                            @endif
                        </div>
                        <span class="text-muted small font-monospace"><i class="bi bi-calendar3"></i> {{ $handling->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                    </div>
                    <div class="rich-text-content small mb-2">{!! $handling->description !!}</div>
                    @if($handling->attachment_path)
                        <div class="border-top pt-2">
                            <a href="{{ asset('storage/'.$handling->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2 text-decoration-none small">
                                <i class="bi bi-paperclip"></i> Lihat Bukti Dukung (Lampiran)
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        @endif

        @if($complaint->response)
        <div class="sipapa-card p-4 mb-4 border-start border-4" style="border-color: var(--sipapa-green) !important;">
            <h3 class="h6 mb-2"><i class="bi bi-check-circle text-success me-1"></i>Jawaban Resmi</h3>
            <p class="mb-0">{{ $complaint->response->response_text }}</p>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="sipapa-card p-4">
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
