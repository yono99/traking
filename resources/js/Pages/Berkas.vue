<template>
    <AppLayout title="Daftar Berkas">
        <div class="page">

            <!-- ── HEADER ── -->
            <div class="page-header">
                <div class="header-left">
                    <div class="header-eyebrow">
                        <span class="eyebrow-dot"></span>
                        Manajemen Berkas
                    </div>
                    <h1 class="header-title">Daftar Berkas</h1>
                    <p class="header-sub">Kelola dan pantau status seluruh berkas layanan pertanahan</p>
                </div>
                <div class="header-stat">
                    <div class="stat-num">{{ filteredServices.length }}</div>
                    <div class="stat-lbl">Total Berkas</div>
                    <div class="stat-bar">
                        <div
                            class="stat-bar-fill"
                            :style="{ width: services.length ? (filteredServices.length / services.length * 100) + '%' : '0%' }"
                        ></div>
                    </div>
                    <div class="stat-ratio">{{ filteredServices.length }} / {{ services.length }}</div>
                </div>
            </div>

            <!-- ── FILTER ── -->
            <div class="filter-card">
                <div class="filter-top">
                    <div class="filter-heading">
                        <i class="ti ti-adjustments-horizontal" aria-hidden="true"></i>
                        Filter &amp; Pencarian
                    </div>
                    <button v-if="isFiltered" class="btn-reset" @click="resetFilter">
                        <i class="ti ti-refresh" aria-hidden="true"></i>
                        Reset
                    </button>
                </div>

                <div class="filter-grid">
                    <div class="field">
                        <label class="field-label">Kode Berkas</label>
                        <div class="field-wrap">
                            <i class="ti ti-bolt field-ico" aria-hidden="true"></i>
                            <input v-model="filters.kode_berkas" placeholder="Cari kode berkas…" class="field-input" @input="debouncedFilter" />
                        </div>
                    </div>
                    
                    <div class="field">
                        <label class="field-label">Nomor Hak</label>
                        <div class="field-wrap">
                            <i class="ti ti-certificate field-ico" aria-hidden="true"></i>
                            <input v-model="filters.nomer_hak" placeholder="Cari nomor hak…" class="field-input" @input="debouncedFilter" />
                        </div>
                    </div>
                    <div class="field">
                        <label class="field-label">Jenis Hak</label>
                        <div class="field-wrap">
                            <i class="ti ti-category field-ico" aria-hidden="true"></i>
                            <select v-model="filters.jenis_hak" class="field-input" @change="applyFilter">
                                <option value="">Semua Jenis Hak</option>
                                <option>Hak Milik</option>
                                <option>Hak Guna Usaha</option>
                                <option>Hak Guna Bangunan</option>
                                <option>Hak Pakai</option>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label class="field-label">Desa / Kecamatan</label>
                        <div class="field-wrap">
                            <i class="ti ti-map-pin field-ico" aria-hidden="true"></i>
                            <input v-model="filters.desa_kecamatan" placeholder="Cari lokasi…" class="field-input" @input="debouncedFilter" />
                        </div>
                    </div>
                    <div class="field">
                        <label class="field-label">Status</label>
                        <div class="field-wrap">
                            <i class="ti ti-circle-check field-ico" aria-hidden="true"></i>
                            <select v-model="filters.status" class="field-input" @change="applyFilter">
                                <option value="">Semua Status</option>
                                <option value="SELESAI DISERAHKAN">Selesai Diserahkan</option>
                                <option value="PROSES LOKET">Proses Loket</option>
                                <option value="FORWARD LOKET">Forward Loket</option>
                                <option value="PROSES BUKU TANAH">Proses Buku Tanah</option>
                                <option value="FORWARD BUKU TANAH">Forward Buku Tanah</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── TABLE ── -->
            <div class="table-card">

                <!-- Empty -->
                <div v-if="filteredServices.length === 0" class="empty">
                    <div class="empty-ico"><i class="ti ti-folder-off" aria-hidden="true"></i></div>
                    <p class="empty-title">Tidak ada data ditemukan</p>
                    <p class="empty-msg">{{ isFiltered ? 'Coba ubah atau reset filter pencarian' : 'Belum ada berkas yang tersedia' }}</p>
                </div>

                <template v-else>
                    <div class="table-scroll">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th class="th-no">No</th>
                                    <th>Kode Berkas</th>
                                    <th>Nomor Hak</th>
                                    <th>Jenis Hak</th>
                                    <th>Desa / Kecamatan</th>
                                    <th>Status</th>
                                  
                                    <th>Nomor HP</th>
                                    <th class="th-act">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(service, index) in filteredServices"
                                    :key="service.id"
                                    class="tbl-row"
                                    @click="openUpdateModal(service)"
                                >
                                    <td class="td-no">{{ index + 1 }}</td>

                                    <td>
                                        <div class="kode-chip">
                                            <i class="ti ti-bolt" aria-hidden="true"></i>
                                            {{ service.kode_berkas || '—' }}
                                        </div>
                                    </td>

 
                                     
                                    <td><span class="mono">{{ service.land_book?.nomer_hak || '—' }}</span></td>

                                    <td>
                                        <span class="jenis-chip">{{ service.land_book?.jenis_hak || '—' }}</span>
                                    </td>

                                    <td class="td-muted">{{ service.land_book?.desa_kecamatan || '—' }}</td>

                                    <td>
                                        <span :class="['status-pill', statusClass(service.status)]">
                                            <span class="pill-dot"></span>
                                            {{ service.status || '—' }}
                                        </span>
                                    </td>

                                   
                                    <td><span class="mono">{{ service.nomor_hp ? '0' + service.nomor_hp : '—' }}</span></td>

                                    <td class="td-act" @click.stop>
                                        <button class="btn-edit" @click="openUpdateModal(service)" title="Ubah data">
                                            <i class="ti ti-edit" aria-hidden="true"></i>
                                            Ubah
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="tbl-footer">
                        <span class="footer-info">
                            Menampilkan <strong>{{ filteredServices.length }}</strong> dari <strong>{{ services.length }}</strong> berkas
                        </span>
                        <div class="footer-dots">
                            <span
                                v-for="s in statusSummary"
                                :key="s.label"
                                :class="['dot-badge', s.cls]"
                            >{{ s.label }}: {{ s.count }}</span>
                        </div>
                    </div>
                </template>
            </div>

            <!-- ── MODAL ── -->
            <UpdateModal
                :show="showUpdateModal"
                :service-id="selectedService?.id"
                :service="selectedService"
                :land-book="selectedService?.land_book"
                @close="closeUpdateModal"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import UpdateModal from "@/Components/UpdateModal.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    services: { type: Array, required: true, default: () => [] },
});

const showUpdateModal = ref(false);
const selectedService = ref(null);
const filters = ref({
    kode_berkas: "", nomor_berkas: "", nomer_hak: "",
    jenis_hak: "", desa_kecamatan: "", status: "",
});
let filterTimeout;

const filteredServices = computed(() =>
    props.services.filter(s =>
        (!filters.value.kode_berkas   || s.kode_berkas?.toLowerCase().includes(filters.value.kode_berkas.toLowerCase())) &&
        (!filters.value.nomor_berkas  || s.nomor_berkas?.toLowerCase().includes(filters.value.nomor_berkas.toLowerCase())) &&
        (!filters.value.nomer_hak     || s.land_book?.nomer_hak?.toString().includes(filters.value.nomer_hak)) &&
        (!filters.value.jenis_hak     || s.land_book?.jenis_hak?.includes(filters.value.jenis_hak)) &&
        (!filters.value.desa_kecamatan|| s.land_book?.desa_kecamatan?.toLowerCase().includes(filters.value.desa_kecamatan.toLowerCase())) &&
        (!filters.value.status        || s.status?.includes(filters.value.status))
    )
);

const isFiltered = computed(() => Object.values(filters.value).some(v => v !== ""));

const statusSummary = computed(() => {
    const map = { selesai: 0, proses: 0, forward: 0 };
    props.services.forEach(s => {
        if (s.status?.includes('SELESAI')) map.selesai++;
        else if (s.status?.includes('PROSES')) map.proses++;
        else if (s.status?.includes('FORWARD')) map.forward++;
    });
    return [
        { label: 'Selesai', count: map.selesai, cls: 'dot-green' },
        { label: 'Proses',  count: map.proses,  cls: 'dot-amber' },
        { label: 'Forward', count: map.forward, cls: 'dot-blue'  },
    ];
});

const debouncedFilter = () => { clearTimeout(filterTimeout); filterTimeout = setTimeout(() => {}, 300); };
const applyFilter = () => {};
const resetFilter = () => { Object.keys(filters.value).forEach(k => filters.value[k] = ""); };
const openUpdateModal  = s => { selectedService.value = s; showUpdateModal.value = true; };
const closeUpdateModal = () => { selectedService.value = null; showUpdateModal.value = false; };

const statusClass = status => {
    if (status?.includes('SELESAI')) return 'pill-green';
    if (status?.includes('PROSES'))  return 'pill-amber';
    if (status?.includes('FORWARD')) return 'pill-blue';
    return 'pill-gray';
};
</script>

<style scoped>
/* ── Google Font ── */
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=DM+Mono:wght@400;500&display=swap');

/* ── Base ── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.page {
    font-family: 'DM Sans', sans-serif;
    background: #f5f4f0;
    min-height: 100vh;
    padding: 2.5rem 1.75rem;
    --ink: #1a1814;
    --ink2: #6b6860;
    --ink3: #a09e9a;
    --surface: #ffffff;
    --border: #e5e3de;
    --accent: #1a1814;
    --accent-fg: #ffffff;
    --radius: 14px;
    --radius-sm: 8px;
}

/* ── Header ── */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2rem;
    margin-bottom: 2rem;
    max-width: 1440px;
}

.header-eyebrow {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--ink3);
    margin-bottom: 0.5rem;
}

.eyebrow-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #2563eb;
    animation: blink 2s ease-in-out infinite;
}

@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

.header-title {
    font-size: 2.25rem;
    font-weight: 600;
    letter-spacing: -0.03em;
    color: var(--ink);
    line-height: 1.1;
    margin-bottom: 0.5rem;
}

.header-sub {
    font-size: 0.9rem;
    color: var(--ink2);
    line-height: 1.6;
}

.header-stat {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.25rem 1.75rem;
    min-width: 200px;
    text-align: right;
}

.stat-num {
    font-size: 2.5rem;
    font-weight: 600;
    letter-spacing: -0.04em;
    color: var(--ink);
    line-height: 1;
}

.stat-lbl {
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--ink3);
    margin: 0.35rem 0 0.75rem;
}

.stat-bar {
    height: 3px;
    background: #ece9e3;
    border-radius: 99px;
    overflow: hidden;
    margin-bottom: 0.4rem;
}

.stat-bar-fill {
    height: 100%;
    background: var(--ink);
    border-radius: 99px;
    transition: width 0.4s ease;
}

.stat-ratio {
    font-size: 0.75rem;
    color: var(--ink3);
    font-family: 'DM Mono', monospace;
}

/* ── Filter Card ── */
.filter-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.5rem 1.75rem;
    margin-bottom: 1.25rem;
    max-width: 1440px;
}

.filter-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
}

.filter-heading {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--ink);
}

.filter-heading .ti { font-size: 16px; }

.btn-reset {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.78rem;
    font-weight: 500;
    color: var(--ink2);
    background: #f5f4f0;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.45rem 0.9rem;
    cursor: pointer;
    transition: color 0.15s, background 0.15s;
    font-family: inherit;
}

.btn-reset:hover { color: var(--ink); background: #eceae4; }
.btn-reset .ti { font-size: 14px; }

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
}

.field { display: flex; flex-direction: column; gap: 0.4rem; }

.field-label {
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: var(--ink3);
}

.field-wrap { position: relative; }

.field-ico {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 15px;
    color: var(--ink3);
    pointer-events: none;
}

.field-input {
    width: 100%;
    padding: 0.6rem 0.85rem 0.6rem 2.25rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 0.85rem;
    font-family: 'DM Sans', sans-serif;
    color: var(--ink);
    background: #fafaf8;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
    appearance: none;
    -webkit-appearance: none;
}

.field-input:focus {
    border-color: #1a1814;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(26,24,20,0.07);
}

.field-input::placeholder { color: var(--ink3); }

/* ── Table Card ── */
.table-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    max-width: 1440px;
}

/* ── Empty ── */
.empty {
    padding: 4rem 2rem;
    text-align: center;
}

.empty-ico {
    font-size: 2.5rem;
    color: var(--ink3);
    margin-bottom: 1rem;
}

.empty-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 0.5rem;
}

.empty-msg { font-size: 0.875rem; color: var(--ink2); }

/* ── Table ── */
.table-scroll { overflow-x: auto; }

.tbl {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.855rem;
}

.tbl thead tr {
    background: #f8f7f3;
    border-bottom: 1px solid var(--border);
}

.tbl th {
    padding: 0.9rem 1rem;
    text-align: left;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: var(--ink3);
    white-space: nowrap;
    border-right: 1px solid var(--border);
}

.tbl th:last-child { border-right: none; }
.th-no, .th-act { text-align: center; }

.tbl-row {
    border-bottom: 1px solid var(--border);
    cursor: pointer;
    transition: background 0.12s;
}

.tbl-row:last-child { border-bottom: none; }
.tbl-row:hover { background: #faf9f6; }

.tbl td {
    padding: 0.85rem 1rem;
    color: var(--ink2);
    border-right: 1px solid var(--border);
    vertical-align: middle;
}

.tbl td:last-child { border-right: none; }

.td-no, .td-act { text-align: center; }

.td-no {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--ink3);
    font-family: 'DM Mono', monospace;
}

.td-name {
    font-weight: 500;
    color: var(--ink);
}

.td-muted { color: var(--ink2); }

/* ── Kode Chip ── */
.kode-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #1a1814;
    color: #f5f4f0;
    font-family: 'DM Mono', monospace;
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.35rem 0.7rem;
    border-radius: 6px;
    white-space: nowrap;
    letter-spacing: 0.03em;
}

.kode-chip .ti { font-size: 12px; }

/* ── Mono ── */
.mono {
    font-family: 'DM Mono', monospace;
    font-size: 0.82rem;
    color: var(--ink);
}

/* ── Jenis Chip ── */
.jenis-chip {
    display: inline-block;
    background: #f0efe9;
    color: var(--ink2);
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.3rem 0.65rem;
    border-radius: 6px;
    border: 1px solid var(--border);
    white-space: nowrap;
}

/* ── Status Pills ── */
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.35rem 0.7rem;
    border-radius: 99px;
    white-space: nowrap;
    border: 1px solid transparent;
}

.pill-dot {
    width: 5px; height: 5px;
    border-radius: 50%;
}

.pill-green  { background: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
.pill-green .pill-dot  { background: #10b981; }

.pill-amber  { background: #fffbeb; color: #78350f; border-color: #fcd34d; }
.pill-amber .pill-dot  { background: #f59e0b; }

.pill-blue   { background: #eff6ff; color: #1e3a8a; border-color: #bfdbfe; }
.pill-blue .pill-dot   { background: #3b82f6; }

.pill-gray   { background: #f8f7f3; color: var(--ink2); border-color: var(--border); }
.pill-gray .pill-dot   { background: var(--ink3); }

/* ── Edit Button ── */
.btn-edit {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.78rem;
    font-weight: 600;
    font-family: inherit;
    color: var(--ink);
    background: #f5f4f0;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.45rem 0.9rem;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s, transform 0.1s;
    white-space: nowrap;
}

.btn-edit:hover {
    background: var(--ink);
    color: #fff;
    border-color: var(--ink);
}

.btn-edit:active { transform: scale(0.97); }
.btn-edit .ti { font-size: 14px; }

/* ── Table Footer ── */
.tbl-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
    padding: 0.9rem 1.25rem;
    background: #f8f7f3;
    border-top: 1px solid var(--border);
}

.footer-info {
    font-size: 0.8rem;
    color: var(--ink3);
}

.footer-info strong { color: var(--ink); font-weight: 600; }

.footer-dots { display: flex; gap: 0.5rem; flex-wrap: wrap; }

.dot-badge {
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.25rem 0.65rem;
    border-radius: 99px;
}

.dot-green { background: #ecfdf5; color: #065f46; }
.dot-amber { background: #fffbeb; color: #78350f; }
.dot-blue  { background: #eff6ff; color: #1e3a8a; }

/* ── Responsive ── */
@media (max-width: 900px) {
    .page-header { flex-direction: column; }
    .header-stat { width: 100%; text-align: left; }
    .stat-num { font-size: 2rem; }
}

@media (max-width: 600px) {
    .page { padding: 1.25rem 0.75rem; }
    .header-title { font-size: 1.75rem; }
    .filter-grid { grid-template-columns: 1fr; }
    .tbl-footer { flex-direction: column; align-items: flex-start; }
}
</style>