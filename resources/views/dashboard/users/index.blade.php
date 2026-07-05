@extends('layouts.dashboard')

@section('content')
<div class="sippm-card p-4">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ url('/dashboard/users/create') }}" class="btn btn-sippm"><i class="bi bi-person-plus me-1"></i>Tambah Pengguna</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Nama</th><th>Email</th><th>Peran</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($users as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ ucfirst(str_replace('_',' ', $u->getRoleNames()->implode(', '))) }}</td>
                    <td><span class="sippm-badge sippm-badge-{{ $u->is_active ? 'green' : 'red' }}">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td class="text-nowrap">
                        @unless($u->hasRole('masyarakat'))
                            <a href="{{ url('/dashboard/users/'.$u->id.'/edit') }}" class="btn btn-sm btn-outline-secondary">Ubah</a>
                            @if($u->id !== auth()->id())
                            <form method="post" action="{{ url('/dashboard/users/'.$u->id.'/toggle-active') }}" class="d-inline" data-confirm="{{ $u->is_active ? 'Nonaktifkan akun '.$u->name.'? Pengguna ini tidak akan bisa masuk lagi sampai diaktifkan kembali.' : 'Aktifkan kembali akun '.$u->name.'?' }}">
                                @csrf
                                <button class="btn btn-sm {{ $u->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" type="submit">
                                    {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            @endif
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pengguna.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</div>
@endsection
