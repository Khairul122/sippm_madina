@extends('layouts.dashboard')

@section('content')
    <div class="sippm-card-raised p-4 mb-4">
        <div class="border-bottom pb-2 mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-journal-richtext text-sippm fs-4"></i>
            <h2 class="h5 mb-0 font-weight-bold" style="font-family: 'Poppins', sans-serif;">Manual Book</h2>
        </div>

        @if($manualBook)
            <div class="d-flex align-items-center gap-3 p-3 mb-4" style="background-color: rgba(22, 52, 92, 0.05); border-radius: var(--sippm-radius-sm);">
                <div class="d-flex align-items-center justify-content-center rounded-3 text-danger" style="width:56px; height:56px; background-color: rgba(178,58,58,0.1); flex-shrink:0;">
                    <i class="bi bi-file-earmark-pdf-fill fs-2"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold">{{ $manualBook->original_name }}</div>
                    <div class="small text-muted">
                        @if($manualBook->file_size)
                            {{ number_format($manualBook->file_size / 1048576, 2) }} MB ·
                        @endif
                        Diunggah {{ $manualBook->created_at->translatedFormat('d F Y, H:i') }} WIB
                        @if($manualBook->uploader)
                            oleh {{ $manualBook->uploader->name }}
                        @endif
                    </div>
                </div>
                <a href="{{ url('/manual-book/download') }}" class="btn btn-sippm btn-sm px-4 rounded-3 fw-semibold flex-shrink-0">
                    <i class="bi bi-download me-1"></i>Unduh
                </a>
            </div>

            <div class="mb-4" style="border-radius: var(--sippm-radius-sm); overflow:hidden; border:1px solid rgba(22, 52, 92, 0.12);">
                <iframe src="{{ url('/manual-book/preview') }}" title="Preview Manual Book" style="width:100%; height:70vh; border:none; display:block;"></iframe>
            </div>
        @else
            <div class="text-center py-5 mb-4">
                <i class="bi bi-journal-x text-muted" style="font-size:64px;"></i>
                <p class="text-muted mt-3 mb-0">Manual book belum diunggah.</p>
                @unless(auth()->user()->hasRole('kominfo'))
                    <p class="text-muted small">Hubungi Kominfo untuk mengunggah manual book.</p>
                @endunless
            </div>
        @endif

        @if(auth()->user()->hasRole('kominfo'))
            <div class="border-top pt-4">
                <h3 class="h6 fw-semibold mb-3">{{ $manualBook ? 'Ganti Manual Book' : 'Unggah Manual Book' }}</h3>
                <form method="post" action="{{ url('/manual-book') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <input type="file" name="file" accept="application/pdf" class="form-control" required>
                        <div class="form-text">File PDF, maks 20 MB.</div>
                    </div>
                    <button type="submit" class="btn btn-sippm btn-sm px-4 rounded-3 fw-semibold">
                        <i class="bi bi-upload me-1"></i>{{ $manualBook ? 'Ganti File' : 'Unggah' }}
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection
