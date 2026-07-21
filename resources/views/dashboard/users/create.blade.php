@extends('layouts.dashboard')

@section('content')
<div class="sippm-card-raised p-4 mb-4" x-data="{ role: '' }">
    <div class="border-bottom pb-2 mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-person-plus-fill text-sippm fs-4"></i>
        <h2 class="h5 mb-0 font-weight-bold" style="font-family: 'Poppins', sans-serif;">Tambah Pengguna Baru</h2>
    </div>
    <form method="post" action="{{ url('/dashboard/users') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Nama lengkap staf/pejabat...">
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nomor Telepon</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Contoh: 081234567890">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">NIP / NIK</label>
                <input type="text" name="nik" class="form-control" value="{{ old('nik') }}" placeholder="Masukkan NIP atau NIK...">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="Contoh: dinas@madina.go.id">
        </div>
        <div class="mb-3">
            <label class="form-label">Kata Sandi</label>
            <input type="password" name="password" class="form-control" required minlength="8" placeholder="Minimal 8 karakter...">
        </div>
        <div class="mb-3">
            <label class="form-label">Peran (Role)</label>
            <select name="role" class="form-select" x-model="role" required>
                <option value="">Pilih Peran</option>
                @foreach($roles as $role)
                    <option value="{{ $role->value }}">{{ $role->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4" x-show="role === 'opd'" x-cloak>
            <label class="form-label">Instansi OPD</label>
            <select name="opd_id" class="form-select">
                @foreach($opds as $opd)<option value="{{ $opd->id }}">{{ $opd->name }}</option>@endforeach
            </select>
        </div>
        <div class="mb-4" x-show="role === 'camat'" x-cloak>
            <label class="form-label">Wilayah Kecamatan</label>
            <select name="kecamatan_id" class="form-select">
                @foreach($kecamatans as $kecamatan)<option value="{{ $kecamatan->id }}">{{ $kecamatan->name }}</option>@endforeach
            </select>
        </div>
        <div class="border-top pt-3 d-flex justify-content-end gap-2">
            <a href="{{ url('/dashboard/users') }}" class="btn btn-light btn-sm px-3 rounded-3" style="border: 1px solid rgba(0,0,0,0.1);">Batal</a>
            <button type="submit" class="btn btn-sippm btn-sm px-4 rounded-3 fw-semibold">Simpan Pengguna</button>
        </div>
    </form>
</div>
@endsection
