@extends('layouts.dashboard')

@section('content')
<div class="sippm-card p-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <h2 class="h5 mb-0">Kelola Pengguna</h2>
        <a href="{{ url('/dashboard/users/create') }}" class="btn btn-sippm"><i class="bi bi-person-plus me-1"></i>Tambah Pengguna</a>
    </div>

    <form method="get" action="{{ url('/dashboard/users') }}" class="row g-3 mb-4 p-3" style="background-color: rgba(22, 52, 92, 0.03); border-radius: var(--sippm-radius-sm);">
        <div class="col-md-5">
            <label class="form-label small fw-semibold text-sippm">Cari Pengguna</label>
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-1" placeholder="Nama atau email..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-sippm">Peran</label>
            <select name="role" class="form-select shadow-sm">
                <option value="">Semua Peran</option>
                @foreach($roles as $role)
                    <option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold text-sippm">Status</label>
            <select name="status" class="form-select shadow-sm">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-sippm flex-grow-1"><i class="bi bi-funnel me-1"></i>Saring</button>
            @if(request()->anyFilled(['search','role','status']))
                <a href="{{ url('/dashboard/users') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
            @endif
        </div>
    </form>

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
                            <form method="post" action="{{ url('/dashboard/users/'.$u->id) }}" class="d-inline" data-confirm="Hapus akun {{ $u->name }} secara permanen?">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">
                                    Hapus
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
