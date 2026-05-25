<script>
import { ref } from "vue";
import { router, Head } from "@inertiajs/vue3";

export default {
    components: { Head },

    props: {
        found:   { type: Boolean, default: null },
        kode:    { type: String,  default: null },
        service: { type: Object,  default: null },
        steps:   { type: Array,   default: () => [] },
        history: { type: Array,   default: () => [] },
    },

    setup(props) {
        const inputKode = ref(props.kode || "");
        const searching = ref(false);

        const cariKode = () => {
            const k = inputKode.value.trim().toUpperCase();
            if (!k) return;
            searching.value = true;
            router.get(`/lacak/${k}`, {}, {
                onFinish: () => { searching.value = false; },
            });
        };

        const handleKeydown = (e) => {
            if (e.key === "Enter") cariKode();
        };

        // 10 tahap proses berkas
        const allSteps = [
            { key: "LOKET",                     label: "Loket",                          icon: "loket",   step: 1 },
            { key: "VERIFIKASI_DOKUMEN",         label: "Loket Verifikasi Dokumen",       icon: "verif",   step: 2 },
            { key: "FORWARD BUKU TANAH",         label: "Forward Buku Tanah",             icon: "forward", step: 3 },
            { key: "PROSES BUKU TANAH",          label: "Pencarian Arsip Buku Tanah",     icon: "buku",    step: 4 },
            { key: "FORWARD PENGUKURAN",         label: "Forward Pengukuran",             icon: "forward", step: 5 },
            { key: "PROSES PENGUKURAN",          label: "Validasi Bidang",                icon: "ukur",    step: 6 },
            { key: "PROSES VALIDASI BIDANG",     label: "Validasi Tekstual Surat Ukur",   icon: "validasi",step: 7 },
            { key: "PROSES VALIDASI BUKU TANAH", label: "Validasi Buku Tanah",            icon: "validasi",step: 8 },
            { key: "PROSES LOKET PENYERAHAN",    label: "Loket Penyerahan",               icon: "serah",   step: 9 },
            { key: "SELESAI DISERAHKAN",         label: "Selesai",                        icon: "done",    step: 10 },
        ];

        // Hitung steps dengan done/active berdasarkan status service
        const computeSteps = (currentStatus) => {
            if (!currentStatus) return allSteps.map(s => ({ ...s, done: false, active: false }));
            const currentIdx = allSteps.findIndex(s => s.key === currentStatus);
            return allSteps.map((s, i) => ({
                ...s,
                done:   i < currentIdx,
                active: i === currentIdx,
            }));
        };

        // Gunakan steps dari props jika ada, atau compute dari service.status
        const getSteps = () => {
            if (props.steps && props.steps.length > 0) return props.steps;
            if (props.service?.status) return computeSteps(props.service.status);
            return allSteps.map(s => ({ ...s, done: false, active: false }));
        };

        const iconMap = {
            loket:   "M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6",
            verif:   "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4",
            forward: "M13 5l7 7-7 7M5 5l7 7-7 7",
            buku:    "M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253",
            ukur:    "M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7",
            validasi:"M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z",
            serah:   "M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1.83 9.172A2 2 0 008.82 19h6.36a2 2 0 001.99-1.828L19 8",
            done:    "M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z",
        };

        const getIcon = (icon) => iconMap[icon] || iconMap.loket;

        // Hapus kata "PROSES" dan "FORWARD" dari label status
        const statusLabel = (status) => {
            if (!status) return "-";
            return status
                .replace(/^PROSES\s+/i, "")
                .replace(/^FORWARD\s+/i, "")
                .toLowerCase()
                .replace(/\b\w/g, c => c.toUpperCase());
        };

        return { inputKode, searching, cariKode, handleKeydown, getIcon, getSteps, statusLabel, allSteps };
    },
};
</script>

<template>
    <Head title="Lacak Berkas">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="" />
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
    </Head>

    <div class="lb-wrap">
        <div class="lb-container">

            <!-- ── Page Header ── -->
            <div class="lb-page-header">
                <div>
                    <h1 class="lb-page-title">Lacak Status Berkas</h1>
                    <p class="lb-page-sub">Masukkan kode berkas untuk melihat progres layanan Anda.</p>
                </div>
                <div class="lb-header-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    Sistem Pelacakan Berkas
                </div>
            </div>

            <!-- ── Search Card ── -->
            <div class="lb-card">
                <div class="lb-section">
                    <div class="lb-section-label">
                        <div class="lb-section-num">01</div>
                        <div>
                            <div class="lb-section-title">Kode Berkas</div>
                            <div class="lb-section-sub">Masukkan kode yang diterima via WhatsApp</div>
                        </div>
                    </div>
                    <div class="lb-fields">
                        <div class="lb-field lb-field-full">
                            <label class="lb-label" for="kode_input">Kode Berkas</label>
                            <div class="lb-search-row">
                                <div class="lb-input-wrap" style="flex:1">
                                    <svg class="lb-input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                    </svg>
                                    <input
                                        id="kode_input"
                                        v-model="inputKode"
                                        @keydown="handleKeydown"
                                        type="text"
                                        class="lb-input lb-input-mono"
                                        placeholder="Contoh: BRK-20250513-A1B2C"
                                        autocomplete="off"
                                        spellcheck="false"
                                    />
                                </div>
                                <button @click="cariKode" :disabled="searching" class="lb-submit">
                                    <span v-if="!searching" style="display:flex;align-items:center;gap:.4rem">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                        </svg>
                                        Cari Berkas
                                    </span>
                                    <span v-else class="lb-submit-loading">
                                        <svg class="lb-spin" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Mencari...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Not Found ── -->
            <div v-if="found === false" class="lb-card">
                <div class="lb-notfound">
                    <div class="lb-notfound-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="lb-notfound-title">Berkas Tidak Ditemukan</h2>
                    <p class="lb-notfound-sub">
                        Kode <span class="lb-kode-hl">{{ kode }}</span> tidak ditemukan dalam sistem.
                        Pastikan kode yang dimasukkan sudah benar.
                    </p>
                </div>
            </div>

            <!-- ── Found ── -->
            <template v-if="found === true && service">

                <!-- Info Card -->
                <div class="lb-card">
                    <div class="lb-section">
                        <div class="lb-section-label">
                            <div class="lb-section-num">02</div>
                            <div>
                                <div class="lb-section-title">Informasi Berkas</div>
                                <div class="lb-section-sub">Detail data pertanahan</div>
                            </div>
                        </div>
                        <div class="lb-fields">
                            <!-- Kode + Status -->
                            <div class="lb-field lb-field-full">
                                <div class="lb-info-header">
                                    <div class="lb-info-kode-wrap">
                                        <span class="lb-info-kode-label">Kode Berkas</span>
                                        <span class="lb-info-kode">{{ service.kode_berkas }}</span>
                                    </div>
                                    <div class="lb-status-pill">
                                        <span class="lb-status-dot"></span>
                                        {{ statusLabel(service.status) }}
                                    </div>
                                </div>
                            </div>
                            <!-- Grid detail -->
                            <div class="lb-field">
                                <span class="lb-detail-label">Nama Pemohon</span>
                                <span class="lb-detail-value">{{ service.nama_pemohon || '—' }}</span>
                            </div>
                            <div class="lb-field">
                                <span class="lb-detail-label">Jenis Hak</span>
                                <span class="lb-detail-value">{{ service.jenis_hak || '—' }}</span>
                            </div>
                            <div class="lb-field">
                                <span class="lb-detail-label">Nomor Hak</span>
                                <span class="lb-detail-value mono">{{ service.nomer_hak || '—' }}</span>
                            </div>
                            <div class="lb-field">
                                <span class="lb-detail-label">Desa / Kecamatan</span>
                                <span class="lb-detail-value">{{ service.desa_kecamatan || '—' }}</span>
                            </div>
                            <div class="lb-field">
                                <span class="lb-detail-label">Tanggal Daftar</span>
                                <span class="lb-detail-value">{{ service.created_at }}</span>
                            </div>
                            <div class="lb-field">
                                <span class="lb-detail-label">Terakhir Diperbarui</span>
                                <span class="lb-detail-value">{{ service.updated_at }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Card -->
                <div class="lb-card">
                    <div class="lb-section">
                        <div class="lb-section-label">
                            <div class="lb-section-num">03</div>
                            <div>
                                <div class="lb-section-title">Progres Berkas</div>
                                <div class="lb-section-sub">Tahapan proses layanan pertanahan</div>
                            </div>
                        </div>
                        <div class="lb-stepper-wrap">
                            <div class="lb-stepper">
                                <div
                                    v-for="(step, idx) in getSteps()"
                                    :key="step.key || step.status || idx"
                                    :class="['lb-step', step.done ? 'done' : '', step.active ? 'active' : '']"
                                >
                                    <div v-if="idx > 0" :class="['lb-connector', (step.done || step.active) ? 'filled' : '']"></div>
                                    <div class="lb-step-node">
                                        <div class="lb-step-circle">
                                            <svg v-if="step.done" xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span v-else-if="step.active" class="lb-pulse"></span>
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" :d="getIcon(step.icon)" />
                                            </svg>
                                        </div>
                                         
                                        <!-- Label tanpa kata "PROSES"/"FORWARD" -->
                                        <div class="lb-step-label">{{ step.label || statusLabel(step.status) }}</div>
                                        <div v-if="step.active" class="lb-step-badge">Sekarang</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

             

            </template>

            <!-- Footer -->
            <div class="lb-footer-note">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Layanan Pertanahan — Sistem Pelacakan Berkas
            </div>

        </div>
    </div>
</template>

<style scoped>
*, *::before, *::after { box-sizing: border-box; }

.lb-wrap {
    font-family: "Plus Jakarta Sans", sans-serif;
    min-height: 100vh;
    background: #f4f6fb;
    padding: 2.5rem 1rem 4rem;
}
.lb-container {
    max-width: 780px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

/* ── Page Header ── */
.lb-page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}
.lb-page-title {
    font-size: 1.6rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.02em;
    margin-bottom: 0.3rem;
}
.lb-page-sub {
    font-size: 0.875rem;
    color: #64748b;
}
.lb-header-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    border-radius: 999px;
    padding: 0.4rem 0.9rem;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}

/* ── Card ── */
.lb-card {
    background: #fff;
    border-radius: 20px;
    border: 1px solid #e8ecf4;
    box-shadow: 0 4px 24px rgba(15, 23, 42, 0.06);
    overflow: hidden;
    animation: fadeUp 0.4s ease both;
}

/* ── Section ── */
.lb-section {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 2rem;
    padding: 2rem;
}
.lb-section-label {
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
    padding-top: 0.2rem;
}
.lb-section-num {
    font-family: "JetBrains Mono", monospace;
    font-size: 0.72rem;
    font-weight: 500;
    color: #2563eb;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    padding: 0.2rem 0.45rem;
    border-radius: 5px;
    flex-shrink: 0;
    margin-top: 2px;
}
.lb-section-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.2rem;
}
.lb-section-sub {
    font-size: 0.78rem;
    color: #94a3b8;
    line-height: 1.4;
}

/* ── Fields ── */
.lb-fields {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.1rem;
}
.lb-field-full { grid-column: 1 / -1; }
.lb-field { display: flex; flex-direction: column; gap: 0.4rem; }
.lb-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #374151;
}

/* ── Input ── */
.lb-search-row {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}
.lb-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.lb-input-icon {
    position: absolute;
    left: 0.75rem;
    color: #9ca3af;
    pointer-events: none;
    z-index: 1;
}
.lb-input {
    width: 100%;
    padding: 0.65rem 0.85rem 0.65rem 2.4rem;
    font-size: 0.875rem;
    font-family: "Plus Jakarta Sans", sans-serif;
    color: #1e293b;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    appearance: none;
}
.lb-input:focus {
    border-color: #2563eb;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
.lb-input::placeholder { color: #c0c8d8; }
.lb-input-mono {
    font-family: "JetBrains Mono", monospace !important;
    letter-spacing: 0.06em;
}

/* ── Submit ── */
.lb-submit {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #1d4ed8;
    color: #fff;
    font-family: "Plus Jakarta Sans", sans-serif;
    font-size: 0.875rem;
    font-weight: 600;
    padding: 0.65rem 1.5rem;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    white-space: nowrap;
    box-shadow: 0 2px 12px rgba(29, 78, 216, 0.3);
    transition: all 0.2s;
}
.lb-submit:hover:not(:disabled) {
    background: #1e40af;
    transform: translateY(-1px);
    box-shadow: 0 4px 20px rgba(29, 78, 216, 0.4);
}
.lb-submit:disabled { opacity: 0.6; cursor: not-allowed; }
.lb-submit-loading { display: flex; align-items: center; gap: 0.5rem; }

/* ── Not Found ── */
.lb-notfound {
    padding: 2.5rem 2rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}
.lb-notfound-icon {
    width: 56px; height: 56px;
    background: #fef2f2;
    color: #ef4444;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 0.25rem;
}
.lb-notfound-title { font-size: 1rem; font-weight: 700; color: #0f172a; }
.lb-notfound-sub { font-size: 0.83rem; color: #64748b; line-height: 1.6; max-width: 380px; }
.lb-kode-hl { font-family: "JetBrains Mono", monospace; color: #ef4444; font-weight: 600; }

/* ── Info Header ── */
.lb-info-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 12px;
    padding: 1rem 1.25rem;
}
.lb-info-kode-label {
    font-size: 0.68rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #94a3b8;
    display: block;
    margin-bottom: 0.25rem;
}
.lb-info-kode {
    font-family: "JetBrains Mono", monospace;
    font-size: 1.35rem;
    font-weight: 700;
    color: #1d4ed8;
    letter-spacing: 0.04em;
}
.lb-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
    border-radius: 999px;
    padding: 5px 14px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}
.lb-status-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #16a34a;
    animation: pulse 2s infinite;
}

/* ── Detail items ── */
.lb-detail-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: #94a3b8;
}
.lb-detail-value {
    font-size: 0.9rem;
    font-weight: 500;
    color: #1e293b;
}
.lb-detail-value.mono { font-family: "JetBrains Mono", monospace; }

/* ── Stepper ── */
.lb-stepper-wrap {
    overflow-x: auto;
    padding-bottom: 0.5rem;
}
.lb-stepper {
    display: flex;
    align-items: flex-start;
    min-width: max-content;
}
.lb-step {
    display: flex;
    align-items: flex-start;
    flex: 1;
    min-width: 72px;
}
.lb-connector {
    flex-shrink: 0;
    height: 2px;
    width: 40px;
    background: #e2e8f0;
    margin-top: 17px;
    transition: background 0.3s;
}
.lb-connector.filled { background: #2563eb; }
.lb-step-node {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.35rem;
    flex-shrink: 0;
}
.lb-step-circle {
    width: 34px; height: 34px;
    border-radius: 50%;
    border: 2px solid #e2e8f0;
    background: #fff;
    display: flex; align-items: center; justify-content: center;
    color: #94a3b8;
    transition: all 0.3s;
    position: relative; z-index: 1;
}
.lb-step.done .lb-step-circle { background: #2563eb; border-color: #2563eb; color: #fff; }
.lb-step.active .lb-step-circle { border-color: #2563eb; box-shadow: 0 0 0 4px rgba(37,99,235,0.15); color: #2563eb; }
.lb-step-label {
    font-size: 0.65rem;
    font-weight: 500;
    color: #94a3b8;
    text-align: center;
    max-width: 72px;
    line-height: 1.3;
}
.lb-step.done .lb-step-label { color: #2563eb; font-weight: 600; }
.lb-step.active .lb-step-label { color: #1e293b; font-weight: 700; }
.lb-step-badge {
    font-size: 0.58rem;
    font-weight: 700;
    color: #fff;
    background: #2563eb;
    border-radius: 999px;
    padding: 2px 7px;
    white-space: nowrap;
}
.lb-pulse {
    width: 10px; height: 10px;
    border-radius: 50%;
    background: #2563eb;
    display: inline-block;
    animation: pulse 1.5s infinite;
}

/* ── Timeline ── */
.lb-timeline {
    position: relative;
    padding-left: 1.5rem;
    display: flex;
    flex-direction: column;
}
.lb-timeline::before {
    content: '';
    position: absolute;
    left: 7px; top: 8px; bottom: 8px;
    width: 2px;
    background: #e2e8f0;
    border-radius: 2px;
}
.lb-timeline-item {
    position: relative;
    padding: 0 0 1.1rem 1.1rem;
}
.lb-timeline-item.last { padding-bottom: 0; }
.lb-timeline-dot {
    position: absolute;
    left: -1.5rem; top: 5px;
    width: 14px; height: 14px;
    border-radius: 50%;
    background: #2563eb;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #bfdbfe;
}
.lb-timeline-content {
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 10px;
    padding: 0.8rem 1rem;
}
.lb-timeline-status { font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 0.15rem; }
.lb-timeline-remarks { font-size: 0.77rem; color: #64748b; margin-bottom: 0.3rem; }
.lb-timeline-time { font-size: 0.68rem; color: #94a3b8; font-family: "JetBrains Mono", monospace; }

/* ── Footer ── */
.lb-footer-note {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    font-size: 0.75rem;
    color: #94a3b8;
    padding-top: 0.5rem;
}

/* ── Animations ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: 0.6; transform: scale(0.85); }
}
.lb-spin { animation: spin 0.8s linear infinite; }
@keyframes spin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}

/* ── Responsive ── */
@media (max-width: 640px) {
    .lb-section {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 1.5rem;
    }
    .lb-fields { grid-template-columns: 1fr; }
    .lb-page-header { flex-direction: column; }
    .lb-header-badge { display: none; }
}
</style>