@extends('layouts.dashboard')

@section('content')
<div class="sippm-card p-4">
    <ul class="nav nav-tabs mb-4" id="laporanTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="laporan-tab" data-bs-toggle="tab" data-bs-target="#laporan-tab-pane" type="button" role="tab" aria-controls="laporan-tab-pane" aria-selected="true">
                <i class="bi bi-file-earmark-text me-1"></i>Data Laporan
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="ttd-tab" data-bs-toggle="tab" data-bs-target="#ttd-tab-pane" type="button" role="tab" aria-controls="ttd-tab-pane" aria-selected="false">
                <i class="bi bi-pencil-square me-1"></i>Pengaturan TTD
            </button>
        </li>
    </ul>

    <div class="tab-content" id="laporanTabsContent">
        <div class="tab-pane fade show active" id="laporan-tab-pane" role="tabpanel" aria-labelledby="laporan-tab" tabindex="0">
            <form method="get" action="{{ url('/dashboard/laporan') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-3">
                    <label class="form-label">Cari Laporan</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Nama kecamatan, kegiatan..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tujuan</label>
                    <select name="target" class="form-select">
                        <option value="">Semua</option>
                        <optgroup label="OPD">
                            @foreach($opds as $opd)
                                <option value="opd:{{ $opd->id }}" @selected(request('target') === 'opd:'.$opd->id)>{{ $opd->name }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Kecamatan">
                            @foreach($kecamatans as $kecamatan)
                                <option value="camat:{{ $kecamatan->id }}" @selected(request('target') === 'camat:'.$kecamatan->id)>{{ $kecamatan->name }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Hari</label>
                    <select name="hari" class="form-select">
                        <option value="">Semua</option>
                        @foreach($hariOptions as $index => $hari)
                            <option value="{{ $index }}" @selected(request('hari') === (string) $index)>{{ $hari }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        <option value="">Semua</option>
                        @foreach($bulanOptions as $index => $bulan)
                            <option value="{{ $index }}" @selected(request('bulan') === (string) $index)>{{ $bulan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        <option value="">Semua</option>
                        @foreach($tahunOptions as $tahun)
                            <option value="{{ $tahun }}" @selected(request('tahun') === (string) $tahun)>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-sippm w-100"><i class="bi bi-search me-1"></i>Cari</button>
                </div>

                <div class="col-12 d-flex flex-wrap gap-2 mt-2">
                    @if(request()->anyFilled(['search','status','target','hari','bulan','tahun']))
                        <a href="{{ url('/dashboard/laporan') }}" class="btn btn-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</a>
                    @endif
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="tab" data-bs-target="#ttd-tab-pane">
                        <i class="bi bi-pencil-square me-1"></i>TTD
                    </button>
                    <a href="{{ url('/dashboard/laporan/export-pdf') }}?{{ http_build_query(request()->query()) }}" class="btn btn-outline-danger">
                        <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                    </a>
                    <a href="{{ url('/dashboard/laporan/export-excel') }}?{{ http_build_query(request()->query()) }}" class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-excel me-1"></i>Excel
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Tiket</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Tujuan</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($complaints as $complaint)
                        <tr>
                            <td class="font-monospace small">{{ $complaint->ticket_number }}</td>
                            <td>{{ $complaint->title }}</td>
                            <td>{{ ucfirst($complaint->category) }}</td>
                            <td><span class="badge badge-status-{{ $complaint->status->value }}">{{ $complaint->status->label() }}</span></td>
                            <td class="small text-muted">
                                @if($complaint->target_type === 'opd')
                                    {{ $opds->firstWhere('id', $complaint->target_id)?->name ?? '-' }}
                                @elseif($complaint->target_type === 'camat')
                                    {{ $kecamatans->firstWhere('id', $complaint->target_id)?->name ?? '-' }}
                                @else
                                    {{ ucfirst(str_replace('_', ' ', (string) $complaint->target_type)) }}
                                @endif
                            </td>
                            <td class="small text-muted">{{ $complaint->created_at->translatedFormat('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data laporan yang cocok dengan filter.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $complaints->links() }}
        </div>

        <div class="tab-pane fade" id="ttd-tab-pane" role="tabpanel" aria-labelledby="ttd-tab" tabindex="0">
            <div x-data="{
                    nama: @js(old('nama_penandatangan', $ttd?->nama_penandatangan ?? '')),
                    jabatan: @js(old('jabatan_penandatangan', $ttd?->jabatan_penandatangan ?? '')),
                    pangkat: @js(old('pangkat', $ttd?->pangkat ?? '')),
                    nip: @js(old('nip', $ttd?->nip ?? '')),
                    get formattedNip() {
                        const digits = (this.nip || '').replace(/\D/g, '');
                        if (digits.length !== 18) {
                            return this.nip || '';
                        }
                        return digits.slice(0, 8) + ' ' + digits.slice(8, 14) + ' ' + digits.slice(14, 15) + ' ' + digits.slice(15, 18);
                    },
                }">
                <div class="row justify-content-center">
                    <div class="col-md-7">
                        <p class="text-muted">Data ini dipakai sebagai blok tanda tangan pada laporan pengaduan yang dicetak (PDF). Cukup diisi sekali, otomatis tersimpan dan dipakai untuk cetakan berikutnya.</p>
                        <form method="post" action="{{ url('/dashboard/laporan/ttd') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Nama Penandatangan</label>
                                <input type="text" name="nama_penandatangan" class="form-control" x-model="nama" required>
                                @error('nama_penandatangan')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jabatan Penandatangan</label>
                                <input type="text" name="jabatan_penandatangan" class="form-control" x-model="jabatan" required>
                                @error('jabatan_penandatangan')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pangkat</label>
                                    <input type="text" name="pangkat" class="form-control" x-model="pangkat">
                                    @error('pangkat')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">NIP</label>
                                    <input type="text" name="nip" class="form-control" inputmode="numeric" x-model="nip" required>
                                    @error('nip')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sippm"><i class="bi bi-save me-1"></i>Simpan</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-4 border">
                    <div class="card-header bg-white d-flex align-items-center gap-2 py-3">
                        <i class="bi bi-file-earmark-text text-info"></i>
                        <span class="fw-semibold">Preview Tanda Tangan</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="border rounded p-4" style="min-height: 220px;">
                            <div class="ms-auto" style="max-width: 380px; text-align: right;">
                                <p class="fst-italic text-secondary mb-3">Panyabungan, {{ now()->translatedFormat('F Y') }}</p>
                                <p class="fw-bold text-uppercase mb-4" style="line-height: 1.4;" x-text="jabatan || 'JABATAN PENANDATANGAN'"></p>
                                <div style="height: 64px;"></div>
                                <p class="fw-bold text-uppercase mb-1" x-text="nama || 'NAMA PENANDATANGAN'"></p>
                                <p class="fst-italic mb-1" x-text="pangkat || '-'"></p>
                                <p class="mb-0" style="color: #2563a8;" x-text="'NIP. ' + (formattedNip || '-')"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
