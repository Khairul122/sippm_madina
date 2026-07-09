@extends('layouts.dashboard')

@section('content')
<div class="sippm-card-raised p-4 mb-4">
    <div class="border-bottom pb-2 mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-signpost-2 text-sippm fs-4"></i>
        <h2 class="h5 mb-0 font-weight-bold" style="font-family: 'Poppins', sans-serif;">Ubah Kecamatan</h2>
    </div>
    <form method="post" action="{{ url('/dashboard/kecamatan/'.$kecamatan->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Nama Kecamatan</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $kecamatan->name) }}" required>
        </div>
        <div class="mb-4">
            <label class="form-label">Kode</label>
            <input type="text" name="code" class="form-control" value="{{ old('code', $kecamatan->code) }}" required>
        </div>
        <div class="border-top pt-3 d-flex justify-content-end gap-2">
            <a href="{{ url('/dashboard/kecamatan') }}" class="btn btn-light btn-sm px-3 rounded-3" style="border: 1px solid rgba(0,0,0,0.1);">Batal</a>
            <button type="submit" class="btn btn-sippm btn-sm px-4 rounded-3 fw-semibold">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
