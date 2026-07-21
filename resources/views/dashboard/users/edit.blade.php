@extends('layouts.dashboard')

@section('content')
<div class="sippm-card-raised p-4 mb-4">
    <div class="border-bottom pb-2 mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-person-gear text-sippm fs-4"></i>
        <h2 class="h5 mb-0 font-weight-bold" style="font-family: 'Poppins', sans-serif;">Edit Pengguna</h2>
    </div>
    <form method="post" action="{{ url('/dashboard/users/'.$targetUser->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $targetUser->name) }}" required>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nomor Telepon</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $targetUser->phone) }}" placeholder="Contoh: 081234567890">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">NIP / NIK</label>
                <input type="text" name="nik" class="form-control" value="{{ old('nik', $targetUser->nik) }}" placeholder="Masukkan NIP atau NIK...">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $targetUser->email) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Kata Sandi Baru</label>
            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin diubah...">
            <div class="form-text">Minimal 8 karakter.</div>
        </div>
        @if($targetUser->hasRole('opd'))
        <div class="mb-4">
            <label class="form-label">Instansi OPD</label>
            <select name="opd_id" class="form-select">
                @foreach($opds as $opd)
                    <option value="{{ $opd->id }}" @selected($targetUser->opd_id === $opd->id)>{{ $opd->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        @if($targetUser->hasRole('camat'))
        <div class="mb-4">
            <label class="form-label">Wilayah Kecamatan</label>
            <select name="kecamatan_id" class="form-select">
                @foreach($kecamatans as $kecamatan)
                    <option value="{{ $kecamatan->id }}" @selected($targetUser->kecamatan_id === $kecamatan->id)>{{ $kecamatan->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="border-top pt-3 d-flex justify-content-end gap-2">
            <a href="{{ url('/dashboard/users') }}" class="btn btn-light btn-sm px-3 rounded-3" style="border: 1px solid rgba(0,0,0,0.1);">Batal</a>
            <button type="submit" class="btn btn-sippm btn-sm px-4 rounded-3 fw-semibold">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
