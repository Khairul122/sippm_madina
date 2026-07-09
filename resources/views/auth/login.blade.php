@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="sippm-card-raised p-4 p-md-5">
                <div class="text-center mb-3">
                    <img src="{{ asset('images/logo-madina.png') }}" alt="Lambang Kabupaten Mandailing Natal" style="height:56px; width:auto;">
                </div>
                <h1 class="h4 mb-4 text-center">Masuk ke SIPPM Madina</h1>
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
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
                            <input type="password" name="password" class="form-control border-start-0 ps-0" required style="border-radius: 0 var(--sippm-radius-sm) var(--sippm-radius-sm) 0;">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sippm w-100 py-2.5 fw-semibold mt-4" style="transition: all 0.2s ease;">Masuk</button>
                </form>
                <p class="text-center small mt-3 mb-0">Belum punya akun? <a href="{{ url('/register') }}">Daftar sebagai masyarakat</a></p>
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
