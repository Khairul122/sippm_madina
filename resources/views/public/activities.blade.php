@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="h4 mb-4">Kegiatan Pemerintah Kabupaten Mandailing Natal</h1>
    <p class="text-muted mb-4">Total {{ $total }} kegiatan telah dipublikasikan.</p>
    <div class="row g-4">
        @forelse($activities as $activity)
            <div class="col-md-4">
                <div class="sippm-card h-100 overflow-hidden">
                    @php($doc = $activity->documentations->first())
                    @if($doc)
                        <img src="{{ asset('storage/'.$doc->file_path) }}" alt="{{ $activity->title }}" class="w-100" style="height:180px; object-fit:cover;">
                    @else
                        <div class="d-flex align-items-center justify-content-center" style="height:180px; background-color: var(--sippm-cream);">
                            <i class="bi bi-image fs-1 text-muted"></i>
                        </div>
                    @endif
                    <div class="p-4">
                        <div class="small text-muted mb-1">{{ $activity->date->translatedFormat('d F Y') }}</div>
                        <h3 class="h6">{{ $activity->title }}</h3>
                        <p class="small text-muted mb-2">{{ \Illuminate\Support\Str::limit(strip_tags($activity->description), 120) }}</p>
                        @if($activity->actor)
                            <span class="sippm-badge sippm-badge-navy"><i class="bi bi-building me-1"></i>{{ $activity->actor->name }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">Belum ada kegiatan yang dipublikasikan.</p>
        @endforelse
    </div>
    <div class="mt-4">
        {{ $activities->links() }}
    </div>
</div>
@endsection
