@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-2">
    <!-- Title Page & Summary -->
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark" style="font-family: 'Poppins', sans-serif; color: var(--sippm-navy) !important;">Daftar Pengaduan</h1>
            <p class="text-muted small mb-0">Kelola, verifikasi, dan tindak lanjuti laporan pengaduan dari masyarakat.</p>
        </div>
        @if($complaints->total() > 0)
            <div>
                <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm small">
                    Total data terfilter: <strong>{{ $complaints->total() }}</strong> pengaduan
                </span>
            </div>
        @endif
    </div>

    <!-- Spacious Filters Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--sippm-radius-lg); background: #ffffff;">
        <div class="card-body p-4">
            <form method="get" action="{{ url('/dashboard/complaints') }}">
                <!-- Grid Row 1 -->
                <div class="row g-3">
                    <!-- Search Input -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-secondary">Pencarian</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nomor tiket atau kata kunci..." value="{{ request('search') }}" style="font-size: 0.9rem;">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small text-secondary">Status</label>
                        <select name="status" class="form-select" onchange="this.form.submit()" style="font-size: 0.9rem;">
                            <option value="">Semua Status</option>
                            @foreach(['diajukan','diverifikasi','diproses','ditindaklanjuti','selesai','ditolak'] as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kategori Filter -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-secondary">Kategori</label>
                        <select name="category" class="form-select" onchange="this.form.submit()" style="font-size: 0.9rem;">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" @selected(request('category') === $category)>{{ ucfirst($category) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tujuan Dinas Filter -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-secondary">Tujuan Dinas / Camat</label>
                        <select name="target" class="form-select" onchange="this.form.submit()" style="font-size: 0.9rem;">
                            <option value="">Semua Tujuan</option>
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
                </div>

                <!-- Grid Row 2 -->
                <div class="row g-3 mt-1 align-items-end">
                    <!-- Dari Tanggal -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-secondary">Dari Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-calendar3"></i></span>
                            <input type="date" name="date_from" class="form-control border-start-0 ps-0" value="{{ request('date_from') }}" style="font-size: 0.9rem;" onchange="this.form.submit()">
                        </div>
                    </div>

                    <!-- Sampai Tanggal -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-secondary">Sampai Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-calendar3"></i></span>
                            <input type="date" name="date_to" class="form-control border-start-0 ps-0" value="{{ request('date_to') }}" style="font-size: 0.9rem;" onchange="this.form.submit()">
                        </div>
                    </div>

                    <!-- Filter Action Buttons -->
                    <div class="col-md-6 d-flex justify-content-end gap-2">
                        @if(request()->anyFilled(['search','status','category','target','date_from','date_to']))
                            <a href="{{ url('/dashboard/complaints') }}" class="btn btn-light border px-4 py-2 rounded-3 fw-semibold text-secondary d-flex align-items-center gap-1" style="font-size: 0.9rem;">
                                <i class="bi bi-x-circle"></i> Reset Filter
                            </a>
                        @endif
                        <button type="submit" class="btn btn-sippm px-4 py-2 rounded-3 fw-semibold text-white shadow-sm d-flex align-items-center gap-1" style="background-color: var(--sippm-navy); border: none; font-size: 0.9rem;">
                            <i class="bi bi-funnel"></i> Terapkan Pencarian
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table complaints list -->
    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: var(--sippm-radius-lg); background: #ffffff;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary small uppercase tracking-wider" style="border-bottom: 2px solid rgba(22, 52, 92, 0.08);">
                    <tr>
                        <th class="py-3 px-4" style="width: 15%;">Nomor Tiket</th>
                        <th class="py-3" style="width: 30%;">Judul Pengaduan</th>
                        <th class="py-3" style="width: 15%;">Nama Pengadu</th>
                        <th class="py-3" style="width: 15%;">Kategori</th>
                        <th class="py-3" style="width: 12%;">Status</th>
                        <th class="py-3" style="width: 13%;">Tanggal Masuk</th>
                        <th class="py-3 text-end px-4" style="width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($complaints as $complaint)
                    <tr class="complaint-row" style="transition: all 0.2s ease;">
                        <!-- Ticket number with custom design -->
                        <td class="py-3 px-4">
                            <span class="font-monospace px-2.5 py-1 rounded bg-light border text-navy fw-semibold small" style="color: var(--sippm-navy) !important; font-size: 0.85rem;">
                                {{ $complaint->ticket_number }}
                            </span>
                        </td>
                        <!-- Title -->
                        <td class="py-3 fw-semibold text-dark" style="font-size: 0.95rem;">
                            {{ Str::limit($complaint->title, 55) }}
                        </td>
                        <!-- Reporter name -->
                        <td class="py-3 text-secondary small">
                            {{ $complaint->user->name }}
                        </td>
                        <!-- Category -->
                        <td class="py-3">
                            <span class="badge bg-light text-dark border px-2 py-1 rounded small">
                                {{ ucfirst($complaint->category) }}
                            </span>
                        </td>
                        <!-- Status Badge -->
                        <td class="py-3">
                            <span class="badge rounded-pill px-3 py-1.5 fw-semibold badge-status-{{ $complaint->status->value }}" style="font-size: 0.72rem; letter-spacing: 0.03em;">
                                {{ $complaint->status->label() }}
                            </span>
                        </td>
                        <!-- Submited Date -->
                        <td class="py-3 text-muted small">
                            {{ $complaint->created_at->translatedFormat('d M Y') }}
                        </td>
                        <!-- Action link -->
                        <td class="py-3 text-end px-4">
                            <a href="{{ url('/dashboard/complaints/'.$complaint->id) }}" class="btn btn-sm px-3 rounded-pill fw-semibold text-white d-inline-flex align-items-center gap-1 shadow-sm transition-all" style="background-color: var(--sippm-navy); border: none; font-size: 0.8rem;">
                                Detail <i class="bi bi-chevron-right small"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <div class="d-flex flex-column align-items-center gap-3">
                                <i class="bi bi-chat-square-dots text-secondary opacity-50" style="font-size: 48px;"></i>
                                <div>
                                    <h5 class="fw-bold mb-1">Belum Ada Pengaduan</h5>
                                    <p class="text-muted small mb-0">Tidak ada data laporan pengaduan yang sesuai filter saat ini.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginator footer -->
        @if($complaints->hasPages())
            <div class="card-footer bg-transparent border-top py-3 px-4 d-flex justify-content-center">
                {{ $complaints->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    /* Focus animations for dropdowns and inputs */
    .input-group:focus-within {
        box-shadow: 0 0 0 3px rgba(22, 52, 92, 0.15);
        border-radius: 6px;
    }
    .input-group:focus-within .input-group-text,
    .input-group:focus-within input {
        border-color: var(--sippm-navy) !important;
    }
    .form-select:focus {
        border-color: var(--sippm-navy) !important;
        box-shadow: 0 0 0 3px rgba(22, 52, 92, 0.15) !important;
    }
    .input-group-text, input, select {
        transition: all 0.2s ease;
    }
    /* Hover highlight rows */
    .complaint-row:hover {
        background-color: rgba(22, 52, 92, 0.02) !important;
    }
</style>
@endsection
