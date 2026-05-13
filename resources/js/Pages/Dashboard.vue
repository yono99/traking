<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, computed } from "vue";

// ── State ──────────────────────────────────────────────────
const loading       = ref(true);
const loadingRecent = ref(true);

const stats = ref({
    countTotal: 0,
    countProses: 0,
    countSelesaiTTE: 0,
    countForward: 0,
    breakdown: {},
});

const recentServices = ref([]);

// ── Fetch dashboard stats ──────────────────────────────────
async function fetchStats() {
    try {
        const res  = await fetch("/api/dashboard-stats");
        const data = await res.json();
        stats.value = data;
    } catch (e) {
        console.error("Stats fetch error:", e);
    } finally {
        loading.value = false;
    }
}

// ── Fetch recent berkas ────────────────────────────────────
async function fetchRecent() {
    try {
        const res  = await fetch("/service/recent");
        const data = await res.json();
        recentServices.value = data.data ?? [];
    } catch (e) {
        console.error("Recent fetch error:", e);
    } finally {
        loadingRecent.value = false;
    }
}

onMounted(() => { fetchStats(); fetchRecent(); });

// ── Breakdown sorted by count ──────────────────────────────
const breakdownRows = computed(() =>
    Object.entries(stats.value.breakdown ?? {})
        .map(([status, total]) => ({ status, total }))
        .sort((a, b) => b.total - a.total)
);

const breakdownMax = computed(() =>
    Math.max(...breakdownRows.value.map(r => r.total), 1)
);

// ── Helpers ────────────────────────────────────────────────
const today = new Intl.DateTimeFormat("id-ID", {
    weekday: "long", day: "numeric", month: "long", year: "numeric",
}).format(new Date());

function statusClass(status = "") {
    const s = status.toUpperCase();
    if (s.startsWith("FORWARD"))   return "badge-amber";
    if (s.startsWith("PROSES"))    return "badge-blue";
    if (s.startsWith("SELESAI"))   return "badge-green";
    return "badge-gray";
}

function statusShort(status = "") {
    return status
        .replace("FORWARD ", "FWD ")
        .replace("PROSES ", "")
        .replace(" PENYERAHAN", " SERAH")
        .replace("VALIDASI BUKU TANAH", "VAL.BT")
        .replace("VALIDASI BIDANG", "VAL.BID")
        .replace("BUKU TANAH", "BK.TANAH")
        .replace("DISERAHKAN", "✓")
        .trim();
}

function barColor(status = "") {
    const s = status.toUpperCase();
    if (s.startsWith("FORWARD"))  return "#f59e0b";
    if (s.startsWith("PROSES"))   return "#3b82f6";
    if (s.startsWith("SELESAI"))  return "#22c55e";
    return "#94a3b8";
}
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="dh-header">
                <div>
                    <h2 class="dh-title">Dashboard</h2>
                    <p class="dh-date">{{ today }}</p>
                </div>
                <div class="dh-actions">
                    <a href="/total-proses" class="dh-btn">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Data Proses
                    </a>
                    <a href="/total-proses-tte" class="dh-btn dh-btn--blue">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Data Selesai
                    </a>
                </div>
            </div>
        </template>

        <div class="db-body">

            <!-- ── Stat Cards ── -->
            <div class="stat-grid">

                <div class="stat-card">
                    <div class="stat-card__icon icon-slate">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <p class="stat-card__label">Total Berkas</p>
                        <p class="stat-card__value" :class="{ 'val-loading': loading }">
                            {{ loading ? '—' : stats.countTotal }}
                        </p>
                        <p class="stat-card__sub">Seluruh layanan</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card__icon icon-amber">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <p class="stat-card__label">Menunggu Proses</p>
                        <p class="stat-card__value amber" :class="{ 'val-loading': loading }">
                            {{ loading ? '—' : stats.countForward }}
                        </p>
                        <p class="stat-card__sub">Forward ke unit</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card__icon icon-blue">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <p class="stat-card__label">Sedang Diproses</p>
                        <p class="stat-card__value blue" :class="{ 'val-loading': loading }">
                            {{ loading ? '—' : stats.countProses }}
                        </p>
                        <p class="stat-card__sub">Aktif di unit</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card__icon icon-green">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <div class="stat-card__body">
                        <p class="stat-card__label">Selesai Diserahkan</p>
                        <p class="stat-card__value green" :class="{ 'val-loading': loading }">
                            {{ loading ? '—' : stats.countSelesaiTTE }}
                        </p>
                        <p class="stat-card__sub">Produk diterima</p>
                    </div>
                </div>

            </div>

            <!-- ── Main Content Row ── -->
            <div class="main-row">

                <!-- Left: Recent Berkas Table -->
                <div class="panel panel--table">
                    <div class="panel__head">
                        <div>
                            <p class="panel__title">Berkas Terbaru</p>
                            <p class="panel__sub">10 layanan terakhir masuk</p>
                        </div>
                        <a href="/berkas" class="panel__link">
                            Lihat semua
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>

                    <!-- Loading skeleton -->
                    <template v-if="loadingRecent">
                        <div class="skeleton-row" v-for="i in 6" :key="i">
                            <div class="sk sk-lg"></div>
                            <div class="sk sk-md"></div>
                            <div class="sk sk-sm"></div>
                        </div>
                    </template>

                    <!-- Empty -->
                    <div v-else-if="recentServices.length === 0" class="empty-state">
                        <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>Belum ada berkas masuk</p>
                    </div>

                    <!-- Table -->
                    <div v-else class="tbl-wrap">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Pemohon</th>
                                    <th>Hak / Nomor</th>
                                    <th>Lokasi</th>
                                    <th>Tgl Masuk</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="svc in recentServices" :key="svc.id" class="tbl-row">
                                    <td class="td-name">{{ svc.name }}</td>
                                    <td class="td-mono">{{ svc.hak }}</td>
                                    <td class="td-lokasi">{{ svc.lokasi }}</td>
                                    <td class="td-mono td-date">{{ svc.created }}</td>
                                    <td>
                                        <span class="badge" :class="statusClass(svc.status)">
                                            {{ statusShort(svc.status) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right: Breakdown per Status -->
                <div class="panel panel--breakdown">
                    <div class="panel__head">
                        <div>
                            <p class="panel__title">Distribusi Status</p>
                            <p class="panel__sub">Jumlah berkas per tahap</p>
                        </div>
                    </div>

                    <template v-if="loading">
                        <div class="skeleton-row" v-for="i in 8" :key="i">
                            <div class="sk sk-full"></div>
                        </div>
                    </template>

                    <div v-else-if="breakdownRows.length === 0" class="empty-state">
                        <p>Tidak ada data</p>
                    </div>

                    <ul v-else class="breakdown-list">
                        <li v-for="row in breakdownRows" :key="row.status" class="breakdown-item">
                            <div class="bd-top">
                                <span class="bd-label">{{ row.status }}</span>
                                <span class="bd-count" :style="{ color: barColor(row.status) }">{{ row.total }}</span>
                            </div>
                            <div class="bd-bar-track">
                                <div
                                    class="bd-bar-fill"
                                    :style="{
                                        width: ((row.total / breakdownMax) * 100).toFixed(1) + '%',
                                        background: barColor(row.status)
                                    }"
                                ></div>
                            </div>
                        </li>
                    </ul>

                    <!-- Legend -->
                    <div class="bd-legend">
                        <span class="legend-item"><i class="leg-dot" style="background:#f59e0b"></i>Forward</span>
                        <span class="legend-item"><i class="leg-dot" style="background:#3b82f6"></i>Proses</span>
                        <span class="legend-item"><i class="leg-dot" style="background:#22c55e"></i>Selesai</span>
                    </div>
                </div>

            </div>

            <!-- ── Quick Links ── -->
            <div class="quick-grid">
                <a href="/genggam-berkas" class="quick-card">
                    <div class="quick-icon" style="background:#eff6ff;color:#1d4ed8">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="quick-title">Input Berkas</p>
                        <p class="quick-sub">Catat berkas baru dari loket</p>
                    </div>
                    <svg class="quick-arrow" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>

                <a href="/inventory" class="quick-card">
                    <div class="quick-icon" style="background:#eef2ff;color:#4338ca">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="quick-title">Inventori</p>
                        <p class="quick-sub">Kelola berkas masuk unit</p>
                    </div>
                    <svg class="quick-arrow" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>

                <a href="/laporan" class="quick-card">
                    <div class="quick-icon" style="background:#f0fdf4;color:#15803d">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="quick-title">Laporan</p>
                        <p class="quick-sub">Grafik &amp; export Excel</p>
                    </div>
                    <svg class="quick-arrow" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>

                <a href="/lacak" class="quick-card">
                    <div class="quick-icon" style="background:#fff7ed;color:#c2410c">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7 7 0 105.65 5.65a7 7 0 0011 11z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="quick-title">Lacak Berkas</p>
                        <p class="quick-sub">Halaman pelacakan publik</p>
                    </div>
                    <svg class="quick-arrow" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

/* ── Wrapper ── */
.db-body {
    padding: 1.5rem 1.75rem 2.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

/* ── Page Header ── */
.dh-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}
.dh-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
}
.dh-date {
    font-size: 0.78rem;
    color: #94a3b8;
    margin-top: 0.15rem;
}
.dh-actions { display: flex; gap: 0.5rem; }
.dh-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    color: #475569;
    background: #fff;
    border: 1px solid #e2e8f0;
    padding: 0.45rem 0.9rem;
    border-radius: 8px;
    transition: border-color 0.12s, background 0.12s;
}
.dh-btn:hover { background: #f8fafc; border-color: #cbd5e1; }
.dh-btn--blue { background: #1d4ed8; color: #fff; border-color: #1d4ed8; }
.dh-btn--blue:hover { background: #1e40af; border-color: #1e40af; }

/* ── Stat Grid ── */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}
.stat-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 1.25rem 1.35rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    transition: box-shadow 0.15s;
}
.stat-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.06); }

.stat-card__icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.icon-slate { background: #f1f5f9; color: #475569; }
.icon-amber { background: #fffbeb; color: #b45309; }
.icon-blue  { background: #eff6ff; color: #1d4ed8; }
.icon-green { background: #f0fdf4; color: #15803d; }

.stat-card__label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.3rem;
}
.stat-card__value {
    font-family: 'DM Mono', monospace;
    font-size: 1.85rem;
    font-weight: 500;
    color: #0f172a;
    line-height: 1;
    margin-bottom: 0.3rem;
}
.stat-card__value.amber { color: #b45309; }
.stat-card__value.blue  { color: #1d4ed8; }
.stat-card__value.green { color: #15803d; }
.stat-card__value.val-loading { color: #e2e8f0; }

.stat-card__sub {
    font-size: 0.72rem;
    color: #94a3b8;
}

/* ── Main Row ── */
.main-row {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 1rem;
    align-items: start;
}

/* ── Panel ── */
.panel {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
}
.panel__head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 1.1rem 1.35rem 0.9rem;
    border-bottom: 1px solid #f1f5f9;
}
.panel__title {
    font-size: 0.9rem;
    font-weight: 700;
    color: #0f172a;
}
.panel__sub {
    font-size: 0.72rem;
    color: #94a3b8;
    margin-top: 0.15rem;
}
.panel__link {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #1d4ed8;
    text-decoration: none;
    white-space: nowrap;
    margin-top: 0.1rem;
    transition: gap 0.12s;
}
.panel__link:hover { gap: 0.45rem; }

/* Skeleton */
.skeleton-row {
    display: flex;
    gap: 0.75rem;
    padding: 0.85rem 1.35rem;
    border-bottom: 1px solid #f8fafc;
}
.sk {
    height: 12px;
    background: #f1f5f9;
    border-radius: 4px;
    animation: shimmer 1.4s ease-in-out infinite;
}
.sk-lg   { width: 38%; }
.sk-md   { width: 25%; }
.sk-sm   { width: 15%; }
.sk-full { width: 100%; }
@keyframes shimmer { 0%,100%{opacity:1} 50%{opacity:0.45} }

/* Empty */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.6rem;
    padding: 2.5rem 1rem;
    color: #94a3b8;
    font-size: 0.82rem;
}

/* Table */
.tbl-wrap { overflow-x: auto; }
.tbl {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;
}
.tbl thead th {
    padding: 0.65rem 1.1rem;
    text-align: left;
    font-size: 0.68rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}
.tbl-row { border-bottom: 1px solid #f1f5f9; transition: background 0.1s; }
.tbl-row:last-child { border-bottom: none; }
.tbl-row:hover { background: #f8fafc; }
.tbl td { padding: 0.75rem 1.1rem; color: #334155; vertical-align: middle; }
.td-name   { font-weight: 600; color: #0f172a; max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.td-mono   { font-family: 'DM Mono', monospace; font-size: 0.78rem; color: #64748b; }
.td-lokasi { color: #64748b; font-size: 0.78rem; max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.td-date   { white-space: nowrap; }

/* Badge */
.badge {
    display: inline-block;
    font-size: 0.67rem;
    font-weight: 700;
    padding: 0.2rem 0.55rem;
    border-radius: 999px;
    white-space: nowrap;
    letter-spacing: 0.03em;
}
.badge-amber { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.badge-blue  { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.badge-green { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.badge-gray  { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

/* Breakdown */
.breakdown-list {
    list-style: none;
    padding: 0.75rem 1.35rem;
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}
.breakdown-item {}
.bd-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.3rem;
}
.bd-label {
    font-size: 0.72rem;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.bd-count {
    font-family: 'DM Mono', monospace;
    font-size: 0.85rem;
    font-weight: 500;
}
.bd-bar-track {
    height: 5px;
    background: #f1f5f9;
    border-radius: 999px;
    overflow: hidden;
}
.bd-bar-fill {
    height: 100%;
    border-radius: 999px;
    transition: width 0.6s ease;
}

.bd-legend {
    display: flex;
    gap: 1rem;
    padding: 0.75rem 1.35rem;
    border-top: 1px solid #f1f5f9;
    margin-top: 0.25rem;
}
.legend-item {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.7rem;
    color: #64748b;
    font-weight: 500;
}
.leg-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}

/* ── Quick Links ── */
.quick-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.85rem;
}
.quick-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.85rem;
    text-decoration: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.quick-card:hover {
    border-color: #bfdbfe;
    box-shadow: 0 4px 12px rgba(29,78,216,0.07);
}
.quick-icon {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.quick-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
}
.quick-sub {
    font-size: 0.72rem;
    color: #94a3b8;
    margin-top: 0.15rem;
}
.quick-arrow {
    margin-left: auto;
    color: #cbd5e1;
    flex-shrink: 0;
    transition: color 0.12s, transform 0.12s;
}
.quick-card:hover .quick-arrow { color: #1d4ed8; transform: translateX(3px); }

/* ── Responsive ── */
@media (max-width: 1024px) {
    .stat-grid   { grid-template-columns: repeat(2, 1fr); }
    .main-row    { grid-template-columns: 1fr; }
    .quick-grid  { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .db-body { padding: 1rem; }
    .stat-grid  { grid-template-columns: 1fr 1fr; }
    .quick-grid { grid-template-columns: 1fr 1fr; }
    .dh-actions { display: none; }
}
</style>