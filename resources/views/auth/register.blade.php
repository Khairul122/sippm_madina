@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="sippm-card-raised p-4 p-md-5">
                <div class="text-center mb-3">
                    <img src="{{ asset('images/logo-madina.png') }}" alt="Lambang Kabupaten Mandailing Natal" style="height:56px; width:auto;">
                </div>
                <h1 class="h4 mb-4 text-center">Daftar Akun Masyarakat</h1>
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif
                <form method="post" action="{{ url('/register') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: var(--sippm-radius-sm) 0 0 var(--sippm-radius-sm); border: 1px solid rgba(22, 52, 92, 0.18);"><i class="bi bi-person-fill"></i></span>
                                <input type="text" name="name" class="form-control border-start-0 ps-0" value="{{ old('name') }}" required style="border-radius: 0 var(--sippm-radius-sm) var(--sippm-radius-sm) 0;">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIK (16 digit)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: var(--sippm-radius-sm) 0 0 var(--sippm-radius-sm); border: 1px solid rgba(22, 52, 92, 0.18);"><i class="bi bi-card-text"></i></span>
                                <input type="text" name="nik" class="form-control border-start-0 ps-0" value="{{ old('nik') }}" maxlength="16" required style="border-radius: 0 var(--sippm-radius-sm) var(--sippm-radius-sm) 0;">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor HP</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: var(--sippm-radius-sm) 0 0 var(--sippm-radius-sm); border: 1px solid rgba(22, 52, 92, 0.18);"><i class="bi bi-telephone-fill"></i></span>
                                <input type="text" name="phone" class="form-control border-start-0 ps-0" value="{{ old('phone') }}" required style="border-radius: 0 var(--sippm-radius-sm) var(--sippm-radius-sm) 0;">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: var(--sippm-radius-sm) 0 0 var(--sippm-radius-sm); border: 1px solid rgba(22, 52, 92, 0.18);"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" name="email" class="form-control border-start-0 ps-0" value="{{ old('email') }}" required style="border-radius: 0 var(--sippm-radius-sm) var(--sippm-radius-sm) 0;">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kata Sandi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: var(--sippm-radius-sm) 0 0 var(--sippm-radius-sm); border: 1px solid rgba(22, 52, 92, 0.18);"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="password" class="form-control border-start-0 ps-0" required minlength="8" style="border-radius: 0 var(--sippm-radius-sm) var(--sippm-radius-sm) 0;">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Konfirmasi Kata Sandi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: var(--sippm-radius-sm) 0 0 var(--sippm-radius-sm); border: 1px solid rgba(22, 52, 92, 0.18);"><i class="bi bi-shield-lock-fill"></i></span>
                                <input type="password" name="password_confirmation" class="form-control border-start-0 ps-0" required minlength="8" style="border-radius: 0 var(--sippm-radius-sm) var(--sippm-radius-sm) 0;">
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-4 mt-2">
                        <input class="form-check-input" type="checkbox" name="consent" value="1" id="consent" required>
                        <label class="form-check-label small text-muted" for="consent">
                            Saya menyetujui pemrosesan data pribadi sesuai dengan UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi.
                        </label>
                    </div>
                    <button type="submit" class="btn btn-sippm w-100 py-2.5 fw-semibold" style="transition: all 0.2s ease;">Daftar Akun</button>
                <p class="text-center small mt-3 mb-0">Sudah punya akun? <a href="{{ url('/login') }}">Masuk</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
