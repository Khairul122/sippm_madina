@extends('layouts.dashboard')

@push('styles')
<style>
    #laporanTabs .nav-link {
        color: var(--sippm-navy-light);
    }
    #laporanTabs .nav-link.active {
        background-color: var(--sippm-navy) !important;
        color: #fff !important;
        box-shadow: var(--sippm-shadow-soft);
    }
    #laporanTabs .nav-link:hover:not(.active) {
        background-color: rgba(22, 52, 92, 0.05);
    }
</style>
@endpush

@section('content')
@php $user = auth()->user(); @endphp

<!-- Kartu Ringkasan Metrik Terfilter -->
@if($activeTab === 'complaints')
<div class="row g-3 mb-4">
    <!-- Total Pengaduan -->
    <div class="col-sm-6 col-xl-3">
        <div class="sippm-card p-3 d-flex align-items-center justify-content-between bg-white border border-opacity-10 shadow-sm" style="transition: transform 0.2s ease; border-left: 4px solid var(--sippm-navy) !important;">
            <div>
                <span class="text-secondary small fw-medium d-block mb-1">Total Terfilter</span>
                <h3 class="fw-bold mb-0 text-sippm">{{ number_format($stats['total']) }}</h3>
            </div>
            <div class="rounded-circle p-2 bg-primary bg-opacity-10 text-primary" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-chat-left-text-fill fs-5"></i>
            </div>
        </div>
    </div>

    <!-- Status Selesai -->
    <div class="col-sm-6 col-xl-3">
        <div class="sippm-card p-3 d-flex align-items-center justify-content-between bg-white border border-opacity-10 shadow-sm" style="transition: transform 0.2s ease; border-left: 4px solid var(--sippm-green) !important;">
            <div>
                <span class="text-secondary small fw-medium d-block mb-1">Selesai</span>
                <h3 class="fw-bold mb-0 text-success">{{ number_format($stats['selesai']) }}</h3>
            </div>
            <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-check-circle-fill fs-5"></i>
            </div>
        </div>
    </div>

    <!-- Dalam Proses / Ditindaklanjuti -->
    <div class="col-sm-6 col-xl-3">
        <div class="sippm-card p-3 d-flex align-items-center justify-content-between bg-white border border-opacity-10 shadow-sm" style="transition: transform 0.2s ease; border-left: 4px solid var(--sippm-amber) !important;">
            <div>
                <span class="text-secondary small fw-medium d-block mb-1">Dalam Proses</span>
                <h3 class="fw-bold mb-0 text-warning" style="color: var(--sippm-amber) !important;">{{ number_format($stats['proses']) }}</h3>
            </div>
            <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-arrow-repeat fs-5"></i>
            </div>
        </div>
    </div>

    <!-- Menunggu / Verifikasi -->
    <div class="col-sm-6 col-xl-3">
        <div class="sippm-card p-3 d-flex align-items-center justify-content-between bg-white border border-opacity-10 shadow-sm" style="transition: transform 0.2s ease; border-left: 4px solid #2563eb !important;">
            <div>
                <span class="text-secondary small fw-medium d-block mb-1">Menunggu</span>
                <h3 class="fw-bold mb-0 text-primary">{{ number_format($stats['pending']) }}</h3>
            </div>
            <div class="rounded-circle p-2 bg-info bg-opacity-10 text-info" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-hourglass-split fs-5"></i>
            </div>
        </div>
    </div>
</div>
@else
<div class="row g-3 mb-4">
    <!-- Total Kegiatan -->
    <div class="col-sm-6 col-xl-3">
        <div class="sippm-card p-3 d-flex align-items-center justify-content-between bg-white border border-opacity-10 shadow-sm" style="transition: transform 0.2s ease; border-left: 4px solid var(--sippm-navy) !important;">
            <div>
                <span class="text-secondary small fw-medium d-block mb-1">Total Terfilter</span>
                <h3 class="fw-bold mb-0 text-sippm">{{ number_format($stats['total']) }}</h3>
            </div>
            <div class="rounded-circle p-2 bg-primary bg-opacity-10 text-primary" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-activity fs-5"></i>
            </div>
        </div>
    </div>

    <!-- Dipublikasikan -->
    <div class="col-sm-6 col-xl-3">
        <div class="sippm-card p-3 d-flex align-items-center justify-content-between bg-white border border-opacity-10 shadow-sm" style="transition: transform 0.2s ease; border-left: 4px solid var(--sippm-green) !important;">
            <div>
                <span class="text-secondary small fw-medium d-block mb-1">Dipublikasikan</span>
                <h3 class="fw-bold mb-0 text-success">{{ number_format($stats['dipublikasikan']) }}</h3>
            </div>
            <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-megaphone-fill fs-5"></i>
            </div>
        </div>
    </div>

    <!-- Diverifikasi -->
    <div class="col-sm-6 col-xl-3">
        <div class="sippm-card p-3 d-flex align-items-center justify-content-between bg-white border border-opacity-10 shadow-sm" style="transition: transform 0.2s ease; border-left: 4px solid var(--sippm-amber) !important;">
            <div>
                <span class="text-secondary small fw-medium d-block mb-1">Diverifikasi</span>
                <h3 class="fw-bold mb-0 text-warning" style="color: var(--sippm-amber) !important;">{{ number_format($stats['diverifikasi']) }}</h3>
            </div>
            <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-shield-check fs-5"></i>
            </div>
        </div>
    </div>

    <!-- Draft -->
    <div class="col-sm-6 col-xl-3">
        <div class="sippm-card p-3 d-flex align-items-center justify-content-between bg-white border border-opacity-10 shadow-sm" style="transition: transform 0.2s ease; border-left: 4px solid #2563eb !important;">
            <div>
                <span class="text-secondary small fw-medium d-block mb-1">Draft Usulan</span>
                <h3 class="fw-bold mb-0 text-primary">{{ number_format($stats['draft']) }}</h3>
            </div>
            <div class="rounded-circle p-2 bg-info bg-opacity-10 text-info" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-file-earmark-text-fill fs-5"></i>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Container Tab Utama -->
<div class="sippm-card p-4">
    <ul class="nav nav-pills p-1 bg-light mb-4 d-inline-flex border" id="laporanTabs" style="border-radius: var(--sippm-radius-sm);">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'complaints' ? 'active' : '' }} px-4 py-2 fw-semibold" href="{{ url('/dashboard/laporan') }}" style="border-radius: 10px; transition: all 0.2s;">
                <i class="bi bi-chat-left-text me-2"></i>Laporan Pengaduan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'activities' ? 'active' : '' }} px-4 py-2 fw-semibold" href="{{ url('/dashboard/laporan/activities') }}" style="border-radius: 10px; transition: all 0.2s;">
                <i class="bi bi-calendar-event me-2"></i>Laporan Kegiatan
            </a>
        </li>
        @if($user->hasRole('kominfo'))
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 fw-semibold" id="ttd-tab" data-bs-toggle="tab" data-bs-target="#ttd-tab-pane" type="button" role="tab" aria-controls="ttd-tab-pane" aria-selected="false" style="border-radius: 10px; transition: all 0.2s;">
                <i class="bi bi-pencil-square me-2"></i>Pengaturan TTD
            </button>
        </li>
        @endif
    </ul>

    <div class="tab-content" id="laporanTabsContent">
        <!-- Tab 1: Data Laporan -->
        <div class="tab-pane fade show active" id="laporan-tab-pane" role="tabpanel" tabindex="0">
            
            @if($activeTab === 'complaints')
                <!-- Panel Filter Pengaduan -->
                <div class="card border-0 bg-light p-4 mb-4" style="border-radius: var(--sippm-radius-sm); border: 1px solid rgba(22, 52, 92, 0.08) !important;">
                    <h5 class="h6 text-sippm fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-funnel-fill text-warning" style="color: var(--sippm-gold) !important;"></i>
                        Saring & Cari Laporan Pengaduan
                    </h5>
                    <form method="get" action="{{ url('/dashboard/laporan') }}" class="row g-3">
                        <div class="col-lg-6">
                            <label class="form-label text-sippm fw-semibold small">Cari Laporan</label>
                            <div class="input-group shadow-sm" style="border-radius: var(--sippm-radius-sm); overflow: hidden;">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 ps-1" placeholder="Masukkan nomor tiket atau judul pengaduan..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label text-sippm fw-semibold small">Tujuan Pelaporan</label>
                            <select name="target" class="form-select shadow-sm">
                                <option value="">Semua Dinas (OPD) & Kecamatan</option>
                                <optgroup label="Organisasi Perangkat Daerah (OPD)">
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

                        <div class="col-sm-6 col-md-3">
                            <label class="form-label text-sippm fw-semibold small">Status</label>
                            <select name="status" class="form-select shadow-sm">
                                <option value="">Semua Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label text-sippm fw-semibold small">Hari</label>
                            <select name="hari" class="form-select shadow-sm">
                                <option value="">Semua Hari</option>
                                @foreach($hariOptions as $index => $hari)
                                    <option value="{{ $index }}" @selected(request('hari') === (string) $index)>{{ $hari }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label text-sippm fw-semibold small">Bulan</label>
                            <select name="bulan" class="form-select shadow-sm">
                                <option value="">Semua Bulan</option>
                                @foreach($bulanOptions as $index => $bulan)
                                    <option value="{{ $index }}" @selected(request('bulan') === (string) $index)>{{ $bulan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label text-sippm fw-semibold small">Tahun</label>
                            <select name="tahun" class="form-select shadow-sm">
                                <option value="">Semua Tahun</option>
                                @foreach($tahunOptions as $tahun)
                                    <option value="{{ $tahun }}" @selected(request('tahun') === (string) $tahun)>{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tombol Aksi Kelompok -->
                        <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-3 mt-3 pt-2 border-top border-2 border-white">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-sippm px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2" style="background-color: var(--sippm-navy); border-color: var(--sippm-navy);">
                                    <i class="bi bi-search"></i>
                                    <span>Cari Data</span>
                                </button>
                                @if(request()->anyFilled(['search','status','target','hari','bulan','tahun']))
                                    <a href="{{ url('/dashboard/laporan') }}" class="btn btn-outline-secondary px-4 py-2 d-inline-flex align-items-center gap-2" style="border-radius: var(--sippm-radius-sm);">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                        <span>Reset</span>
                                    </a>
                                @endif
                            </div>
                            @if($user->hasRole('kominfo'))
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-primary px-3 py-2 d-inline-flex align-items-center gap-2" style="border-radius: var(--sippm-radius-sm);" @click="document.getElementById('ttd-tab').click()">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>Konfig TTD</span>
                                </button>
                                <a href="{{ url('/dashboard/laporan/export-pdf') }}?{{ http_build_query(request()->query()) }}" target="_blank" rel="noopener" class="btn btn-danger px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: var(--sippm-radius-sm); background-color: var(--sippm-red); border-color: var(--sippm-red);">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                    <span>Preview PDF</span>
                                </a>
                                <a href="{{ url('/dashboard/laporan/export-excel') }}?{{ http_build_query(request()->query()) }}" class="btn btn-success px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: var(--sippm-radius-sm); background-color: var(--sippm-green); border-color: var(--sippm-green);">
                                    <i class="bi bi-file-earmark-excel-fill"></i>
                                    <span>Ekspor Excel</span>
                                </a>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Tabel Data Pengaduan Premium -->
                <div class="card border-0 shadow-sm" style="border-radius: var(--sippm-radius-sm); overflow: hidden; border: 1px solid var(--sippm-border) !important;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light" style="border-bottom: 2px solid var(--sippm-border);">
                                <tr>
                                    <th class="px-4 py-3 text-sippm fw-bold text-uppercase small" style="letter-spacing: 0.05em;">No. Tiket</th>
                                    <th class="py-3 text-sippm fw-bold text-uppercase small" style="letter-spacing: 0.05em;">Judul Pengaduan</th>
                                    <th class="py-3 text-sippm fw-bold text-uppercase small" style="letter-spacing: 0.05em;">Kategori</th>
                                    <th class="py-3 text-sippm fw-bold text-uppercase small" style="letter-spacing: 0.05em;">Tujuan</th>
                                    <th class="py-3 text-sippm fw-bold text-uppercase small" style="letter-spacing: 0.05em; text-align: center;">Status</th>
                                    <th class="py-3 text-sippm fw-bold text-uppercase small" style="letter-spacing: 0.05em;">Tanggal Masuk</th>
                                    <th class="px-4 py-3 text-sippm fw-bold text-uppercase small" style="letter-spacing: 0.05em; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($complaints as $complaint)
                                <tr style="transition: background-color 0.2s;">
                                    <td class="px-4 py-3 font-monospace small fw-bold text-secondary">{{ $complaint->ticket_number }}</td>
                                    <td class="py-3">
                                        <div class="fw-semibold text-dark">{{ $complaint->title }}</div>
                                    </td>
                                    <td class="py-3"><span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 rounded">{{ ucfirst($complaint->category) }}</span></td>
                                    <td class="py-3 small text-muted">
                                        @if($complaint->target_type === 'opd')
                                            <div class="d-flex align-items-center gap-1">
                                                <i class="bi bi-building text-secondary"></i>
                                                <span>{{ $opds->firstWhere('id', $complaint->target_id)?->name ?? '-' }}</span>
                                            </div>
                                        @elseif($complaint->target_type === 'camat')
                                            <div class="d-flex align-items-center gap-1">
                                                <i class="bi bi-geo-alt text-secondary"></i>
                                                <span>{{ $kecamatans->firstWhere('id', $complaint->target_id)?->name ?? '-' }}</span>
                                            </div>
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', (string) $complaint->target_type)) }}
                                        @endif
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="sippm-badge badge-status-{{ $complaint->status->value }} px-3 py-1.5 shadow-none" style="font-size: 0.75rem; border-radius: 30px;">
                                            {{ $complaint->status->label() }}
                                        </span>
                                    </td>
                                    <td class="py-3 small text-muted">
                                        <div class="d-flex align-items-center gap-1">
                                            <i class="bi bi-calendar3 opacity-75"></i>
                                            <span>{{ $complaint->created_at->translatedFormat('d M Y') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ url('/dashboard/complaints/'.$complaint->id) }}" class="btn btn-sm btn-sippm px-3 py-1.5 d-inline-flex align-items-center gap-1" style="font-size: 0.8rem; background-color: var(--sippm-navy);">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox-fill fs-2 text-secondary opacity-25 d-block mb-2"></i>
                                        Tidak ada data laporan yang cocok dengan filter pencarian Anda.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-center">
                    {{ $complaints->links() }}
                </div>
            @else
                <!-- Panel Filter Kegiatan -->
                <div class="card border-0 bg-light p-4 mb-4" style="border-radius: var(--sippm-radius-sm); border: 1px solid rgba(22, 52, 92, 0.08) !important;">
                    <h5 class="h6 text-sippm fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-funnel-fill text-warning" style="color: var(--sippm-gold) !important;"></i>
                        Saring & Cari Kegiatan
                    </h5>
                    <form method="get" action="{{ url('/dashboard/laporan/activities') }}" class="row g-3">
                        <div class="col-lg-6">
                            <label class="form-label text-sippm fw-semibold small">Cari Kegiatan</label>
                            <div class="input-group shadow-sm" style="border-radius: var(--sippm-radius-sm); overflow: hidden;">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 ps-1" placeholder="Masukkan judul kegiatan atau lokasi..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label text-sippm fw-semibold small">Penerbit (OPD/Kecamatan)</label>
                            <select name="target" class="form-select shadow-sm">
                                <option value="">Semua Dinas (OPD) & Kecamatan</option>
                                <optgroup label="Organisasi Perangkat Daerah (OPD)">
                                    @foreach($opds as $opd)
                                        <option value="opd:{{ $opd->id }}" @selected(request('target') === 'opd:'.$opd->id)>{{ $opd->name }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Kecamatan">
                                    @foreach($kecamatans as $kecamatan)
                                        <option value="kecamatan:{{ $kecamatan->id }}" @selected(request('target') === 'kecamatan:'.$kecamatan->id)>{{ $kecamatan->name }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label text-sippm fw-semibold small">Status</label>
                            <select name="status" class="form-select shadow-sm">
                                <option value="">Semua Status</option>
                                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                                <option value="diverifikasi" @selected(request('status') === 'diverifikasi')>Diverifikasi</option>
                                <option value="dipublikasikan" @selected(request('status') === 'dipublikasikan')>Dipublikasikan</option>
                                <option value="ditolak" @selected(request('status') === 'ditolak')>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label text-sippm fw-semibold small">Dari Tanggal</label>
                            <input type="date" name="date_from" class="form-control shadow-sm" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label text-sippm fw-semibold small">Sampai Tanggal</label>
                            <input type="date" name="date_to" class="form-control shadow-sm" value="{{ request('date_to') }}">
                        </div>

                        <!-- Tombol Aksi Kelompok -->
                        <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-3 mt-3 pt-2 border-top border-2 border-white">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-sippm px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2" style="background-color: var(--sippm-navy); border-color: var(--sippm-navy);">
                                    <i class="bi bi-search"></i>
                                    <span>Cari Data</span>
                                </button>
                                @if(request()->anyFilled(['search','status','target','date_from','date_to']))
                                    <a href="{{ url('/dashboard/laporan/activities') }}" class="btn btn-outline-secondary px-4 py-2 d-inline-flex align-items-center gap-2" style="border-radius: var(--sippm-radius-sm);">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                        <span>Reset</span>
                                    </a>
                                @endif
                            </div>
                            @if($user->hasRole('kominfo'))
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ url('/dashboard/laporan/activities/export-pdf') }}?{{ http_build_query(request()->query()) }}" target="_blank" rel="noopener" class="btn btn-danger px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: var(--sippm-radius-sm); background-color: var(--sippm-red); border-color: var(--sippm-red);">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                    <span>Preview PDF</span>
                                </a>
                                <a href="{{ url('/dashboard/laporan/activities/export-excel') }}?{{ http_build_query(request()->query()) }}" class="btn btn-success px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: var(--sippm-radius-sm); background-color: var(--sippm-green); border-color: var(--sippm-green);">
                                    <i class="bi bi-file-earmark-excel-fill"></i>
                                    <span>Ekspor Excel</span>
                                </a>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Tabel Data Kegiatan Premium -->
                <div class="card border-0 shadow-sm" style="border-radius: var(--sippm-radius-sm); overflow: hidden; border: 1px solid var(--sippm-border) !important;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light" style="border-bottom: 2px solid var(--sippm-border);">
                                <tr>
                                    <th class="px-4 py-3 text-sippm fw-bold text-uppercase small" style="letter-spacing: 0.05em; width: 40%;">Judul Kegiatan</th>
                                    <th class="py-3 text-sippm fw-bold text-uppercase small" style="letter-spacing: 0.05em; width: 25%;">Penerbit (OPD/Kecamatan)</th>
                                    <th class="py-3 text-sippm fw-bold text-uppercase small" style="letter-spacing: 0.05em; width: 15%;">Lokasi</th>
                                    <th class="py-3 text-sippm fw-bold text-uppercase small" style="letter-spacing: 0.05em; text-align: center; width: 8%;">Status</th>
                                    <th class="py-3 text-sippm fw-bold text-uppercase small" style="letter-spacing: 0.05em; width: 12%;">Tanggal</th>
                                    <th class="px-4 py-3 text-sippm fw-bold text-uppercase small" style="letter-spacing: 0.05em; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($activities as $act)
                                <tr style="transition: background-color 0.2s;">
                                    <td class="px-4 py-3">
                                        <div class="fw-semibold text-dark">{{ $act->title }}</div>
                                    </td>
                                    <td class="py-3 small text-muted">
                                        <div class="d-flex align-items-center gap-1">
                                            <i class="bi bi-building text-secondary"></i>
                                            <span>{{ $act->actor ? $act->actor->name : '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 small text-muted">{{ $act->location ?? '-' }}</td>
                                    <td class="py-3 text-center">
                                        <span class="sippm-badge sippm-badge-{{ $act->status->value === 'dipublikasikan' ? 'green' : ($act->status->value === 'ditolak' ? 'red' : 'amber') }} px-3 py-1.5 shadow-none" style="font-size: 0.75rem; border-radius: 30px;">
                                            {{ $act->status->label() }}
                                        </span>
                                    </td>
                                    <td class="py-3 small text-muted">
                                        <div class="d-flex align-items-center gap-1">
                                            <i class="bi bi-calendar3 opacity-75"></i>
                                            <span>{{ $act->date->translatedFormat('d M Y') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ url('/dashboard/activities/'.$act->id) }}" class="btn btn-sm btn-sippm px-3 py-1.5 d-inline-flex align-items-center gap-1" style="font-size: 0.8rem; background-color: var(--sippm-navy);">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox-fill fs-2 text-secondary opacity-25 d-block mb-2"></i>
                                        Tidak ada data kegiatan yang cocok dengan filter pencarian Anda.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-center">
                    {{ $activities->links() }}
                </div>
            @endif
        </div>

        @if($user->hasRole('kominfo'))
        <!-- Tab 2: Pengaturan TTD (Side-by-Side Layout) -->
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
                <div class="row g-4">
                    <!-- Kolom Kiri: Form Pengisian -->
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm p-4 bg-light" style="border-radius: var(--sippm-radius-sm); border: 1px solid var(--sippm-border) !important;">
                            <h5 class="h6 text-sippm fw-bold mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-pencil-square text-warning" style="color: var(--sippm-gold) !important;"></i>
                                Konfigurasi Penandatangan
                            </h5>
                            
                            <form method="post" action="{{ url('/dashboard/laporan/ttd') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label text-sippm fw-semibold small">Nama Penandatangan</label>
                                    <input type="text" name="nama_penandatangan" class="form-control bg-white shadow-sm" x-model="nama" required placeholder="Contoh: Drs. H. Dahlan Hasan Nasution">
                                    @error('nama_penandatangan')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-sippm fw-semibold small">Jabatan Dinas</label>
                                    <input type="text" name="jabatan_penandatangan" class="form-control bg-white shadow-sm" x-model="jabatan" required placeholder="Contoh: KEPALA DINAS KOMUNIKASI DAN INFORMATIKA">
                                    @error('jabatan_penandatangan')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-sippm fw-semibold small">Pangkat / Golongan</label>
                                    <input type="text" name="pangkat" class="form-control bg-white shadow-sm" x-model="pangkat" placeholder="Contoh: Pembina Tk. I (IV/b)">
                                    @error('pangkat')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-sippm fw-semibold small">NIP</label>
                                    <input type="text" name="nip" class="form-control bg-white shadow-sm" inputmode="numeric" x-model="nip" required placeholder="Contoh: 197208151998031003">
                                    @error('nip')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <button type="submit" class="btn btn-sippm w-100 py-2.5 mt-2 d-flex align-items-center justify-content-center gap-2" style="background-color: var(--sippm-navy); border-color: var(--sippm-navy);">
                                    <i class="bi bi-save-fill"></i>
                                    <span>Simpan Pengaturan</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Live Preview Resmi -->
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm" style="border-radius: var(--sippm-radius-sm); border: 1px solid var(--sippm-border) !important; height: 100%;">
                            <div class="card-header bg-light py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid var(--sippm-border);">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-eye-fill text-sippm"></i>
                                    <span class="fw-semibold text-sippm">Preview TTD</span>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-center bg-white p-4" style="background-image: radial-gradient(rgba(22, 52, 92, 0.03) 1px, transparent 1px); background-size: 16px 16px;">
                                <div class="mx-auto w-100 p-4 border rounded-3" style="max-width: 480px; background-color: #ffffff; box-shadow: var(--sippm-shadow-soft); position: relative;">
                                    
                                    <div class="ms-auto" style="max-width: 320px; text-align: left; font-family: 'Times New Roman', Times, serif; color: #000; line-height: 1.5;">
                                        <!-- Tempat & Tanggal -->
                                        <p class="fst-italic mb-3" style="font-size: 1rem;">Panyabungan, {{ now()->translatedFormat('F Y') }}</p>
                                        
                                        <!-- Jabatan Dinas -->
                                        <p class="fw-bold text-uppercase mb-0" style="font-size: 0.95rem; min-height: 2.8em;" x-text="jabatan || 'JABATAN DINAS PENANDATANGAN'"></p>
                                        
                                        <!-- Area Kosong Tanda Tangan -->
                                        <div style="height: 80px;"></div>
                                        
                                        <!-- Nama Pejabat -->
                                        <p class="fw-bold text-uppercase mb-1" style="font-size: 1rem;" x-text="nama || 'NAMA PEJABAT PENANDATANGAN'"></p>
                                        
                                        <!-- Pangkat / Golongan -->
                                        <p class="fst-italic mb-1" style="font-size: 0.9rem;" x-text="pangkat || 'Pangkat / Golongan'"></p>
                                        
                                        <!-- NIP -->
                                        <p class="mb-0" style="font-size: 0.95rem;" x-text="'NIP. ' + (formattedNip || 'NIP PEJABAT')"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
