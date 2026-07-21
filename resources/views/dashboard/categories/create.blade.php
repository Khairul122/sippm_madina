@extends('layouts.dashboard')

@section('content')
<div class="sippm-card-raised p-4 mb-4">
    <div class="border-bottom pb-2 mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-tag text-sippm fs-4"></i>
        <h2 class="h5 mb-0 font-weight-bold" style="font-family: 'Poppins', sans-serif;">Tambah Kategori Baru</h2>
    </div>
    <form method="post" action="{{ url('/dashboard/categories') }}">
        @csrf
        <div class="mb-4">
            <label class="form-label">Nama Kategori</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Infrastruktur, Kesehatan, Pendidikan...">
        </div>
        <div class="border-top pt-3 d-flex justify-content-end gap-2">
            <a href="{{ url('/dashboard/categories') }}" class="btn btn-light btn-sm px-3 rounded-3" style="border: 1px solid rgba(0,0,0,0.1);">Batal</a>
            <button type="submit" class="btn btn-sippm btn-sm px-4 rounded-3 fw-semibold">Simpan Kategori</button>
        </div>
    </form>
</div>
@endsection
