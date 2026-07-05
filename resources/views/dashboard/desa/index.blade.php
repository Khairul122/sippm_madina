@extends('layouts.dashboard')

@section('content')
<div class="sippm-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <form method="get" class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 650px;">
            <div class="input-group shadow-sm">
                <!-- Icon Prepend -->
                <span class="input-group-text bg-white text-muted border-end-0 px-3">
                    <i class="bi bi-funnel-fill text-secondary"></i>
                </span>
                
                <!-- Kecamatan Select -->
                <select name="kecamatan_id" class="form-select border-start-0 border-end-0 bg-white" onchange="this.form.submit()" style="max-width: 180px; font-size: 0.875rem; border-left: 0; border-right: 0;">
                    <option value="">Semua Kecamatan</option>
                    @foreach($kecamatans as $kecamatan)
                        <option value="{{ $kecamatan->id }}" @selected((string) request('kecamatan_id') === (string) $kecamatan->id)>{{ $kecamatan->name }}</option>
                    @endforeach
                </select>
                
                <!-- Divider -->
                <span class="bg-light d-none d-sm-block" style="width: 1px; margin: 8px 0; z-index: 5;"></span>

                <!-- Text Input -->
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama/kode desa..." value="{{ request('search') }}" style="font-size: 0.875rem; border-left: 0;">
                
                <!-- Search Button -->
                <button type="submit" class="btn btn-sippm px-3"><i class="bi bi-search me-1"></i> Cari</button>
            </div>
            
            @if(request('search') || request('kecamatan_id'))
                <a href="{{ url('/dashboard/desa') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center px-3" style="height: 38px;" title="Reset Pencarian">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            @endif
        </form>
        <a href="{{ url('/dashboard/desa/create') }}" class="btn btn-sippm shadow-sm"><i class="bi bi-houses me-1"></i>Tambah Desa</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Nama Desa</th><th>Kecamatan</th><th>Kode</th><th></th></tr></thead>
            <tbody>
            @forelse($desas as $desa)
                <tr>
                    <td>{{ $desa->name }}</td>
                    <td>{{ $desa->kecamatan->name }}</td>
                    <td class="font-monospace small">{{ $desa->code ?? '-' }}</td>
                    <td class="text-nowrap">
                        <a href="{{ url('/dashboard/desa/'.$desa->id.'/edit') }}" class="btn btn-sm btn-outline-secondary">Ubah</a>
                        <form method="post" action="{{ url('/dashboard/desa/'.$desa->id) }}" class="d-inline" data-confirm="Hapus data desa &quot;{{ $desa->name }}&quot;? Aksi ini tidak dapat dibatalkan.">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data desa.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $desas->links() }}
</div>
@endsection
