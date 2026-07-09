@extends('layouts.app')

@push('styles')
<style>
    .timeline-track {
        position: relative;
        padding-left: 3rem;
        margin-bottom: 2rem;
    }
    .timeline-track::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 5px;
        bottom: 5px;
        width: 3px;
        background-color: var(--sippm-border);
    }
    .timeline-track-item {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .timeline-track-badge {
        position: absolute;
        left: -3rem;
        top: 0;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #fff;
        border: 3px solid var(--sippm-border);
        color: #64748b;
        z-index: 2;
        transition: all 0.3s ease;
    }
    .timeline-track-item.active .timeline-track-badge {
        border-color: var(--sippm-navy);
        background-color: var(--sippm-navy);
        color: #fff;
        box-shadow: 0 0 8px rgba(22, 52, 92, 0.2);
    }
    .timeline-track-item.completed .timeline-track-badge {
        border-color: var(--sippm-green);
        background-color: var(--sippm-green);
        color: #fff;
    }
    .timeline-track-content {
        padding: 1rem;
        background-color: #fff;
        border: 1px solid var(--sippm-border);
        border-radius: var(--sippm-radius-sm);
        box-shadow: var(--sippm-shadow-soft);
        transition: all 0.2s ease;
    }
    .timeline-track-item.active .timeline-track-content {
        border-color: var(--sippm-navy-light);
        background-color: rgba(22, 52, 92, 0.02);
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="reveal sippm-card-raised p-4 p-md-5 bg-white">
                <div class="text-center mb-3">
                    <img src="{{ asset('images/logo-madina.png') }}" alt="Lambang Kabupaten Mandailing Natal" style="height:56px; width:auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
                </div>
                <h1 class="h3 mb-2 text-center fw-bold text-sippm">Lacak Status Pengaduan</h1>
                <p class="text-muted text-center mb-4">Masukkan nomor tiket pengaduan Anda untuk memantau proses tindak lanjut</p>

                <form method="get" action="{{ url('/lacak') }}" class="mb-4">
                    <div class="input-group shadow-sm">
                        <input type="text" name="ticket_number" class="form-control form-control-lg bg-light" placeholder="Contoh: PGD-2026-000001" value="{{ $ticketNumber }}">
                        <button type="submit" class="btn btn-sippm px-4"><i class="bi bi-search me-1"></i> Cari Tiket</button>
                    </div>
                </form>

                @if($ticketNumber)
                    @if($complaint)
                        <!-- Main Ticket Header -->
                        <div class="sippm-card p-4 mb-4 border-start border-4" style="border-color: var(--sippm-navy) !important; background-color: #fafaf9;">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <span class="font-monospace small text-muted d-block mb-1">{{ $complaint->ticket_number }}</span>
                                    <h2 class="h4 fw-bold mb-0 text-sippm">{{ $complaint->title }}</h2>
                                </div>
                                <span class="badge badge-status-{{ $complaint->status->value }} fs-6 py-2 px-3">{{ $complaint->status->label() }}</span>
                            </div>
                        </div>

                        <!-- Timeline Heading -->
                        <h3 class="h5 fw-bold mb-4 text-sippm border-bottom pb-2"><i class="bi bi-clock-history me-1"></i>Riwayat Alur Proses</h3>

                        <!-- Vertical Timeline -->
                        <div class="timeline-track">
                            @php
                                $histories = $complaint->statusHistories->sortBy('created_at');
                                $currentStatus = $complaint->status->value;
                            @endphp

                            @foreach($histories as $history)
                                <div class="timeline-track-item {{ $loop->last ? 'active' : 'completed' }}">
                                    <div class="timeline-track-badge">
                                        @if($loop->last && $currentStatus !== 'selesai')
                                            <i class="bi bi-arrow-right-circle-fill"></i>
                                        @else
                                            <i class="bi bi-check-lg"></i>
                                        @endif
                                    </div>
                                    <div class="timeline-track-content">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-1">
                                            <span class="badge badge-status-{{ $history->status->value }}">{{ $history->status->label() }}</span>
                                            <span class="small text-muted font-monospace">{{ $history->created_at->translatedFormat('d F Y, H:i') }}</span>
                                        </div>
                                        @if($history->note)
                                            <p class="small text-secondary mb-0 mt-2 bg-light p-2 rounded border border-light font-medium" style="line-height: 1.4;">
                                                <i class="bi bi-chat-left-text me-1 text-muted"></i>{{ $history->note }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning d-flex align-items-center gap-2 mb-0 shadow-sm">
                            <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
                            <div>Nomor tiket <strong>{{ $ticketNumber }}</strong> tidak ditemukan. Silakan periksa kembali penulisan nomor tiket Anda.</div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
