@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="reveal sippm-auth-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo-madina.png') }}" alt="Lambang Kabupaten Mandailing Natal" class="mb-3" style="height:60px; width:auto;">
                    <h1 class="h3 fw-bold text-sippm mb-1">Masuk ke SIPPM Madina</h1>
                    <p class="text-muted mb-0">Gunakan email dan kata sandi akun Anda untuk melanjutkan.</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>{{ $errors->first() }}</div>
                    </div>
                @endif

                <form method="post" action="{{ url('/login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: var(--sippm-radius-sm) 0 0 var(--sippm-radius-sm); border: 1px solid rgba(22, 52, 92, 0.18);"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" name="email" class="form-control border-start-0 ps-0" required autofocus style="border-radius: 0 var(--sippm-radius-sm) var(--sippm-radius-sm) 0;">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Kata Sandi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: var(--sippm-radius-sm) 0 0 var(--sippm-radius-sm); border: 1px solid rgba(22, 52, 92, 0.18);"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password" id="loginPassword" class="form-control border-start-0 border-end-0 ps-0" required>
                            <button type="button" class="btn sippm-password-toggle" style="border-radius: 0 var(--sippm-radius-sm) var(--sippm-radius-sm) 0;" data-toggle-password="#loginPassword" aria-label="Tampilkan kata sandi"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sippm btn-lg w-100 fw-semibold mt-4" style="transition: all 0.2s ease;">Masuk <i class="bi bi-arrow-right ms-2"></i></button>
                </form>
                <p class="text-center mt-4 mb-0">Belum punya akun? <a href="{{ url('/register') }}" class="fw-semibold">Daftar sebagai masyarakat</a></p>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
    @push('scripts')
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal Masuk',
                text: @json($errors->first()),
            });
        </script>
    @endpush
@endif
@endsection
