// SIPPM Madina — app entry point (compiled by Vite).
//
// Alpine.js is loaded via CDN in the Blade layouts, so it does not need to
// be imported/bundled here. This file only wires up Laravel Echo over
// Reverb (Fase 8) for the notification bell in layouts/dashboard.blade.php.
// `window.SIPPM_USER` is set by an inline script in that layout before this
// module runs, so channel subscriptions can be scoped to the logged-in
// user's id/roles/opd/kecamatan (see routes/channels.php for the matching
// server-side authorization).

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Global "confirm before submit" dialog for any <form data-confirm="...">
// — used across both layouts for destructive/important actions (logout,
// verify/tolak, disposisi, publikasi, nonaktifkan pengguna, dst).
// SweetAlert2 itself is loaded via CDN in the Blade layouts, not bundled
// here; this only wires the behavior against the `Swal` global.
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!(form instanceof HTMLFormElement) || !form.dataset.confirm || form.dataset.confirmed) {
        return;
    }

    e.preventDefault();

    if (typeof window.Swal === 'undefined') {
        form.submit();
        return;
    }

    window.Swal.fire({
        title: 'Konfirmasi',
        text: form.dataset.confirm,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, lanjutkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#16345c',
        cancelButtonColor: '#b23a3a',
    }).then((result) => {
        if (result.isConfirmed) {
            form.dataset.confirmed = '1';
            form.submit();
        }
    });
});

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
});

function notify(title, message) {
    window.dispatchEvent(new CustomEvent('sippm:notification', { detail: { title, message } }));
}

document.addEventListener('DOMContentLoaded', () => {
    // Public: kegiatan yang baru dipublikasikan (satu-satunya channel publik
    // by design, lihat routes/channels.php dan PRD FR-22/BR-08).
    window.Echo.channel('public-activities')
        .listen('.activity.published', (e) => notify('Kegiatan baru', e.title));

    const user = window.SIPPM_USER;
    if (!user) {
        return;
    }

    window.Echo.private(`App.Models.User.${user.id}`)
        .listen('.complaint.verified', (e) => notify('Pengaduan diverifikasi', `${e.ticket_number} — ${e.title}`))
        .listen('.complaint.rejected', (e) => notify('Pengaduan ditolak', `${e.ticket_number} — ${e.title}`))
        .listen('.complaint.resolved', (e) => notify('Jawaban resmi diterima', `${e.ticket_number} — ${e.title}`));

    if (user.roles.includes('kominfo')) {
        window.Echo.private('channel-kominfo')
            .listen('.complaint.submitted', (e) => notify('Pengaduan baru', `${e.ticket_number} — ${e.title}`))
            .listen('.complaint.handled', (e) => notify('Pengaduan ditindaklanjuti', `${e.ticket_number} — ${e.title}`));
    }

    if (user.roles.includes('opd') && user.opd_id) {
        window.Echo.private(`channel-opd.${user.opd_id}`)
            .listen('.complaint.disposed', (e) => notify('Disposisi baru', `${e.ticket_number} — ${e.title}`));
    }

    if (user.roles.includes('camat') && user.kecamatan_id) {
        window.Echo.private(`channel-camat.${user.kecamatan_id}`)
            .listen('.complaint.disposed', (e) => notify('Disposisi baru', `${e.ticket_number} — ${e.title}`));
    }
});
