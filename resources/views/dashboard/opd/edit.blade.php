@extends('layouts.dashboard')

@section('content')
<div class="sippm-card-raised p-4 mb-4" style="max-width:560px;">
    <div class="border-bottom pb-2 mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-building-gear text-sippm fs-4"></i>
        <h2 class="h5 mb-0 font-weight-bold" style="font-family: 'Poppins', sans-serif;">Ubah OPD</h2>
    </div>
    <form method="post" action="{{ url('/dashboard/opd/'.$opd->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Nama OPD</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $opd->name) }}" required>
        </div>
        <div class="mb-4">
            <label class="form-label">Kode</label>
            <input type="text" name="code" class="form-control" value="{{ old('code', $opd->code) }}" required>
        </div>
        <div class="border-top pt-3 d-flex justify-content-end gap-2">
            <a href="{{ url('/dashboard/opd') }}" class="btn btn-light btn-sm px-3 rounded-3" style="border: 1px solid rgba(0,0,0,0.1);">Batal</a>
            <button type="submit" class="btn btn-sippm btn-sm px-4 rounded-3 fw-semibold">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
