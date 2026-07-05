@extends('layouts.dashboard')

@section('content')
<div class="sippm-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <form method="get" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama/kode OPD..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sippm btn-sm"><i class="bi bi-search"></i></button>
        </form>
        <a href="{{ url('/dashboard/opd/create') }}" class="btn btn-sippm"><i class="bi bi-building-add me-1"></i>Tambah OPD</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Nama OPD</th><th>Kode</th><th>Pengguna</th><th>Kegiatan</th><th></th></tr></thead>
            <tbody>
            @forelse($opds as $opd)
                <tr>
                    <td>{{ $opd->name }}</td>
                    <td class="font-monospace small">{{ $opd->code }}</td>
                    <td>{{ $opd->users_count }}</td>
                    <td>{{ $opd->activities_count }}</td>
                    <td class="text-nowrap">
                        <a href="{{ url('/dashboard/opd/'.$opd->id.'/edit') }}" class="btn btn-sm btn-outline-secondary">Ubah</a>
                        <form method="post" action="{{ url('/dashboard/opd/'.$opd->id) }}" class="d-inline" data-confirm="Hapus data OPD &quot;{{ $opd->name }}&quot;? Aksi ini tidak dapat dibatalkan.">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data OPD.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $opds->links() }}
</div>
@endsection
