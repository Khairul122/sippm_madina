@extends('layouts.dashboard')

@section('content')
<div class="sippm-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <form method="get" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama/kode kecamatan..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sippm btn-sm"><i class="bi bi-search"></i></button>
        </form>
        <a href="{{ url('/dashboard/kecamatan/create') }}" class="btn btn-sippm"><i class="bi bi-signpost-2 me-1"></i>Tambah Kecamatan</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Nama Kecamatan</th><th>Kode</th><th>Desa</th><th>Pengguna</th><th>Kegiatan</th><th></th></tr></thead>
            <tbody>
            @forelse($kecamatans as $kecamatan)
                <tr>
                    <td>{{ $kecamatan->name }}</td>
                    <td class="font-monospace small">{{ $kecamatan->code }}</td>
                    <td><a href="{{ url('/dashboard/desa?kecamatan_id='.$kecamatan->id) }}">{{ $kecamatan->desas_count }}</a></td>
                    <td>{{ $kecamatan->users_count }}</td>
                    <td>{{ $kecamatan->activities_count }}</td>
                    <td class="text-nowrap">
                        <a href="{{ url('/dashboard/kecamatan/'.$kecamatan->id.'/edit') }}" class="btn btn-sm btn-outline-secondary">Ubah</a>
                        <form method="post" action="{{ url('/dashboard/kecamatan/'.$kecamatan->id) }}" class="d-inline" data-confirm="Hapus data kecamatan &quot;{{ $kecamatan->name }}&quot;? Aksi ini tidak dapat dibatalkan.">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data kecamatan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $kecamatans->links() }}
</div>
@endsection
