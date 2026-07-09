<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — SIPPM Madina</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-madina.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.11/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
    <script>
        window.SIPPM_USER = {
            id: {{ auth()->id() }},
            roles: @json(auth()->user()->getRoleNames()),
            opd_id: @json(auth()->user()->opd_id),
            kecamatan_id: @json(auth()->user()->kecamatan_id),
        };
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: var(--sippm-cream); }
        h1, h2, h3, h4, .brand-text { font-family: 'Poppins', sans-serif; }

        .sippm-sidebar {
            width: 260px; min-width: 260px; flex-shrink: 0; min-height: 100vh; background-color: var(--sippm-navy);
            box-shadow: var(--sippm-shadow-raised); position: sticky; top: 0;
        }
        .sippm-sidebar .brand-text { color: #fff; }
        .sippm-sidebar .nav-link { color: rgba(255,255,255,.75); border-radius: var(--sippm-radius-sm); margin: 2px 10px; }
        .sippm-sidebar .nav-link.active, .sippm-sidebar .nav-link:hover { background-color: rgba(255,255,255,.12); color: #fff; }
        .sippm-sidebar .nav-header { color: var(--sippm-gold); font-size: .72rem; text-transform: uppercase; letter-spacing: .08em; padding: .5rem 1rem 0.25rem; }

        .sippm-topbar { background-color: #fff; box-shadow: var(--sippm-shadow-soft); border-top: 3px solid var(--sippm-gold); border-radius: 0 0 var(--sippm-radius-lg) var(--sippm-radius-lg); }
        .btn-sippm { background-color: var(--sippm-navy); color: #fff; border-radius: var(--sippm-radius-sm); box-shadow: var(--sippm-shadow-soft); border: none; }
        .btn-sippm:hover { background-color: var(--sippm-navy-light); color: #fff; }
        [x-cloak] { display: none !important; }

        /* Custom Notification Dropdown styling */
        .notification-dropdown {
            width: 360px;
            border-radius: var(--sippm-radius-lg);
            border: 1px solid var(--sippm-border);
            box-shadow: var(--sippm-shadow-raised);
            overflow: hidden;
            background-color: #fff;
        }
        .notification-header {
            background-color: var(--sippm-navy);
            color: #fff;
            padding: 14px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--sippm-gold);
        }
        .notification-header h6 {
            margin: 0;
            font-weight: 700;
            font-size: 0.95rem;
        }
        .notification-list {
            max-height: 320px;
            overflow-y: auto;
            background-color: var(--sippm-cream);
        }
        .notification-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid rgba(22, 52, 92, 0.08);
            text-decoration: none;
            color: var(--sippm-text);
            transition: all 0.2s ease;
            position: relative;
        }
        .notification-item:hover {
            background-color: rgba(22, 52, 92, 0.04);
            color: var(--sippm-navy);
        }
        .notification-item.unread {
            background-color: rgba(22, 52, 92, 0.06);
        }
        .notification-item.unread::before {
            content: '';
            position: absolute;
            left: 6px;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 8px;
            background-color: var(--sippm-gold);
            border-radius: 50%;
            box-shadow: 0 0 4px var(--sippm-gold);
        }
        .notification-icon-wrapper {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            box-shadow: var(--sippm-shadow-soft);
        }
        
        /* Icon types styling */
        .notification-icon-info {
            background-color: rgba(37, 99, 235, 0.1);
            color: #2563eb;
        }
        .notification-icon-success {
            background-color: rgba(46, 125, 79, 0.1);
            color: var(--sippm-green);
        }
        .notification-icon-warning {
            background-color: rgba(217, 142, 4, 0.1);
            color: var(--sippm-amber);
        }
        .notification-icon-danger {
            background-color: rgba(178, 58, 58, 0.1);
            color: var(--sippm-red);
        }
        
        .notification-content {
            flex-grow: 1;
            min-width: 0;
        }
        .notification-title {
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 2px;
            line-height: 1.3;
        }
        .notification-item.unread .notification-title {
            color: #000;
            font-weight: 700;
        }
        .notification-message {
            font-size: 0.78rem;
            color: #555;
            margin-bottom: 4px;
            white-space: normal;
            word-wrap: break-word;
            line-height: 1.4;
        }
        .notification-time {
            font-size: 0.7rem;
            color: #888;
        }
        .notification-footer {
            padding: 10px 16px;
            background-color: #fff;
            border-top: 1px solid rgba(22, 52, 92, 0.08);
            text-align: center;
        }
        .notification-footer .text-sippm {
            color: var(--sippm-navy);
            font-size: 0.8rem;
            transition: color 0.2s;
            font-weight: 600;
        }
        .notification-footer .text-sippm:hover {
            color: var(--sippm-gold);
        }
        .sippm-toast-raised {
            box-shadow: var(--sippm-shadow-raised) !important;
            border: 1px solid var(--sippm-border) !important;
            border-radius: var(--sippm-radius-sm) !important;
        }
    </style>
    @stack('styles')
</head>
<body>
@php $user = auth()->user(); @endphp
<div class="d-flex">
    <aside class="sippm-sidebar d-none d-lg-flex flex-column py-3">
        <a href="{{ url('/') }}" class="brand-text text-decoration-none fw-bold fs-5 px-3 mb-3 d-flex align-items-center gap-2">
            <img src="{{ asset('images/logo-madina.png') }}" alt="Logo" style="height:32px; width:auto;">
            SIPPM Madina
        </a>
        @include('dashboard.partials.sidebar-nav')
        <div class="px-3 pt-2 border-top border-white border-opacity-25">
            <form method="post" action="{{ url('/logout') }}" data-confirm="Apakah Anda yakin ingin keluar?">
                @csrf
                <button class="btn btn-sm btn-outline-light w-100" type="submit"><i class="bi bi-box-arrow-left me-1"></i>Keluar</button>
            </form>
        </div>
    </aside>

    <!-- Mobile sidebar (Bootstrap 5 offcanvas) -->
    <div class="offcanvas offcanvas-start d-lg-none sippm-sidebar" tabindex="-1" id="sidebarOffcanvas">
        <div class="offcanvas-header">
            <a href="{{ url('/') }}" class="brand-text text-decoration-none fw-bold fs-5 d-flex align-items-center gap-2">
                <img src="{{ asset('images/logo-madina.png') }}" alt="Logo" style="height:28px; width:auto;">
                SIPPM Madina
            </a>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            @include('dashboard.partials.sidebar-nav')
            <div class="px-1 pt-2 border-top border-white border-opacity-25">
                <form method="post" action="{{ url('/logout') }}" data-confirm="Apakah Anda yakin ingin keluar?">
                    @csrf
                    <button class="btn btn-sm btn-outline-light w-100" type="submit"><i class="bi bi-box-arrow-left me-1"></i>Keluar</button>
                </form>
            </div>
        </div>
    </div>

    <div class="flex-grow-1">
        <header class="sippm-topbar d-flex align-items-center justify-content-between px-4 py-3 mb-4">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-light d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="h5 mb-0">{{ $title ?? 'Dashboard' }}</h1>
            </div>
            <div class="d-flex align-items-center gap-3" x-data="notificationBell()" x-init="init()">
                <div class="position-relative">
                    <button class="btn btn-light position-relative rounded-circle shadow-sm p-2" @click="open = !open" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                        <i class="bi bi-bell-fill text-secondary fs-5"></i>
                        <span x-show="unread > 0" x-text="unread" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7rem; padding: 0.35em 0.6em;"></span>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak class="dropdown-menu show p-0 border-0 shadow-lg notification-dropdown" style="position:absolute; right:0; top: 110%; z-index: 1050;">
                        <!-- Header -->
                        <div class="notification-header">
                            <div class="d-flex align-items-center gap-2">
                                <h6>Notifikasi</h6>
                                <span x-show="unread > 0" class="badge bg-danger rounded-pill small" x-text="unread + ' Baru'"></span>
                            </div>
                            <button x-show="unread > 0" @click="markAllAsRead()" class="btn btn-link btn-sm text-decoration-none p-0 text-white opacity-75 hover-opacity-100 small" style="font-size: 0.75rem;">
                                <i class="bi bi-check-all me-1"></i>Tandai semua dibaca
                            </button>
                        </div>
                        
                        <!-- List -->
                        <div class="notification-list">
                            <template x-if="items.length === 0">
                                <div class="text-center text-muted p-4">
                                    <i class="bi bi-bell-slash fs-2 mb-2 d-block text-secondary opacity-50"></i>
                                    <span class="small">Tidak ada notifikasi.</span>
                                </div>
                            </template>
                            <template x-for="item in items" :key="item.id">
                                <a href="#" @click.prevent="handleClick(item)" class="notification-item" :class="!item.is_read ? 'unread' : ''">
                                    <div class="notification-icon-wrapper" :class="getIconBgClass(item.type, item.title)">
                                        <i class="bi" :class="getIconClass(item.type, item.title)"></i>
                                    </div>
                                    <div class="notification-content">
                                        <div class="notification-title" x-text="item.title"></div>
                                        <div class="notification-message text-muted" x-text="item.message"></div>
                                        <div class="notification-time d-flex align-items-center gap-1">
                                            <i class="bi bi-clock-history" style="font-size: 0.7rem;"></i>
                                            <span x-text="timeAgo(item.created_at)"></span>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>
                        
                        <!-- Footer -->
                        <div class="notification-footer">
                            <a href="{{ url('/dashboard/complaints') }}" class="text-decoration-none text-sippm">Lihat Semua Pengaduan</a>
                        </div>
                    </div>
                </div>
                <div class="text-end small d-none d-sm-block">
                    <div class="fw-semibold">{{ $user->name }}</div>
                    <div class="text-muted">{{ ucfirst(str_replace('_',' ', $user->getRoleNames()->first() ?? '')) }}</div>
                </div>
            </div>
        </header>

        <div class="px-4 pb-5">
            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
<script>
    /**
     * Shared rich-text-editor wiring for every "Deskripsi" field
     * (pengaduan, kegiatan, hasil penanganan). Turns a hidden
     * <textarea name="..."> into a Quill WYSIWYG editor and keeps the
     * textarea's value (HTML) in sync so normal form POST/validation
     * (Form Request `string`/`required` rules) keeps working unchanged.
     */
    function sippmInitRichText(editorId, textareaSelector) {
        if (typeof Quill === 'undefined') return null;

        const textarea = document.querySelector(textareaSelector);
        const container = document.getElementById(editorId);
        if (!textarea || !container) return null;

        const quill = new Quill(container, {
            theme: 'snow',
            placeholder: textarea.getAttribute('placeholder') || '',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['clean'],
                ],
            },
        });

        if (textarea.value) {
            quill.root.innerHTML = textarea.value;
        }

        quill.on('text-change', () => {
            textarea.value = quill.getText().trim().length ? quill.root.innerHTML : '';
        });

        const form = textarea.closest('form');
        if (form && textarea.hasAttribute('required')) {
            form.addEventListener('submit', (e) => {
                if (quill.getText().trim().length === 0) {
                    e.preventDefault();
                    container.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }

        return quill;
    }
</script>
<script>
    function notificationBell() {
        return {
            open: false,
            items: [],
            unread: 0,
            init() {
                this.load();
                
                // Live push via Reverb (resources/js/app.js)
                window.addEventListener('sippm:notification', (e) => {
                    const now = new Date();
                    const newItem = { 
                        id: 'live-' + Date.now(), 
                        title: e.detail.title, 
                        message: e.detail.message, 
                        is_read: false,
                        type: 'LiveNotification',
                        created_at: now.toISOString()
                    };
                    
                    this.items.unshift(newItem);
                    this.unread++;

                    // Visual Alert / Swal Toast for real-time notification
                    if (window.Swal) {
                        window.Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: this.getToastIcon(e.detail.title),
                            title: e.detail.title,
                            text: e.detail.message,
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true,
                            showCloseButton: true,
                            customClass: {
                                popup: 'sippm-toast-raised'
                            }
                        });
                    }
                });
            },
            async load() {
                try {
                    const res = await fetch('{{ url('/dashboard/notifications') }}', {
                        headers: { 'Accept': 'application/json' },
                    });
                    if (!res.ok) return;
                    const json = await res.json();
                    this.items = json.data ?? [];
                    // Server-side count (seluruh baris belum dibaca di DB),
                    // bukan dihitung dari `items` yang cuma 20 item terbaru —
                    // fallback ke hitungan lokal kalau field belum ada.
                    this.unread = json.unread_count ?? this.items.filter(i => !i.is_read).length;
                } catch (e) { /* diamkan bila gagal memuat */ }
            },
            async markRead(item) {
                if (item.is_read) return;
                item.is_read = true;
                this.unread = Math.max(0, this.unread - 1);
                
                // Don't fetch for temporary live items without integer DB ID
                if (typeof item.id === 'string' && item.id.startsWith('live-')) {
                    return;
                }
                
                try {
                    await fetch(`{{ url('/dashboard/notifications') }}/${item.id}/read`, {
                        method: 'POST',
                        headers: { 
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? '' 
                        },
                    });
                } catch (e) {}
            },
            async markAllAsRead() {
                this.items.forEach(i => i.is_read = true);
                this.unread = 0;
                try {
                    await fetch('{{ url('/dashboard/notifications/read-all') }}', {
                        method: 'POST',
                        headers: { 
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? '' 
                        },
                    });
                } catch (e) {}
            },
            async handleClick(item) {
                await this.markRead(item);
                
                const canViewComplaints = window.SIPPM_USER.roles.some(r => ['kominfo', 'opd', 'camat'].includes(r));
                if (!canViewComplaints) {
                    // Bupati / Wakil Bupati / Sekda don't have /complaints access
                    return;
                }

                // Extract ticket number from either message or title
                const textToSearch = `${item.title} ${item.message}`;
                const ticketMatch = textToSearch.match(/PGD-\d{4}-\d{6}/);
                
                if (ticketMatch) {
                    window.location.href = `{{ url('/dashboard/complaints') }}?search=${ticketMatch[0]}`;
                } else {
                    window.location.href = `{{ url('/dashboard/complaints') }}`;
                }
            },
            getToastIcon(title) {
                const low = (title || '').toLowerCase();
                if (low.includes('tolak') || low.includes('ditolak')) return 'error';
                if (low.includes('selesai') || low.includes('verifikasi')) return 'success';
                if (low.includes('tindak') || low.includes('disposisi')) return 'warning';
                return 'info';
            },
            getIconClass(type, title) {
                const lowType = (type || '').toLowerCase();
                const lowTitle = (title || '').toLowerCase();
                
                if (lowType.includes('submitted') || lowTitle.includes('masuk')) {
                    return 'bi-file-earmark-text';
                }
                if (lowType.includes('verified') || lowTitle.includes('verifikasi')) {
                    if (lowTitle.includes('tolak') || lowTitle.includes('ditolak')) {
                        return 'bi-shield-x';
                    }
                    return 'bi-shield-check';
                }
                if (lowType.includes('disposed') || lowTitle.includes('disposisi')) {
                    return 'bi-arrow-right-circle';
                }
                if (lowType.includes('handled') || lowTitle.includes('tindaklanjuti')) {
                    return 'bi-wrench';
                }
                if (lowType.includes('resolved') || lowTitle.includes('selesai')) {
                    return 'bi-check-circle-fill';
                }
                if (lowType.includes('published') || lowTitle.includes('publikasi')) {
                    return 'bi-megaphone';
                }
                return 'bi-bell-fill';
            },
            getIconBgClass(type, title) {
                const lowType = (type || '').toLowerCase();
                const lowTitle = (title || '').toLowerCase();
                
                if (lowType.includes('submitted') || lowTitle.includes('masuk')) {
                    return 'notification-icon-info';
                }
                if (lowType.includes('verified') || lowTitle.includes('verifikasi')) {
                    if (lowTitle.includes('tolak') || lowTitle.includes('ditolak')) {
                        return 'notification-icon-danger';
                    }
                    return 'notification-icon-success';
                }
                if (lowType.includes('disposed') || lowTitle.includes('disposisi')) {
                    return 'notification-icon-info';
                }
                if (lowType.includes('handled') || lowTitle.includes('tindaklanjuti')) {
                    return 'notification-icon-warning';
                }
                if (lowType.includes('resolved') || lowTitle.includes('selesai')) {
                    return 'notification-icon-success';
                }
                if (lowType.includes('published') || lowTitle.includes('publikasi')) {
                    return 'notification-icon-warning';
                }
                return 'notification-icon-info';
            },
            timeAgo(dateStr) {
                if (!dateStr) return '';
                const date = new Date(dateStr);
                const now = new Date();
                const diffMs = now - date;
                const diffMins = Math.floor(diffMs / 60000);
                const diffHours = Math.floor(diffMins / 60);
                const diffDays = Math.floor(diffHours / 24);

                if (diffMins < 1) return 'Baru saja';
                if (diffMins < 60) return `${diffMins} m yang lalu`;
                if (diffHours < 24) return `${diffHours} jam yang lalu`;
                if (diffDays === 1) return 'Kemarin';
                if (diffDays < 7) return `${diffDays} hari yang lalu`;
                
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            }
        };
    }

    @if(session('status'))
        Swal.fire({
            icon: 'success',
            title: @json(session('status')),
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Periksa kembali data Anda',
            html: @json('<ul class="text-start mb-0 ps-3">'.collect($errors->all())->map(fn ($e) => '<li>'.e($e).'</li>')->implode('').'</ul>'),
        });
    @endif
</script>
@stack('scripts')
</body>
</html>
