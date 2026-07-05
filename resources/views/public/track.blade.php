@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="sippm-card-raised p-4 p-md-5">
                <div class="text-center mb-3">
                    <img src="{{ asset('images/logo-madina.png') }}" alt="Lambang Kabupaten Mandailing Natal" style="height:48px; width:auto;">
                </div>
                <h1 class="h4 mb-4 text-center">Lacak Pengaduan</h1>
                <form method="get" action="{{ url('/lacak') }}" class="d-flex gap-2 mb-4">
                    <input type="text" name="ticket_number" class="form-control" placeholder="PGD-2026-000001" value="{{ $ticketNumber }}">
                    <button type="submit" class="btn btn-sippm"><i class="bi bi-search"></i> Cari</button>
                </form>

                @if($ticketNumber)
                    @if($complaint)
                        <div class="sippm-surface-inset p-3 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="font-monospace small text-muted">{{ $complaint->ticket_number }}</div>
                                    <div class="fw-semibold">{{ $complaint->title }}</div>
                                </div>
                                <span class="badge badge-status-{{ $complaint->status->value }}">{{ $complaint->status->label() }}</span>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">Nomor tiket tidak ditemukan. Periksa kembali penulisan nomor tiket Anda.</div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
