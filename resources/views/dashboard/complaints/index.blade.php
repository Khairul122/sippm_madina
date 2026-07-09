@extends('layouts.dashboard')

@section('content')
<div class="sippm-card p-4">
    <form method="get" class="row g-2 mb-3 align-items-center">
        <div class="col-auto">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                @foreach(['diajukan','diverifikasi','diproses','ditindaklanjuti','selesai','ditolak'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ ucfirst($category) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <select name="target" class="form-select form-select-sm" onchange="this.form.submit()">
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
        <div class="col-auto">
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" title="Dari tanggal" onchange="this.form.submit()">
        </div>
        <div class="col-auto">
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" title="Sampai tanggal" onchange="this.form.submit()">
        </div>
        <div class="col-auto">
            <div class="input-group input-group-sm">
                <input type="text" name="search" class="form-control" placeholder="Cari tiket / judul..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-sippm btn-sm"><i class="bi bi-search"></i> Cari</button>
            </div>
        </div>
        @if(request()->anyFilled(['search','status','category','target','date_from','date_to']))
            <div class="col-auto">
                <a href="{{ url('/dashboard/complaints') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-circle"></i> Reset</a>
            </div>
        @endif
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Tiket</th>
                    <th>Judul</th>
                    <th>Pengadu</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Diajukan</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($complaints as $complaint)
                <tr>
                    <td class="font-monospace small">{{ $complaint->ticket_number }}</td>
                    <td>{{ $complaint->title }}</td>
                    <td>{{ $complaint->user->name }}</td>
                    <td>{{ ucfirst($complaint->category) }}</td>
                    <td><span class="badge badge-status-{{ $complaint->status->value }}">{{ $complaint->status->label() }}</span></td>
                    <td class="small text-muted">{{ $complaint->created_at->translatedFormat('d M Y') }}</td>
                    <td><a href="{{ url('/dashboard/complaints/'.$complaint->id) }}" class="btn btn-sm btn-sippm">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada pengaduan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $complaints->links() }}
</div>
@endsection
