@extends('layouts.dashboard')

@section('content')
<div class="sippm-card-raised p-4 mb-4" style="max-width:560px;">
    <div class="border-bottom pb-2 mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-houses text-sippm fs-4"></i>
        <h2 class="h5 mb-0 font-weight-bold" style="font-family: 'Poppins', sans-serif;">Ubah Desa</h2>
    </div>
    <form method="post" action="{{ url('/dashboard/desa/'.$desa->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Kecamatan</label>
            <select name="kecamatan_id" class="form-select" required>
                @foreach($kecamatans as $kecamatan)
                    <option value="{{ $kecamatan->id }}" @selected((int) old('kecamatan_id', $desa->kecamatan_id) === $kecamatan->id)>{{ $kecamatan->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Nama Desa</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $desa->name) }}" required>
        </div>
        <div class="mb-4">
            <label class="form-label">Kode <span class="text-muted small">(opsional)</span></label>
            <input type="text" name="code" class="form-control" value="{{ old('code', $desa->code) }}">
        </div>
        <div class="border-top pt-3 d-flex justify-content-end gap-2">
            <a href="{{ url('/dashboard/desa') }}" class="btn btn-light btn-sm px-3 rounded-3" style="border: 1px solid rgba(0,0,0,0.1);">Batal</a>
            <button type="submit" class="btn btn-sippm btn-sm px-4 rounded-3 fw-semibold">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
