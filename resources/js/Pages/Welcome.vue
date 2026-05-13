<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
});
</script>

<template>
    <Head title="Selamat Datang  " />

    <div class="smart-page">
        <!-- Noise texture overlay -->
        <div class="noise"></div>

        <!-- Header / Nav -->
        <header class="smart-header">
            <div class="brand">
                <img src="/assets/images/smart_logo.svg" alt="SMART" class="brand-logo" />
                <span class="brand-name">traking</span>
            </div>
            <div class="nav-actions" v-if="canLogin">
                <Link :href="route('login')" class="btn-ghost">Masuk</Link>
                <Link v-if="canRegister" :href="route('register')" class="btn-outline">Daftar</Link>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="smart-hero">
            <div class="hero-badge">Sistem Manajemen & Administrasi Real-Time</div>

            <h1 class="hero-title">
                Lacak Berkas<br />
                <span class="accent">Lebih Mudah.</span>
            </h1>

            <p class="hero-desc">
                Platform terpadu untuk pengelolaan berkas pertanahan —
                transparansi proses, efisiensi kerja, dan akses real-time
                untuk seluruh unit.
            </p>

            <!-- CTA Buttons -->
            <div class="hero-cta">
                <a href="/genggam-berkas" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Input Berkas Sekarang
                </a>
                <Link v-if="canLogin" :href="route('login')" class="btn-secondary">
                    Masuk ke Dashboard
                </Link>
            </div>

            <!-- Stats strip -->
            <div class="stats-strip">
                <div class="stat-item">
                    <span class="stat-label">Unit Terintegrasi</span>
                    <span class="stat-value">16+</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-label">Layanan Tersedia</span>
                    <span class="stat-value">Real-time</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-label">Status Berkas</span>
                    <span class="stat-value">Terpantau</span>
                </div>
            </div>
        </main>

        <!-- Feature cards -->
        <section class="features">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3>Input Berkas</h3>
                <p>Catat dan kelola berkas masuk dengan cepat langsung dari loket.</p>
                <a href="/genggam-berkas" class="feature-link">Mulai input →</a>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3>Pantau Progres</h3>
                <p>Lihat status berkas secara real-time di setiap tahap proses.</p>
                <Link v-if="canLogin" :href="route('login')" class="feature-link">Lihat dashboard →</Link>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3>Multi Unit</h3>
                <p>Koordinasi antar unit — loket, verifikator, pengukuran, hingga TTE.</p>
                <Link v-if="canLogin" :href="route('login')" class="feature-link">Masuk unit →</Link>
            </div>
        </section>

        <footer class="smart-footer">
            <p>© {{ new Date().getFullYear() }} SMART — Sistem Manajemen Administrasi Real-Time</p>
        </footer>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.smart-page {
    font-family: 'Sora', sans-serif;
    background: #0a0f1e;
    color: #e8eaf0;
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
}

/* Noise texture */
.noise {
    pointer-events: none;
    position: fixed;
    inset: 0;
    z-index: 0;
    opacity: 0.035;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
    background-size: 200px;
}

/* Glow blobs */
.smart-page::before {
    content: '';
    position: fixed;
    top: -200px;
    left: -100px;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(56, 189, 248, 0.12) 0%, transparent 70%);
    pointer-events: none;
    z-index: 0;
}
.smart-page::after {
    content: '';
    position: fixed;
    bottom: -150px;
    right: -100px;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
    pointer-events: none;
    z-index: 0;
}

/* Header */
.smart-header {
    position: relative;
    z-index: 10;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    backdrop-filter: blur(8px);
}

.brand {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.brand-logo {
    width: 36px;
    height: 36px;
    object-fit: contain;
}

.brand-name {
    font-family: 'DM Mono', monospace;
    font-size: 1.1rem;
    font-weight: 500;
    letter-spacing: 0.15em;
    color: #38bdf8;
}

.nav-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.btn-ghost {
    color: #94a3b8;
    text-decoration: none;
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    transition: color 0.2s;
}
.btn-ghost:hover { color: #e8eaf0; }

.btn-outline {
    color: #38bdf8;
    text-decoration: none;
    font-size: 0.9rem;
    padding: 0.5rem 1.2rem;
    border: 1px solid rgba(56, 189, 248, 0.4);
    border-radius: 6px;
    transition: all 0.2s;
}
.btn-outline:hover {
    background: rgba(56, 189, 248, 0.08);
    border-color: #38bdf8;
}

/* Hero */
.smart-hero {
    position: relative;
    z-index: 10;
    max-width: 860px;
    margin: 0 auto;
    padding: 5rem 2rem 3rem;
    text-align: center;
}

.hero-badge {
    display: inline-block;
    font-family: 'DM Mono', monospace;
    font-size: 0.72rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #38bdf8;
    background: rgba(56, 189, 248, 0.08);
    border: 1px solid rgba(56, 189, 248, 0.2);
    padding: 0.4rem 1rem;
    border-radius: 999px;
    margin-bottom: 2rem;
    animation: fadeUp 0.6s ease both;
}

.hero-title {
    font-size: clamp(2.5rem, 6vw, 4.5rem);
    font-weight: 700;
    line-height: 1.1;
    letter-spacing: -0.02em;
    color: #f1f5f9;
    margin-bottom: 1.5rem;
    animation: fadeUp 0.6s 0.1s ease both;
}

.accent {
    background: linear-gradient(135deg, #38bdf8, #818cf8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-desc {
    font-size: 1.05rem;
    color: #94a3b8;
    line-height: 1.7;
    max-width: 540px;
    margin: 0 auto 2.5rem;
    animation: fadeUp 0.6s 0.2s ease both;
}

.hero-cta {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 3.5rem;
    animation: fadeUp 0.6s 0.3s ease both;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #0ea5e9, #6366f1);
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    padding: 0.85rem 1.8rem;
    border-radius: 10px;
    transition: all 0.25s;
    box-shadow: 0 0 30px rgba(56, 189, 248, 0.25);
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 0 40px rgba(56, 189, 248, 0.4);
}

.btn-secondary {
    display: inline-flex;
    align-items: center;
    color: #cbd5e1;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.95rem;
    padding: 0.85rem 1.8rem;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.1);
    transition: all 0.25s;
    background: rgba(255,255,255,0.03);
}
.btn-secondary:hover {
    background: rgba(255,255,255,0.07);
    border-color: rgba(255,255,255,0.2);
}

/* Stats */
.stats-strip {
    display: inline-flex;
    gap: 2rem;
    align-items: center;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    padding: 1.2rem 2rem;
    animation: fadeUp 0.6s 0.4s ease both;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.2rem;
}

.stat-label {
    font-size: 0.72rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-family: 'DM Mono', monospace;
}

.stat-value {
    font-size: 1.1rem;
    font-weight: 600;
    color: #38bdf8;
}

.stat-divider {
    width: 1px;
    height: 36px;
    background: rgba(255,255,255,0.08);
}

/* Features */
.features {
    position: relative;
    z-index: 10;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.25rem;
    max-width: 960px;
    margin: 0 auto 4rem;
    padding: 0 2rem;
    animation: fadeUp 0.6s 0.5s ease both;
}

.feature-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 14px;
    padding: 1.8rem;
    transition: all 0.25s;
}
.feature-card:hover {
    background: rgba(255,255,255,0.055);
    border-color: rgba(56, 189, 248, 0.2);
    transform: translateY(-3px);
}

.feature-icon {
    width: 44px;
    height: 44px;
    background: rgba(56, 189, 248, 0.1);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #38bdf8;
    margin-bottom: 1rem;
}

.feature-card h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #f1f5f9;
    margin-bottom: 0.5rem;
}

.feature-card p {
    font-size: 0.875rem;
    color: #64748b;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.feature-link {
    font-size: 0.85rem;
    color: #38bdf8;
    text-decoration: none;
    font-weight: 500;
    transition: opacity 0.2s;
}
.feature-link:hover { opacity: 0.7; }

/* Footer */
.smart-footer {
    position: relative;
    z-index: 10;
    text-align: center;
    padding: 1.5rem 2rem;
    border-top: 1px solid rgba(255,255,255,0.06);
    font-size: 0.78rem;
    color: #334155;
    font-family: 'DM Mono', monospace;
}

/* Animations */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 600px) {
    .smart-header { padding: 1rem 1.25rem; }
    .smart-hero { padding: 3rem 1.25rem 2rem; }
    .features { padding: 0 1.25rem; }
    .stats-strip { gap: 1rem; padding: 1rem 1.25rem; flex-wrap: wrap; justify-content: center; }
    .hero-cta { flex-direction: column; align-items: center; }
}
</style>