<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
});

// ── Tracking ──────────────────────────────────────────────
const kode       = ref('');
const searching  = ref(false);
const errorMsg   = ref('');

function cariBerkas() {
    const raw = kode.value.trim().toUpperCase();
    if (!raw) { errorMsg.value = 'Masukkan kode berkas terlebih dahulu.'; return; }
    errorMsg.value = '';
    searching.value = true;
    router.visit('/lacak', { data: { kode: raw }, onFinish: () => { searching.value = false; } });
}

function handleEnter(e) {
    if (e.key === 'Enter') cariBerkas();
}

// ── Stats (fetched once on mount) ─────────────────────────
const stats = ref({ countTotal: '—', countProses: '—', countSelesaiTTE: '—', countForward: '—' });
const statsLoaded = ref(false);

onMounted(async () => {
    try {
        const res  = await fetch('/api/dashboard-stats');
        const data = await res.json();
        stats.value    = data;
        statsLoaded.value = true;
    } catch (_) { /* silent — stats stay as placeholders */ }
});
</script>

<template>
    <Head title="SMART — Sistem Manajemen Administrasi Pertanahan" />

    <div class="page">

        <!-- ── Header ── -->
        <header class="header">
            <div class="wrap header-inner">
                <div class="brand">
                    <img src="/assets/images/favicon2.svg" alt="SMART" class="brand-logo" />
                    <div>
                        <p class="brand-name">SMART</p>
                        <p class="brand-sub">Kantor Pertanahan Kab. Tangerang</p>
                    </div>
                </div>
                <nav class="nav" v-if="canLogin">
                    <Link :href="route('login')" class="nav-link">Masuk</Link>
                    
                </nav>
            </div>
        </header>

        <!-- ── Hero ── -->
        <section class="hero-section">
            <div class="wrap hero-inner">

                <div class="kementr-badge">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Kementerian ATR/BPN
                </div>

                <h1 class="hero-title">
                    Lacak Berkas<br />
                    <span class="title-accent">Pertanahan Anda</span>
                </h1>

                <p class="hero-desc">
                    Masukkan kode berkas yang diterima saat pendaftaran untuk melihat status dan progres pengurusan layanan pertanahan.
                </p>

                <!-- Search Box -->
                <div class="search-box">
                    <div class="search-field" :class="{ 'search-field--error': errorMsg }">
                        <span class="search-prefix">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                        </span>
                        <input
                            v-model="kode"
                            type="text"
                            placeholder="Contoh: BRK-20260513-20E72"
                            class="search-input"
                            @keydown="handleEnter"
                            autocomplete="off"
                            spellcheck="false"
                        />
                        <button class="search-btn" @click="cariBerkas" :disabled="searching">
                            <svg v-if="!searching" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7 7 0 105.65 5.65a7 7 0 0011 11z"/>
                            </svg>
                            <svg v-else width="16" height="16" class="spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v3m9-9h-3M3 12H6"/>
                            </svg>
                            <span class="btn-label">{{ searching ? 'Mencari…' : 'Cari Berkas' }}</span>
                        </button>
                    </div>
                    <p v-if="errorMsg" class="search-error">{{ errorMsg }}</p>
                    <p class="search-hint">Kode berkas dikirim via WhatsApp saat pendaftaran layanan di loket.</p>
                </div>

            </div>
        </section>

        <!-- ── Stats ── -->
        <section class="stats-section">
            <div class="wrap stats-grid">
                <div class="stat-card">
                    <p class="stat-num" :class="{ 'stat-num--loading': !statsLoaded }">{{ stats.countTotal }}</p>
                    <p class="stat-lbl">Total Berkas</p>
                </div>
                <div class="stat-card">
                    <p class="stat-num amber" :class="{ 'stat-num--loading': !statsLoaded }">{{ stats.countForward }}</p>
                    <p class="stat-lbl">Menunggu Proses</p>
                </div>
                <div class="stat-card">
                    <p class="stat-num blue" :class="{ 'stat-num--loading': !statsLoaded }">{{ stats.countProses }}</p>
                    <p class="stat-lbl">Sedang Diproses</p>
                </div>
                <div class="stat-card">
                    <p class="stat-num green" :class="{ 'stat-num--loading': !statsLoaded }">{{ stats.countSelesaiTTE }}</p>
                    <p class="stat-lbl">Selesai Diserahkan</p>
                </div>
            </div>
        </section>

        <!-- ── Feature Cards ── -->
        <section class="features-section">
            <div class="wrap">
                <p class="section-label">Layanan Tersedia</p>
                <div class="features-grid">

                    <div class="feat-card">
                        <div class="feat-icon icon-blue">
                            <svg width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7 7 0 105.65 5.65a7 7 0 0011 11z"/>
                            </svg>
                        </div>
                        <div class="feat-body">
                            <h3>Lacak Berkas</h3>
                            <p>Cek status berkas layanan pertanahan menggunakan kode yang diterima di loket.</p>
                        </div>
                        <a href="/lacak" class="feat-link">
                            Buka pelacak
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>

                    <div class="feat-card">
                        <div class="feat-icon icon-indigo">
                            <svg width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="feat-body">
                            <h3>Input Berkas Loket</h3>
                            <p>Catat berkas masuk dan generate kode pelacakan langsung dari meja loket pelayanan.</p>
                        </div>
                        <a href="/genggam-berkas" class="feat-link">
                            Input berkas
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>

                    <div class="feat-card">
                        <div class="feat-icon icon-teal">
                            <svg width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="feat-body">
                            <h3>Dashboard Petugas</h3>
                            <p>Akses penuh pengelolaan berkas, pembaruan status, dan koordinasi antar unit.</p>
                        </div>
                        <Link v-if="canLogin" :href="route('login')" class="feat-link">
                            Masuk dashboard
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </Link>
                    </div>

                </div>
            </div>
        </section>

        <!-- ── Footer ── -->
        <footer class="footer">
            <div class="wrap footer-inner">
                <span>© {{ new Date().getFullYear() }} SMART · Kantor Pertanahan Kabupaten Tangerang</span>
                <span class="footer-right">Kementerian ATR/BPN</span>
            </div>
        </footer>

    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.page {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #f8fafc;
    color: #1e293b;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.wrap {
    max-width: 1060px;
    margin: 0 auto;
    padding: 0 1.75rem;
    width: 100%;
}

/* Header */
.header {
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 50;
}
.header-inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 62px;
}
.brand { display: flex; align-items: center; gap: 0.7rem; }
.brand-logo { width: 32px; height: 32px; object-fit: contain; }
.brand-name {
    font-family: 'DM Mono', monospace;
    font-size: 0.95rem;
    font-weight: 500;
    color: #1d4ed8;
    letter-spacing: 0.1em;
    line-height: 1.1;
}
.brand-sub { font-size: 0.62rem; color: #94a3b8; line-height: 1; }
.nav { display: flex; align-items: center; gap: 0.5rem; }
.nav-link {
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748b;
    text-decoration: none;
    padding: 0.45rem 0.85rem;
    border-radius: 7px;
    transition: background 0.12s, color 0.12s;
}
.nav-link:hover { background: #f1f5f9; color: #1e293b; }
.btn-blue-sm {
    font-size: 0.875rem;
    font-weight: 600;
    color: #fff;
    text-decoration: none;
    background: #1d4ed8;
    padding: 0.45rem 1rem;
    border-radius: 7px;
    transition: background 0.12s;
}
.btn-blue-sm:hover { background: #1e40af; }

/* Hero */
.hero-section {
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
    padding: 3.5rem 0 3rem;
}
.hero-inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 1.4rem;
}
.kementr-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.7rem;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 0.32rem 0.8rem;
    border-radius: 999px;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}
.hero-title {
    font-size: clamp(1.9rem, 4vw, 2.85rem);
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: -0.03em;
    color: #0f172a;
}
.title-accent { color: #1d4ed8; }
.hero-desc {
    font-size: 0.975rem;
    color: #64748b;
    line-height: 1.75;
    max-width: 480px;
}

/* Search */
.search-box {
    width: 100%;
    max-width: 580px;
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
    margin-top: 0.4rem;
}
.search-field {
    display: flex;
    align-items: center;
    background: #fff;
    border: 1.5px solid #cbd5e1;
    border-radius: 12px;
    overflow: hidden;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.search-field:focus-within {
    border-color: #1d4ed8;
    box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.09);
}
.search-field--error { border-color: #ef4444; }
.search-prefix {
    padding: 0 0.85rem;
    color: #94a3b8;
    display: flex;
    align-items: center;
    flex-shrink: 0;
}
.search-input {
    flex: 1;
    border: none;
    outline: none;
    font-family: 'DM Mono', monospace;
    font-size: 0.88rem;
    color: #0f172a;
    padding: 0.9rem 0;
    background: transparent;
    letter-spacing: 0.04em;
}
.search-input::placeholder {
    color: #94a3b8;
    font-family: 'Plus Jakarta Sans', sans-serif;
    letter-spacing: 0;
    font-size: 0.875rem;
}
.search-btn {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    background: #1d4ed8;
    color: #fff;
    border: none;
    cursor: pointer;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 600;
    padding: 0 1.3rem;
    min-height: 52px;
    border-radius: 0 10px 10px 0;
    transition: background 0.12s;
    flex-shrink: 0;
}
.search-btn:hover:not(:disabled) { background: #1e40af; }
.search-btn:disabled { opacity: 0.65; cursor: not-allowed; }
.search-error { font-size: 0.775rem; color: #ef4444; font-weight: 500; padding-left: 0.2rem; }
.search-hint  { font-size: 0.74rem; color: #94a3b8; padding-left: 0.2rem; }

/* Stats */
.stats-section {
    padding: 1.75rem 0;
    border-bottom: 1px solid #e2e8f0;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.85rem;
}
.stat-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.1rem 1.4rem;
    text-align: center;
}
.stat-num {
    font-family: 'DM Mono', monospace;
    font-size: 1.55rem;
    font-weight: 500;
    color: #1e293b;
    line-height: 1;
    margin-bottom: 0.35rem;
}
.stat-num--loading { color: #e2e8f0; }
.stat-num.blue  { color: #1d4ed8; }
.stat-num.green { color: #15803d; }
.stat-num.amber { color: #b45309; }
.stat-lbl {
    font-size: 0.7rem;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    font-weight: 600;
}

/* Features */
.features-section { padding: 2.25rem 0 3rem; flex: 1; }
.section-label {
    font-family: 'DM Mono', monospace;
    font-size: 0.68rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #94a3b8;
    font-weight: 500;
    margin-bottom: 0.9rem;
}
.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.85rem;
}
.feat-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 1.4rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.feat-card:hover {
    border-color: #bfdbfe;
    box-shadow: 0 4px 14px rgba(29, 78, 216, 0.06);
}
.feat-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.icon-blue   { background: #eff6ff; color: #1d4ed8; }
.icon-indigo { background: #eef2ff; color: #4338ca; }
.icon-teal   { background: #f0fdfa; color: #0f766e; }
.feat-body { flex: 1; }
.feat-body h3 { font-size: 0.9rem; font-weight: 700; color: #0f172a; margin-bottom: 0.35rem; }
.feat-body p  { font-size: 0.78rem; color: #64748b; line-height: 1.6; }
.feat-link {
    display: inline-flex;
    align-items: center;
    gap: 0.32rem;
    font-size: 0.79rem;
    font-weight: 600;
    color: #1d4ed8;
    text-decoration: none;
    transition: gap 0.12s;
}
.feat-link:hover { gap: 0.5rem; }

/* Footer */
.footer { background: #fff; border-top: 1px solid #e2e8f0; }
.footer-inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 50px;
    font-size: 0.7rem;
    color: #94a3b8;
    font-family: 'DM Mono', monospace;
}
.footer-right { font-size: 0.68rem; letter-spacing: 0.05em; text-transform: uppercase; }

/* Spin */
@keyframes spin { to { transform: rotate(360deg); } }
.spin { animation: spin 0.75s linear infinite; }

/* Responsive */
@media (max-width: 768px) {
    .stats-grid    { grid-template-columns: repeat(2, 1fr); }
    .features-grid { grid-template-columns: 1fr; }
    .brand-sub     { display: none; }
    .hero-section  { padding: 2.5rem 0 2rem; }
}
@media (max-width: 480px) {
    .btn-label  { display: none; }
    .search-btn { min-width: 50px; padding: 0 1rem; justify-content: center; }
    .footer-inner { flex-direction: column; height: auto; padding: 0.85rem 0; gap: 0.3rem; text-align: center; }
}
</style>