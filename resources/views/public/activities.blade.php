@extends('layouts.app')

@push('styles')
<style>
    .activity-card {
        transition: all 0.3s ease;
        border: 1px solid var(--sippm-border);
    }
    .activity-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--sippm-shadow-raised) !important;
        border-color: var(--sippm-gold) !important;
    }
    .activity-card-img-wrap {
        overflow: hidden;
        position: relative;
    }
    .activity-card-img {
        transition: transform 0.5s ease;
    }
    .activity-card:hover .activity-card-img {
        transform: scale(1.08);
    }
    /* Modal styles */
    .modal-content {
        background-color: var(--sippm-surface);
        border: 1px solid var(--sippm-border);
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <!-- Page Header Banner -->
    <div class="sippm-card-raised p-4 mb-5 bg-white text-center text-md-start d-md-flex align-items-center justify-content-between gap-4 border-start border-4" style="border-color: var(--sippm-gold) !important;">
        <div>
            <h1 class="h4 fw-bold text-sippm mb-1"><i class="bi bi-calendar3 me-2 text-secondary"></i>Kegiatan Pemerintah Kabupaten Mandailing Natal</h1>
            <p class="text-muted small mb-0">Publikasi dokumentasi dan laporan kegiatan pembangunan oleh OPD dan Kecamatan setempat.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <span class="badge bg-gold text-dark fs-6 py-2 px-3 fw-bold" style="background-color: var(--sippm-gold) !important;">
                <i class="bi bi-activity me-1"></i>Total: {{ $total }} Kegiatan
            </span>
        </div>
    </div>

    <div class="row g-4">
        @forelse($activities as $activity)
            <div class="col-md-6 col-lg-4">
                <div class="sippm-card h-100 overflow-hidden activity-card" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#activityModal{{ $activity->id }}">
                    <div class="activity-card-img-wrap">
                        @php($doc = $activity->documentations->first())
                        @if($doc)
                            <img src="{{ asset('storage/'.$doc->file_path) }}" alt="{{ $activity->title }}" class="w-100 activity-card-img" style="height:200px; object-fit:cover;">
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-light activity-card-img" style="height:200px;">
                                <i class="bi bi-image fs-1 text-muted opacity-50"></i>
                            </div>
                        @endif
                        <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge bg-dark bg-opacity-75 small font-monospace"><i class="bi bi-calendar-event me-1"></i>{{ $activity->date->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                    
                    <div class="p-4 d-flex flex-column justify-content-between" style="min-height: 180px;">
                        <div>
                            <h3 class="h6 fw-bold text-sippm mb-2" style="line-height: 1.4;">{{ $activity->title }}</h3>
                            <p class="small text-muted mb-3" style="line-height: 1.5;">{{ \Illuminate\Support\Str::limit(strip_tags($activity->description), 110) }}</p>
                        </div>
                        
                        @if($activity->actor)
                            <div>
                                <span class="sippm-badge sippm-badge-navy small fw-semibold"><i class="bi bi-building me-1"></i>{{ $activity->actor->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Activity Modal -->
            <div class="modal fade" id="activityModal{{ $activity->id }}" tabindex="-1" aria-labelledby="activityModalLabel{{ $activity->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-raised" style="border-radius: var(--sippm-radius-lg);">
                        <div class="modal-header border-0 pb-0">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 pt-0">
                            @if($doc)
                                <img src="{{ asset('storage/'.$doc->file_path) }}" alt="{{ $activity->title }}" class="w-100 rounded-3 mb-4 shadow-sm" style="max-height:380px; object-fit:cover;">
                            @endif
                            <div class="small text-muted mb-2 font-monospace"><i class="bi bi-calendar-event me-1"></i>{{ $activity->date->translatedFormat('d F Y') }}</div>
                            <h2 class="h4 fw-bold text-sippm mb-3">{{ $activity->title }}</h2>
                            <div class="rich-text-content mb-4" style="line-height:1.6; font-size:0.95rem;">
                                {!! $activity->description !!}
                            </div>
                            @if($activity->actor)
                                <div class="border-top pt-3 mt-3">
                                    <span class="sippm-badge sippm-badge-navy"><i class="bi bi-building me-1"></i>{{ $activity->actor->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x fs-1 mb-2 d-block"></i>
                    <p>Belum ada dokumentasi kegiatan pemerintah yang dipublikasikan.</p>
                </div>
            </div>
        @endforelse
    </div>
    
    <div class="mt-5">
        {{ $activities->links() }}
    </div>
</div>
@endsection
