@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-2" style="max-width: 1100px;">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark" style="font-family: 'Poppins', sans-serif; color: var(--sippm-navy) !important;">Pengaturan Profil</h1>
            <p class="text-muted small mb-0">Kelola informasi pribadi, foto profil, dan keamanan akun Anda.</p>
        </div>
        <div>
            <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm small">
                <i class="bi bi-clock me-1 text-warning"></i>
                Hari ini: {{ now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Kolom Kiri: Ringkasan Profil & Foto -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: var(--sippm-radius-lg); background: #ffffff;">
                <!-- Decorative Top Banner Background -->
                <div class="position-relative" style="height: 120px; background: linear-gradient(135deg, var(--sippm-navy) 0%, var(--sippm-navy-light) 100%);">
                    <div class="position-absolute w-100 h-100" style="background: radial-gradient(circle at 80% 20%, rgba(201, 162, 39, 0.15) 0%, transparent 50%);"></div>
                </div>
                
                <!-- Avatar & General Info -->
                <div class="card-body text-center position-relative pt-0" style="margin-top: -60px;">
                    <!-- Avatar Upload Area with Overlay -->
                    <div class="position-relative d-inline-block mb-3 group-avatar">
                        <div class="avatar-container border border-4 border-white shadow-lg rounded-circle bg-white overflow-hidden" style="width: 120px; height: 120px; transition: all 0.3s ease;">
                            @if($user->avatarUrl())
                                <img id="profileAvatarPreview" src="{{ $user->avatarUrl() }}" alt="Foto profil" class="w-100 h-100" style="object-fit: cover;">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                    <i id="profileAvatarPreviewIcon" class="bi bi-person-fill text-secondary" style="font-size: 64px;"></i>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Overlay edit button -->
                        <label for="avatarFileInput" class="position-absolute bottom-0 end-0 bg-warning text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px; border: 2.5px solid #fff; cursor: pointer; transition: all 0.2s ease;" title="Ubah foto">
                            <i class="bi bi-camera-fill text-dark small"></i>
                        </label>
                    </div>

                    <!-- Hidden File Input inside Form -->
                    <form id="avatarUploadForm" method="post" action="{{ url('/profil/avatar') }}" enctype="multipart/form-data" class="d-none">
                        @csrf
                        <input type="file" id="avatarFileInput" name="avatar" accept="image/png,image/jpeg" onchange="submitAvatarForm(this)">
                    </form>

                    <h4 class="fw-bold mb-1 text-dark" style="font-family: 'Poppins', sans-serif; font-size: 1.15rem;">{{ $user->name }}</h4>
                    <p class="text-muted small mb-3">{{ $user->email }}</p>
                    
                    <span class="badge px-3 py-2 mb-4 text-uppercase tracking-wider fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.05em; background-color: rgba(22, 52, 92, 0.08); color: var(--sippm-navy); border-radius: 30px;">
                        <i class="bi bi-shield-fill-check me-1"></i>
                        {{ str_replace('_',' ', $user->getRoleNames()->first() ?? '-') }}
                    </span>

                    <hr class="my-4 text-black-50 opacity-25">

                    <!-- Profile Metadata List -->
                    <div class="text-start">
                        @if($user->nik)
                            <div class="d-flex align-items-center justify-content-between mb-3 px-2">
                                <span class="text-muted small"><i class="bi bi-card-text me-2"></i>NIK</span>
                                <span class="fw-semibold small text-dark">{{ $user->nik }}</span>
                            </div>
                        @endif
                        
                        @if($user->opd)
                            <div class="d-flex align-items-center justify-content-between mb-3 px-2">
                                <span class="text-muted small"><i class="bi bi-building me-2"></i>OPD</span>
                                <span class="fw-semibold small text-dark text-end" style="max-width: 70%; word-break: break-word;">{{ $user->opd->name }}</span>
                            </div>
                        @endif

                        @if($user->kecamatan)
                            <div class="d-flex align-items-center justify-content-between mb-3 px-2">
                                <span class="text-muted small"><i class="bi bi-geo-alt me-2"></i>Kecamatan</span>
                                <span class="fw-semibold small text-dark">{{ $user->kecamatan->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Pengaturan Data & Kata Sandi -->
        <div class="col-lg-8">
            <!-- Card 1: Data Pribadi -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--sippm-radius-lg); background: #ffffff;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex align-items-center gap-2">
                    <div class="p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: rgba(22, 52, 92, 0.08) !important; color: var(--sippm-navy) !important;">
                        <i class="bi bi-person-gear fs-5"></i>
                    </div>
                    <div>
                        <h2 class="h5 mb-0 fw-bold text-dark" style="font-family: 'Poppins', sans-serif;">Data Pribadi</h2>
                        <p class="text-muted small mb-0">Ubah detail nama lengkap dan nomor telepon kontak Anda.</p>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form method="post" action="{{ url('/profil') }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" name="name" class="form-control border-start-0 ps-0" value="{{ old('name', $user->name) }}" required style="font-size: 0.95rem;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">Nomor Telepon</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="phone" class="form-control border-start-0 ps-0" value="{{ old('phone', $user->phone) }}" style="font-size: 0.95rem;">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-sippm px-4 py-2 rounded-3 fw-semibold text-white shadow-sm d-flex align-items-center gap-2" style="background-color: var(--sippm-navy); border: none;">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card 2: Ubah Kata Sandi -->
            <div class="card border-0 shadow-sm" style="border-radius: var(--sippm-radius-lg); background: #ffffff;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex align-items-center gap-2">
                    <div class="p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: rgba(178, 58, 58, 0.08) !important; color: var(--sippm-red) !important;">
                        <i class="bi bi-shield-lock fs-5"></i>
                    </div>
                    <div>
                        <h2 class="h5 mb-0 fw-bold text-dark" style="font-family: 'Poppins', sans-serif;">Ubah Kata Sandi</h2>
                        <p class="text-muted small mb-0">Perbarui kata sandi secara berkala untuk menjaga keamanan akun.</p>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form method="post" action="{{ url('/profil/password') }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-secondary">Kata Sandi Saat Ini</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
                                <input type="password" name="current_password" id="currentPassword" class="form-control border-start-0 border-end-0 ps-0" required style="font-size: 0.95rem;">
                                <button type="button" class="btn btn-light border border-start-0 text-muted px-3" onclick="togglePasswordVisibility('currentPassword', this)" aria-label="Tampilkan kata sandi">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">Kata Sandi Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" id="newPassword" class="form-control border-start-0 border-end-0 ps-0" required minlength="8" style="font-size: 0.95rem;">
                                    <button type="button" class="btn btn-light border border-start-0 text-muted px-3" onclick="togglePasswordVisibility('newPassword', this)" aria-label="Tampilkan kata sandi">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">Konfirmasi Kata Sandi Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock-check"></i></span>
                                    <input type="password" name="password_confirmation" id="newPasswordConfirm" class="form-control border-start-0 border-end-0 ps-0" required minlength="8" style="font-size: 0.95rem;">
                                    <button type="button" class="btn btn-light border border-start-0 text-muted px-3" onclick="togglePasswordVisibility('newPasswordConfirm', this)" aria-label="Tampilkan kata sandi">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn px-4 py-2 rounded-3 fw-semibold text-white shadow-sm d-flex align-items-center gap-2" style="background-color: var(--sippm-red); border: none;">
                                <i class="bi bi-key-fill"></i> Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    function submitAvatarForm(input) {
        if (input.files && input.files[0]) {
            // Show SweetAlert loading spinner
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Mengunggah...',
                    text: 'Silakan tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
            document.getElementById('avatarUploadForm').submit();
        }
    }
</script>

<style>
    /* Dynamic Hover Animations */
    .group-avatar:hover .avatar-container {
        transform: scale(1.04);
        box-shadow: 0 10px 24px rgba(22, 52, 92, 0.18) !important;
    }
    .group-avatar label {
        transition: all 0.2s ease;
    }
    .group-avatar label:hover {
        transform: scale(1.1) rotate(15deg);
        background-color: var(--sippm-gold) !important;
    }
    
    /* Form input focus animations */
    .input-group {
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }
    .input-group:focus-within {
        box-shadow: 0 0 0 3px rgba(22, 52, 92, 0.15);
    }
    .input-group:focus-within .input-group-text,
    .input-group:focus-within input,
    .input-group:focus-within button {
        border-color: var(--sippm-navy) !important;
    }
    
    /* Custom button transition effects */
    .btn-sippm:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }
    .btn[style*="background-color: var(--sippm-red)"]:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }
    
    .tracking-wider {
        letter-spacing: 0.06em;
    }
</style>
@endsection
