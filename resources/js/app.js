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

// Slim top-of-page loading bar (#sippmPageLoader, markup + static CSS in
// layouts/dashboard.blade.php / resources/css/app.css) shown while a
// full-page navigation is in flight. This app is server-rendered (no SPA
// router) so the JS context is fully torn down on every navigation — a
// sessionStorage flag is the only way to know "the page the user is
// currently looking at was requested via a click/submit in this app" and
// should therefore animate to completion + fade on load, as opposed to a
// fresh/direct load (typed URL, bookmark, login redirect) which should
// stay invisible instead of flashing for no reason.
const PAGE_LOADER_FLAG = 'sippm:page-loading';

function showPageLoader() {
    const bar = document.getElementById('sippmPageLoader');
    if (!bar) {
        return;
    }

    sessionStorage.setItem(PAGE_LOADER_FLAG, '1');

    bar.style.transition = 'none';
    bar.style.width = '0%';
    bar.style.opacity = '1';
    void bar.offsetWidth; // force reflow so the reset above applies before animating
    bar.style.transition = 'width 4s cubic-bezier(0.1, 0.7, 1, 0.1), opacity 0.2s ease';
    requestAnimationFrame(() => {
        bar.style.width = '90%';
    });
}

// Complete + fade the bar on the destination page, but ONLY if this load
// was actually triggered by a click/submit in-app (see flag above) —
// otherwise leave it untouched (invisible, per its default CSS).
document.addEventListener('DOMContentLoaded', () => {
    const bar = document.getElementById('sippmPageLoader');
    if (!bar || sessionStorage.getItem(PAGE_LOADER_FLAG) !== '1') {
        return;
    }

    sessionStorage.removeItem(PAGE_LOADER_FLAG);
    bar.style.transition = 'width 0.25s ease';
    bar.style.width = '100%';
    setTimeout(() => {
        bar.style.transition = 'opacity 0.3s ease';
        bar.style.opacity = '0';
        setTimeout(() => {
            bar.style.width = '0%';
        }, 300);
    }, 150);
});

// Page restored from bfcache (browser Back/Forward) — navigation already
// finished instantly, so any in-flight bar state is stale; reset it.
window.addEventListener('pageshow', (e) => {
    if (!e.persisted) {
        return;
    }

    sessionStorage.removeItem(PAGE_LOADER_FLAG);
    const bar = document.getElementById('sippmPageLoader');
    if (bar) {
        bar.style.transition = 'none';
        bar.style.width = '0%';
        bar.style.opacity = '0';
    }
});

// Show the bar the moment a normal in-app link is clicked (same-origin,
// not a new tab/download/anchor/mailto/tel, no modifier-key "open in new
// tab" click) — covers sidebar nav, topbar dropdown, pagination, every
// plain <a> across the panel.
document.addEventListener('click', (e) => {
    if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
        return;
    }

    const link = e.target.closest('a[href]');
    if (!link || !document.getElementById('sippmPageLoader')) {
        return;
    }

    const href = link.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) {
        return;
    }

    if (link.target === '_blank' || link.hasAttribute('download')) {
        return;
    }

    if (new URL(link.href, window.location.href).origin !== window.location.origin) {
        return;
    }

    showPageLoader();
});

// Show the bar on plain form submits (filters, login, create/edit forms,
// dst). Forms with `data-confirm` are deliberately excluded here — those
// pause for a SweetAlert2 dialog first, so the loader is triggered from
// inside that confirm handler instead, right before the real submit.
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!(form instanceof HTMLFormElement) || form.dataset.confirm) {
        return;
    }

    showPageLoader();
});

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
            showPageLoader();
            form.submit();
        }
    });
});

// Generic password show/hide toggle for any `<button data-toggle-password="#targetId">`
// button placed next to a password `<input>` (see auth/login.blade.php and
// auth/register.blade.php). Toggles the input's type and swaps the
// Bootstrap Icons eye/eye-slash icon inside the button.
document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-toggle-password]');
    if (!btn) {
        return;
    }

    const target = document.querySelector(btn.dataset.togglePassword);
    if (!target) {
        return;
    }

    const showing = target.type === 'text';
    target.type = showing ? 'password' : 'text';

    const icon = btn.querySelector('i');
    if (icon) {
        icon.classList.toggle('bi-eye', showing);
        icon.classList.toggle('bi-eye-slash', !showing);
    }
});

// Live client-side preview for any `<input type="file" data-avatar-preview="#targetId">`
// (see profile/show.blade.php) — swaps the target <img>'s src to the
// locally-selected file via an object URL, before the form is ever
// submitted. Purely cosmetic; the actual upload/validation still happens
// server-side in ProfileController::updateAvatar().
document.addEventListener('change', (e) => {
    const input = e.target.closest('[data-avatar-preview]');
    if (!(input instanceof HTMLInputElement) || !input.files || !input.files[0]) {
        return;
    }

    const target = document.querySelector(input.dataset.avatarPreview);
    if (!target) {
        return;
    }

    target.src = URL.createObjectURL(input.files[0]);
});

// Scroll/load-reveal for elements marked `.reveal` (currently only used on
// the public landing page, see public/home.blade.php). Adds `.is-visible`
// once an element enters the viewport; falls back to revealing everything
// immediately when the user prefers reduced motion or the browser lacks
// IntersectionObserver, and force-reveals everything after 2s as a safety
// net so content is never stuck invisible if this script errors elsewhere.
document.addEventListener('DOMContentLoaded', () => {
    const targets = document.querySelectorAll('.reveal');
    if (!targets.length) {
        return;
    }

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion || !('IntersectionObserver' in window)) {
        targets.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

    targets.forEach((el) => observer.observe(el));

    setTimeout(() => targets.forEach((el) => el.classList.add('is-visible')), 2000);
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
