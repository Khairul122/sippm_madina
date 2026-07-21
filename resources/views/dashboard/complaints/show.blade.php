@extends('layouts.dashboard')

@section('content')
@php $user = auth()->user(); @endphp
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
            <dl class="row small mb-0">
                <dt class="col-sm-3">Pengadu</dt><dd class="col-sm-9">{{ $complaint->user->name }}</dd>
                <dt class="col-sm-3">Kategori</dt><dd class="col-sm-9">{{ ucfirst($complaint->category) }}</dd>
                <dt class="col-sm-3">Tujuan</dt><dd class="col-sm-9">{{ ucfirst(str_replace('_',' ', $complaint->target_type)) }}</dd>
                <dt class="col-sm-3">Deskripsi</dt><dd class="col-sm-9"><div class="rich-text-content">{!! $complaint->description !!}</div></dd>
                @if($complaint->rejection_reason)
                    <dt class="col-sm-3">Alasan Ditolak</dt><dd class="col-sm-9 text-danger">{{ $complaint->rejection_reason }}</dd>
                @endif
            </dl>
            @if($complaint->attachments->isNotEmpty())
                <hr>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($complaint->attachments as $attachment)
                        <a href="{{ asset('storage/'.$attachment->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-paperclip"></i> Lampiran {{ $loop->iteration }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        @if($complaint->handlings->isNotEmpty())
        <div class="sippm-card p-4 mb-4">
            <h3 class="h6 mb-3 border-bottom pb-2 text-sippm fw-bold" style="font-family: 'Poppins', sans-serif;"><i class="bi bi-wrench me-1"></i>Laporan Tindak Lanjut dari OPD / Kecamatan</h3>
            @foreach($complaint->handlings->sortBy('created_at') as $handling)
                <div class="mb-3 p-3 border rounded-3 bg-light" style="background-color: #fcfbf9 !important; border: 1px solid var(--sippm-border) !important;">
                    <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                        <div>
                            <span class="fw-semibold text-sippm small"><i class="bi bi-person-fill text-secondary"></i> {{ $handling->handledBy?->name ?? 'Petugas' }}</span>
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
                            <a href="{{ asset('storage/'.$handling->attachment_path) }}" target="_blank" class="btn btn-xs btn-outline-primary py-1 px-2 text-decoration-none small" style="font-size: 0.75rem;">
                                <i class="bi bi-paperclip"></i> Lihat Bukti Dukung (Lampiran)
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        @endif

        {{-- Kominfo: verifikasi (status diajukan) --}}
        @if($user->hasRole('kominfo') && $complaint->status->value === 'diajukan')
        <div class="sippm-card p-4 mb-4">
            <h3 class="h6 mb-3 border-bottom pb-2 text-sippm fw-bold" style="font-family: 'Poppins', sans-serif;"><i class="bi bi-shield-check me-1"></i>Verifikasi Pengaduan</h3>
            <form method="post" action="{{ url('/dashboard/complaints/'.$complaint->id.'/verify') }}" x-data="{ valid: '1' }" :data-confirm="valid === '1' ? 'Konfirmasi pengaduan ini VALID dan lanjut ke disposisi?' : 'Konfirmasi TOLAK pengaduan ini?'">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Catatan Verifikasi</label>
                    <textarea name="note" class="form-control" placeholder="Catatan atau review internal (opsional)" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label d-block">Hasil Verifikasi</label>
                    <div class="d-flex gap-3">
                        <label class="flex-fill p-3 border rounded-3 text-center transition" :class="valid === '1' ? 'border-success bg-success bg-opacity-10 text-success fw-semibold' : 'border-light bg-light'" style="cursor: pointer;">
                            <input type="radio" name="is_valid" value="1" x-model="valid" class="form-check-input me-1"> Valid (Setujui)
                        </label>
                        <label class="flex-fill p-3 border rounded-3 text-center transition" :class="valid === '0' ? 'border-danger bg-danger bg-opacity-10 text-danger fw-semibold' : 'border-light bg-light'" style="cursor: pointer;">
                            <input type="radio" name="is_valid" value="0" x-model="valid" class="form-check-input me-1"> Tidak Valid (Tolak)
                        </label>
                    </div>
                    <div class="mt-3" x-show="valid === '0'" x-cloak>
                        <label class="form-label text-danger">Alasan Penolakan <span class="text-danger">*</span></label>
                        <input type="text" name="rejection_reason" class="form-control" placeholder="Tulis alasan penolakan secara jelas...">
                    </div>
                </div>
                <button class="btn btn-sippm btn-sm px-4 py-2" type="submit">Simpan Hasil Verifikasi</button>
            </form>
        </div>
        @endif

        {{-- Kominfo: disposisi (status diverifikasi) --}}
        @if($user->hasRole('kominfo') && $complaint->status->value === 'diverifikasi')
        <div class="sippm-card p-4 mb-4">
            <h3 class="h6 mb-3 border-bottom pb-2 text-sippm fw-bold" style="font-family: 'Poppins', sans-serif;"><i class="bi bi-arrow-right-circle me-1"></i>Disposisi ke OPD / Camat</h3>
            <form method="post" action="{{ url('/dashboard/complaints/'.$complaint->id.'/dispose') }}" data-confirm="Kirim disposisi ke unit yang dipilih?">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label d-block mb-2 text-secondary small uppercase tracking-wider">PILIH OPD TARGET</label>
                        <div class="border rounded-3 p-3 bg-light" style="max-height: 200px; overflow-y: auto; background-color: #fafaf9 !important;">
                            @foreach($opds as $opd)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="targets[{{ $loop->index }}][id]" value="{{ $opd->id }}" onclick="const t=this.form.querySelector('input[name=\'targets[{{ $loop->index }}][type]\']'); t.disabled = !this.checked; t.value = this.checked ? 'opd' : '';">
                                    <input type="hidden" name="targets[{{ $loop->index }}][type]" value="" disabled>
                                    <label class="form-check-label small fw-medium">{{ $opd->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label d-block mb-2 text-secondary small uppercase tracking-wider">PILIH KECAMATAN TARGET</label>
                        <div class="border rounded-3 p-3 bg-light" style="max-height: 200px; overflow-y: auto; background-color: #fafaf9 !important;">
                            @foreach($kecamatans as $kecamatan)
                                @php $idx = $opds->count() + $loop->index; @endphp
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="targets[{{ $idx }}][id]" value="{{ $kecamatan->id }}" onclick="const t=this.form.querySelector('input[name=\'targets[{{ $idx }}][type]\']'); t.disabled = !this.checked; t.value = this.checked ? 'camat' : ''">
                                    <input type="hidden" name="targets[{{ $idx }}][type]" value="" disabled>
                                    <label class="form-check-label small fw-medium">{{ $kecamatan->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="mt-3 mb-3">
                    <label class="form-label">Catatan Disposisi</label>
                    <textarea name="note" class="form-control" placeholder="Instruksi penanganan khusus untuk instansi (opsional)" rows="2"></textarea>
                </div>
                <button class="btn btn-sippm btn-sm px-4 py-2" type="submit">Kirim Lembar Disposisi</button>
            </form>
        </div>
        @endif

        {{-- OPD/Camat: tangani (ada disposisi ke unit mereka & belum ditangani) --}}
        @if(($user->hasRole('opd') || $user->hasRole('camat')) && $myPendingDisposition && $complaint->status->value === 'diproses')
        <div class="sippm-card p-4 mb-4">
            <h3 class="h6 mb-3 border-bottom pb-2 text-sippm fw-bold" style="font-family: 'Poppins', sans-serif;"><i class="bi bi-wrench me-1"></i>Kirim Hasil Penanganan</h3>
            <form method="post" action="{{ url('/dashboard/complaints/'.$complaint->id.'/handle') }}" enctype="multipart/form-data" data-confirm="Kirim hasil penanganan ini? Pastikan data sudah benar." x-data="{ files: [] }">
                @csrf
                <input type="hidden" name="disposition_id" value="{{ $myPendingDisposition }}">
                <div class="mb-3">
                    <label class="form-label">Deskripsi Tindak Lanjut</label>
                    <div id="description-editor" style="height:130px;"></div>
                    <textarea name="description" class="form-control d-none" placeholder="Tuliskan secara detail langkah-langkah tindak lanjut yang telah diambil..." required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Lampiran Bukti Penanganan</label>
                    <div class="file-upload-zone" style="padding: 1.2rem;">
                        <i class="bi bi-cloud-upload file-upload-icon" style="font-size: 1.6rem; margin-bottom: 0.2rem;"></i>
                        <p class="mb-1 fw-semibold text-sippm small">Klik atau seret file bukti kegiatan</p>
                        <p class="text-muted small mb-0" style="font-size: 0.68rem;">Format: JPG, PNG, PDF (Maks. 5MB)</p>
                        <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf" @change="files = $event.target.files.length ? [{ name: $event.target.files[0].name, size: ($event.target.files[0].size / 1024 / 1024).toFixed(2) + ' MB' }] : []">
                    </div>
                    <div class="mt-2" x-show="files.length > 0">
                        <template x-for="(file, index) in files" :key="index">
                            <div class="file-preview-card mt-1 py-1 px-2">
                                <span class="small text-truncate" style="max-width: 250px;" x-text="file.name"></span>
                                <span class="badge bg-success rounded-pill" style="font-size: 0.65rem;" x-text="file.size"></span>
                            </div>
                        </template>
                    </div>
                </div>
                <button class="btn btn-sippm btn-sm px-4 py-2" type="submit">Kirim Laporan Penanganan</button>
            </form>
        </div>
        @endif

        {{-- Kominfo: jawaban resmi (status ditindaklanjuti) --}}
        @if($user->hasRole('kominfo') && $complaint->status->value === 'ditindaklanjuti')
        <div class="sippm-card p-4 mb-4">
            <h3 class="h6 mb-3 border-bottom pb-2 text-sippm fw-bold" style="font-family: 'Poppins', sans-serif;"><i class="bi bi-chat-left-text me-1"></i>Jawaban Resmi ke Masyarakat</h3>
            <form method="post" action="{{ url('/dashboard/complaints/'.$complaint->id.'/respond') }}" data-confirm="Kirim jawaban resmi ini ke masyarakat? Pengaduan akan ditandai selesai.">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Tanggapan/Jawaban Resmi</label>
                    <textarea name="response_text" class="form-control" rows="4" placeholder="Tuliskan draf tanggapan formal kepada pengadu..." required>{{ old('response_text', strip_tags($complaint->handlings->last()?->description ?? '')) }}</textarea>
                </div>
                <button class="btn btn-sippm btn-sm px-4 py-2" type="submit">Kirim Jawaban Resmi &amp; Selesaikan</button>
            </form>
        </div>
        @endif

        @if($complaint->response)
        <div class="sippm-card p-4 mb-4">
            <h3 class="h6 mb-2">Jawaban Resmi</h3>
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
                        <div class="text-muted mt-1">{{ $history->created_at->translatedFormat('d F Y, H:i') }} @if($history->changedBy) &middot; {{ $history->changedBy->name }} @endif</div>
                        @if($history->note)
                            <div class="mt-1 text-secondary small">{{ strip_tags($history->note) }}</div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        sippmInitRichText('description-editor', 'textarea[name="description"]');
    });
</script>
@endpush
