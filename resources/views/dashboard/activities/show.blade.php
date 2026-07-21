@extends('layouts.dashboard')

@section('content')
@php $user = auth()->user(); @endphp
<div class="row g-4">
    <div class="col-lg-8">
        <div class="sippm-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="badge bg-gold text-dark fs-6 py-2 px-3 fw-bold mb-2" style="background-color: var(--sippm-gold) !important;">
                        <i class="bi bi-activity me-1"></i>Kegiatan {{ $activity->actor ? $activity->actor->name : '-' }}
                    </span>
                    <h2 class="h4 mb-0 fw-bold text-sippm" style="font-family: 'Poppins', sans-serif;">{{ $activity->title }}</h2>
                </div>
                <span class="sippm-badge sippm-badge-{{ $activity->status->value === 'dipublikasikan' ? 'green' : ($activity->status->value === 'ditolak' ? 'red' : 'amber') }}">{{ $activity->status->label() }}</span>
            </div>
            
            <dl class="row small mb-0 mt-3">
                <dt class="col-sm-3 text-secondary">Tanggal Kegiatan</dt>
                <dd class="col-sm-9 fw-semibold text-dark">{{ $activity->date->translatedFormat('d F Y') }}</dd>
                
                <dt class="col-sm-3 text-secondary">Lokasi</dt>
                <dd class="col-sm-9 fw-semibold text-dark">{{ $activity->location ?? '-' }}</dd>
                
                <dt class="col-sm-3 text-secondary">Deskripsi</dt>
                <dd class="col-sm-9"><div class="rich-text-content">{!! $activity->description !!}</div></dd>
                
                @if($activity->rejection_reason)
                    <dt class="col-sm-3 text-danger">Alasan Ditolak</dt>
                    <dd class="col-sm-9 text-danger fw-semibold">{{ $activity->rejection_reason }}</dd>
                @endif
            </dl>
        </div>

        <!-- Documentation Photos Card -->
        <div class="sippm-card p-4 mb-4">
            <h3 class="h6 mb-3 border-bottom pb-2 text-sippm fw-bold"><i class="bi bi-images me-1"></i>Dokumentasi Foto (Maks. 5)</h3>
            @if($activity->documentations->isNotEmpty())
                <div class="row g-3">
                    @foreach($activity->documentations as $doc)
                        <div class="col-sm-6 col-md-4">
                            <div class="card border overflow-hidden shadow-sm h-100">
                                <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank">
                                    <img src="{{ asset('storage/'.$doc->file_path) }}" alt="Foto Kegiatan" class="w-100" style="height:150px; object-fit:cover;">
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-image fs-1 mb-2 d-block opacity-50"></i>
                    Belum ada foto dokumentasi diunggah.
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column Actions -->
    <div class="col-lg-4">
        <div class="sippm-card p-4">
            <h3 class="h6 mb-3 border-bottom pb-2 text-sippm fw-bold"><i class="bi bi-shield-lock me-1"></i>Aksi Kegiatan</h3>
            
            <div class="d-flex flex-column gap-2">
                @if($user->hasRole('kominfo'))
                    @if($activity->status->value === 'draft')
                        <!-- Verify Action -->
                        <div class="p-3 border rounded-3 bg-light mb-2">
                            <h4 class="small fw-bold text-sippm mb-2">Verifikasi Kegiatan</h4>
                            <form method="post" action="{{ url('/dashboard/activities/'.$activity->id.'/verify') }}" class="m-0" data-confirm="Simpan hasil verifikasi ini?">
                                @csrf
                                <div class="mb-2">
                                    <select name="is_valid" class="form-select form-select-sm" onchange="const r=this.form.querySelector('.rejection-group'); r.style.display = this.value === '0' ? 'block' : 'none';">
                                        <option value="1">Setujui (Valid)</option>
                                        <option value="0">Tolak (Tidak Valid)</option>
                                    </select>
                                </div>
                                <div class="mb-2 rejection-group" style="display:none;">
                                    <label class="small text-danger">Alasan Penolakan</label>
                                    <input type="text" name="rejection_reason" class="form-control form-control-sm" placeholder="Tulis alasan...">
                                </div>
                                <button class="btn btn-sm btn-success w-100" type="submit">Verifikasi</button>
                            </form>
                        </div>
                    @elseif($activity->status->value === 'diverifikasi')
                        <!-- Publish Action -->
                        <form method="post" action="{{ url('/dashboard/activities/'.$activity->id.'/publish') }}" class="m-0" data-confirm="Publikasikan kegiatan ini ke feed publik?">
                            @csrf
                            <button class="btn btn-sm btn-sippm w-100 py-2 fw-semibold" type="submit"><i class="bi bi-megaphone me-1"></i> Publikasikan Kegiatan</button>
                        </form>
                    @elseif($activity->status->value === 'dipublikasikan')
                        <!-- Unpublish Action -->
                        <form method="post" action="{{ url('/dashboard/activities/'.$activity->id.'/unpublish') }}" class="m-0" data-confirm="Tarik kembali kegiatan ini ke draft?">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary w-100 py-2 fw-semibold" type="submit"><i class="bi bi-arrow-counterclockwise me-1"></i> Tarik Kembali ke Draft</button>
                        </form>
                    @endif
                @endif

                <!-- Edit Button -->
                @can('update', $activity)
                    <a href="{{ url('/dashboard/activities/'.$activity->id.'/edit') }}" class="btn btn-sm btn-outline-secondary w-100 py-2 fw-semibold"><i class="bi bi-pencil-square me-1"></i> Ubah Kegiatan</a>
                @endcan

                <!-- Delete Button -->
                @can('delete', $activity)
                    <form method="post" action="{{ url('/dashboard/activities/'.$activity->id) }}" class="m-0 w-100" data-confirm="Hapus kegiatan secara permanen?">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger w-100 py-2 fw-semibold" type="submit"><i class="bi bi-trash me-1"></i> Hapus Kegiatan</button>
                    </form>
                @endcan

                <a href="{{ url('/dashboard/activities') }}" class="btn btn-sm btn-light w-100 py-2 border small"><i class="bi bi-arrow-left"></i> Kembali ke Daftar</a>
            </div>
        </div>
    </div>
</div>
@endsection
