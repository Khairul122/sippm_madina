@extends('layouts.dashboard')

@section('content')
<div class="sippm-card p-4">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ url('/dashboard/categories/create') }}" class="btn btn-sippm"><i class="bi bi-tag-fill me-1"></i>Tambah Kategori</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Slug</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($categories as $cat)
                <tr>
                    <td>{{ $cat->name }}</td>
                    <td class="font-monospace small text-muted">{{ $cat->slug }}</td>
                    <td class="text-nowrap">
                        <a href="{{ url('/dashboard/categories/'.$cat->id.'/edit') }}" class="btn btn-sm btn-outline-secondary">Ubah</a>
                        <form method="post" action="{{ url('/dashboard/categories/'.$cat->id) }}" class="d-inline" data-confirm="Hapus kategori &quot;{{ $cat->name }}&quot;?">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada kategori pengaduan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $categories->links() }}
</div>
@endsection
