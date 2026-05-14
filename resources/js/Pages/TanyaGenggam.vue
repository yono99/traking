<template>
    <AppLayout title="Berkas Pending">
        <div class="page">
            <!-- ── HEADER ── -->
            <div class="page-header">
                <div class="header-left">
                    <div class="eyebrow">
                        <span class="eyebrow-pulse"></span>
                        Sistem Pencarian
                    </div>
                    <h1 class="page-title">Berkas Pending</h1>
                    <p class="page-sub">
                        Cari dan perbarui status berkas layanan pertanahan
                    </p>
                </div>
                <div class="header-badge" v-if="services.length > 0">
                    <div class="badge-num">{{ filteredServices.length }}</div>
                    <div class="badge-lbl">Hasil ditemukan</div>
                </div>
            </div>

            <!-- ── SEARCH & FILTER CARD ── -->
            <div class="search-card">
                <div class="search-card-title">
                    <i class="ti ti-search" aria-hidden="true"></i>
                    Filter &amp; Pencarian
                </div>

                <div class="search-row">
                    <!-- Nomor Hak -->
                    <div class="field">
                        <label class="field-label">Nomor Hak</label>
                        <div class="field-wrap">
                            <i
                                class="ti ti-certificate field-ico"
                                aria-hidden="true"
                            ></i>
                            <input
                                v-model="nomorHak"
                                type="text"
                                placeholder="Masukkan nomor hak..."
                                class="field-input"
                                @keyup.enter="search"
                                @input="liveSearch"
                            />
                            <button
                                v-if="nomorHak"
                                class="field-clear"
                                @click="clearSearch"
                                title="Hapus"
                            >
                                <i class="ti ti-x" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Jenis Hak filter -->
                    <div class="field">
                        <label class="field-label">Jenis Hak</label>
                        <div class="field-wrap">
                            <i
                                class="ti ti-category field-ico"
                                aria-hidden="true"
                            ></i>
                            <select
                                v-model="filterJenisHak"
                                class="field-input field-select"
                            >
                                <option value="">Semua Jenis Hak</option>
                                <option>Hak Milik</option>
                                <option>Hak Guna Usaha</option>
                                <option>Hak Guna Bangunan</option>
                                <option>Hak Pakai</option>
                            </select>
                        </div>
                    </div>

                    <!-- Desa / Kecamatan filter -->
                    <div class="field">
                        <label class="field-label">Desa / Kecamatan</label>
                        <div class="field-wrap">
                            <i
                                class="ti ti-map-pin field-ico"
                                aria-hidden="true"
                            ></i>
                            <input
                                v-model="filterDesa"
                                type="text"
                                placeholder="Filter lokasi..."
                                class="field-input"
                            />
                        </div>
                    </div>

                    <!-- Search Button -->
                    <div class="field field-btn">
                        <label class="field-label">&nbsp;</label>
                        <button
                            class="btn-search"
                            @click="search"
                            :disabled="loading"
                        >
                            <i class="ti ti-search" aria-hidden="true"></i>
                            {{ loading ? "Mencari..." : "Cari Berkas" }}
                        </button>
                    </div>
                </div>

                <!-- Active filters info -->
                <div class="filter-active" v-if="hasFilter">
                    <span class="filter-active-lbl">Filter aktif:</span>
                    <span class="filter-tag" v-if="nomorHak">
                        Nomor Hak: {{ nomorHak }}
                        <button
                            @click="
                                nomorHak = '';
                                liveSearch();
                            "
                        >
                            <i class="ti ti-x"></i>
                        </button>
                    </span>
                    <span class="filter-tag" v-if="filterJenisHak">
                        {{ filterJenisHak }}
                        <button @click="filterJenisHak = ''">
                            <i class="ti ti-x"></i>
                        </button>
                    </span>
                    <span class="filter-tag" v-if="filterDesa">
                        {{ filterDesa }}
                        <button @click="filterDesa = ''">
                            <i class="ti ti-x"></i>
                        </button>
                    </span>
                    <button class="filter-reset" @click="resetAll">
                        Reset semua
                    </button>
                </div>
            </div>

            <!-- ── ERROR ── -->
            <div class="error-bar" v-if="errorMessage">
                <i class="ti ti-alert-circle" aria-hidden="true"></i>
                {{ errorMessage }}
            </div>

            <!-- ── LOADING SKELETON ── -->
            <div class="table-card" v-if="loading">
                <div class="sk-row" v-for="i in 4" :key="i">
                    <div class="sk sk-sm"></div>
                    <div class="sk sk-md"></div>
                    <div class="sk sk-lg"></div>
                    <div class="sk sk-md"></div>
                    <div class="sk sk-sm"></div>
                </div>
            </div>

            <!-- ── EMPTY STATE (belum search) ── -->
            <div class="empty-card" v-else-if="!hasSearched">
                <div class="empty-ico">
                    <i class="ti ti-search" aria-hidden="true"></i>
                </div>
                <p class="empty-title">
                    Masukkan nomor hak untuk mencari berkas
                </p>
                <p class="empty-sub">
                    Gunakan filter di atas untuk mempersempit hasil pencarian
                </p>
            </div>

            <!-- ── EMPTY STATE (tidak ada hasil) ── -->
            <div class="empty-card" v-else-if="filteredServices.length === 0">
                <div class="empty-ico empty-ico--warn">
                    <i class="ti ti-folder-off" aria-hidden="true"></i>
                </div>
                <p class="empty-title">Tidak ada data ditemukan</p>
                <p class="empty-sub">
                    Coba ubah kata kunci atau filter pencarian Anda
                </p>
            </div>

            <!-- ── TABLE ── -->
            <div class="table-card" v-else>
                <div class="table-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th class="th-no">No</th>
                                <th>kode Berkas</th>
                                <th>Nomor Hak</th>
                                <th>Jenis Hak</th>
                                <th>Desa / Kecamatan</th>
                                <th>Status</th>
                                <th class="th-act">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(service, index) in filteredServices"
                                :key="service.id"
                                class="tbl-row"
                                :class="{
                                    'tbl-row--updated': updatedIds.has(
                                        service.id,
                                    ),
                                }"
                            >
                                <td class="td-no">{{ index + 1 }}</td>
                                <td>
                                    <span class="mono-text">{{
                                        service.kode_berkas || "—"
                                    }}</span>
                                </td>
                                <td>
                                    <span class="nohak-chip">
                                        <i
                                            class="ti ti-file-certificate"
                                            aria-hidden="true"
                                        ></i>
                                        {{
                                            service.land_book?.nomer_hak || "—"
                                        }}
                                    </span>
                                </td>

                                <td>
                                    <span class="jenis-chip">{{
                                        service.land_book?.jenis_hak || "—"
                                    }}</span>
                                </td>

                                <td class="td-lokasi">
                                    <i
                                        class="ti ti-map-pin td-pin"
                                        aria-hidden="true"
                                    ></i>
                                    {{
                                        service.land_book?.desa_kecamatan || "—"
                                    }}
                                </td>

                                <td>
                                    <span
                                        :class="[
                                            'status-pill',
                                            statusClass(service.status),
                                        ]"
                                    >
                                        <span class="pill-dot"></span>
                                        {{ service.status || "Pending" }}
                                    </span>
                                </td>

                                <td class="td-act">
                                    <button
                                        class="btn-update"
                                        :class="{
                                            'btn-update--done': updatedIds.has(
                                                service.id,
                                            ),
                                        }"
                                        :disabled="
                                            updatingId === service.id ||
                                            updatedIds.has(service.id)
                                        "
                                        @click="updateStatus(service.id)"
                                    >
                                        <i
                                            :class="
                                                updatedIds.has(service.id)
                                                    ? 'ti ti-check'
                                                    : updatingId === service.id
                                                      ? 'ti ti-loader-2 spin'
                                                      : 'ti ti-refresh'
                                            "
                                            aria-hidden="true"
                                        ></i>
                                        {{
                                            updatedIds.has(service.id)
                                                ? "Diperbarui"
                                                : updatingId === service.id
                                                  ? "Memperbarui..."
                                                  : "Masukan ke Inventory"
                                        }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="tbl-footer">
                    <span class="footer-info">
                        Menampilkan
                        <strong>{{ filteredServices.length }}</strong> dari
                        <strong>{{ services.length }}</strong> berkas
                    </span>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";

export default {
    layout: AppLayout,
    setup() {
        const nomorHak = ref("");
        const filterJenisHak = ref("");
        const filterDesa = ref("");
        const services = ref([]);
        const errorMessage = ref("");
        const loading = ref(false);
        const hasSearched = ref(false);
        const updatingId = ref(null);
        const updatedIds = ref(new Set());

        let debounceTimer = null;

        const hasFilter = computed(
            () => nomorHak.value || filterJenisHak.value || filterDesa.value,
        );

        const filteredServices = computed(() =>
            services.value.filter((s) => {
                const matchJenis =
                    !filterJenisHak.value ||
                    s.land_book?.jenis_hak === filterJenisHak.value;
                const matchDesa =
                    !filterDesa.value ||
                    s.land_book?.desa_kecamatan
                        ?.toLowerCase()
                        .includes(filterDesa.value.toLowerCase());
                return matchJenis && matchDesa;
            }),
        );

        onMounted(async () => {
            try {
                await axios.get("/sanctum/csrf-cookie");
            } catch (e) {
                console.error("CSRF init error:", e);
            }

            // ← tambah ini, fetch semua data saat halaman dibuka
            await search();
        });

        const search = async () => {
            errorMessage.value = "";
            loading.value = true;
            hasSearched.value = true;
            services.value = [];

            try {
                const res = await axios.get("/search", {
                    params: { nomer_hak: nomorHak.value },
                });
                services.value = res.data.services || [];
            } catch (e) {
                errorMessage.value =
                    e.response?.data?.message ||
                    "Terjadi kesalahan saat mencari data.";
            } finally {
                loading.value = false;
            }
        };

        const liveSearch = () => {
            if (!nomorHak.value) return;
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(search, 500);
        };

        const clearSearch = () => {
            nomorHak.value = "";
            services.value = [];
            hasSearched.value = false;
            errorMessage.value = "";
        };

        const resetAll = () => {
            nomorHak.value = "";
            filterJenisHak.value = "";
            filterDesa.value = "";
            services.value = [];
            hasSearched.value = false;
            errorMessage.value = "";
        };

        const updateStatus = async (serviceId) => {
            if (!serviceId) return;
            updatingId.value = serviceId;
            errorMessage.value = "";

            try {
                const res = await axios.post("/update-status", {
                    service_id: serviceId,
                    status: "UPDATED",
                });
                updatedIds.value = new Set([...updatedIds.value, serviceId]);
            } catch (e) {
                errorMessage.value =
                    e.response?.data?.message || "Gagal memperbarui status.";
            } finally {
                updatingId.value = null;
            }
        };

        const statusClass = (status) => {
            if (!status) return "pill-gray";
            if (status.includes("SELESAI")) return "pill-green";
            if (status.includes("PROSES")) return "pill-amber";
            if (status.includes("FORWARD")) return "pill-blue";
            if (status.includes("UPDATED")) return "pill-green";
            return "pill-gray";
        };

        return {
            nomorHak,
            filterJenisHak,
            filterDesa,
            services,
            filteredServices,
            errorMessage,
            loading,
            hasSearched,
            updatingId,
            updatedIds,
            hasFilter,
            search,
            liveSearch,
            clearSearch,
            resetAll,
            updateStatus,
            statusClass,
        };
    },
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&family=DM+Mono:wght@400;500&display=swap");

*,
*::before,
*::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

.page {
    font-family: "DM Sans", sans-serif;
    background: #f5f4f0;
    min-height: 100vh;
    padding: 2.5rem 1.75rem;
    --ink: #1a1814;
    --ink2: #6b6860;
    --ink3: #a09e9a;
    --surface: #ffffff;
    --border: #e5e3de;
    --radius: 14px;
    --radius-sm: 8px;
}

/* ── Header ── */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1.5rem;
    margin-bottom: 1.75rem;
}

.eyebrow {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--ink3);
    margin-bottom: 0.45rem;
}

.eyebrow-pulse {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #f59e0b;
    animation: blink 2s ease-in-out infinite;
}

@keyframes blink {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.25;
    }
}

.page-title {
    font-size: 2rem;
    font-weight: 600;
    letter-spacing: -0.03em;
    color: var(--ink);
    margin-bottom: 0.4rem;
    line-height: 1.1;
}

.page-sub {
    font-size: 0.875rem;
    color: var(--ink2);
}

.header-badge {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1rem 1.5rem;
    text-align: center;
    min-width: 120px;
}

.badge-num {
    font-size: 2rem;
    font-weight: 600;
    letter-spacing: -0.04em;
    color: var(--ink);
    line-height: 1;
}

.badge-lbl {
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: var(--ink3);
    margin-top: 0.3rem;
}

/* ── Search Card ── */
.search-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.5rem 1.75rem;
    margin-bottom: 1.25rem;
}

.search-card-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 1.25rem;
}

.search-card-title .ti {
    font-size: 16px;
}

.search-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr auto;
    gap: 1rem;
    align-items: end;
}

/* ── Fields ── */
.field {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}
.field-btn {
    justify-content: flex-end;
}

.field-label {
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: var(--ink3);
}

.field-wrap {
    position: relative;
}

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
    padding: 0.65rem 2.5rem 0.65rem 2.25rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 0.875rem;
    font-family: "DM Sans", sans-serif;
    color: var(--ink);
    background: #fafaf8;
    outline: none;
    transition:
        border-color 0.15s,
        box-shadow 0.15s;
    appearance: none;
    -webkit-appearance: none;
}

.field-input:focus {
    border-color: var(--ink);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(26, 24, 20, 0.07);
}

.field-input::placeholder {
    color: var(--ink3);
}

.field-select {
    cursor: pointer;
}

.field-clear {
    position: absolute;
    right: 0.6rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--ink3);
    padding: 0.2rem;
    border-radius: 4px;
    display: flex;
    align-items: center;
    transition: color 0.15s;
}

.field-clear:hover {
    color: var(--ink);
}
.field-clear .ti {
    font-size: 14px;
}

/* ── Search Button ── */
.btn-search {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.5rem;
    background: var(--ink);
    color: #f5f4f0;
    border: none;
    border-radius: var(--radius-sm);
    font-size: 0.875rem;
    font-weight: 600;
    font-family: "DM Sans", sans-serif;
    cursor: pointer;
    white-space: nowrap;
    transition:
        opacity 0.15s,
        transform 0.1s;
}

.btn-search:hover:not(:disabled) {
    opacity: 0.88;
}
.btn-search:active:not(:disabled) {
    transform: scale(0.98);
}
.btn-search:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.btn-search .ti {
    font-size: 15px;
}

/* ── Active Filters ── */
.filter-active {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
}

.filter-active-lbl {
    font-size: 0.75rem;
    color: var(--ink3);
    font-weight: 500;
}

.filter-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--ink);
    background: #f0efe9;
    border: 1px solid var(--border);
    padding: 0.25rem 0.6rem;
    border-radius: 99px;
}

.filter-tag button {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--ink3);
    padding: 0;
    display: flex;
    line-height: 1;
    transition: color 0.12s;
}

.filter-tag button:hover {
    color: var(--ink);
}
.filter-tag .ti {
    font-size: 12px;
}

.filter-reset {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--ink2);
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.2rem 0.4rem;
    font-family: inherit;
    text-decoration: underline;
    text-underline-offset: 2px;
    transition: color 0.12s;
}

.filter-reset:hover {
    color: var(--ink);
}

/* ── Error Bar ── */
.error-bar {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    font-size: 0.85rem;
    font-weight: 500;
    padding: 0.85rem 1.25rem;
    border-radius: var(--radius-sm);
    margin-bottom: 1.25rem;
}

.error-bar .ti {
    font-size: 16px;
}

/* ── Skeleton ── */
.sk-row {
    display: flex;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
}

.sk {
    height: 14px;
    background: #ece9e3;
    border-radius: 4px;
    animation: shimmer 1.3s ease-in-out infinite;
}

.sk-sm {
    width: 60px;
}
.sk-md {
    width: 120px;
}
.sk-lg {
    flex: 1;
}
@keyframes shimmer {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.4;
    }
}

/* ── Empty Cards ── */
.empty-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 4rem 2rem;
    text-align: center;
}

.empty-ico {
    font-size: 2.5rem;
    color: var(--ink3);
    margin-bottom: 1rem;
}

.empty-ico--warn {
    color: #f59e0b;
}

.empty-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 0.4rem;
}

.empty-sub {
    font-size: 0.85rem;
    color: var(--ink2);
}

/* ── Table Card ── */
.table-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
}

.table-scroll {
    overflow-x: auto;
}

.tbl {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.tbl thead tr {
    background: #f8f7f3;
    border-bottom: 1px solid var(--border);
}

.tbl th {
    padding: 0.85rem 1rem;
    text-align: left;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: var(--ink3);
    white-space: nowrap;
}

.th-no,
.th-act {
    text-align: center;
}

.tbl-row {
    border-bottom: 1px solid var(--border);
    transition: background 0.12s;
}

.tbl-row:last-child {
    border-bottom: none;
}
.tbl-row:hover {
    background: #faf9f6;
}
.tbl-row--updated {
    background: #f0fdf4;
}

.tbl td {
    padding: 0.85rem 1rem;
    color: var(--ink2);
    vertical-align: middle;
}

.td-no {
    text-align: center;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--ink3);
    font-family: "DM Mono", monospace;
}

.td-act {
    text-align: center;
}

.td-lokasi {
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.td-pin {
    font-size: 13px;
    color: var(--ink3);
}

/* ── Chips ── */
.nohak-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: var(--ink);
    color: #f5f4f0;
    font-family: "DM Mono", monospace;
    font-size: 0.78rem;
    padding: 0.3rem 0.65rem;
    border-radius: 6px;
    white-space: nowrap;
}

.nohak-chip .ti {
    font-size: 12px;
}

.jenis-chip {
    display: inline-block;
    background: #f0efe9;
    color: var(--ink2);
    font-size: 0.78rem;
    font-weight: 500;
    padding: 0.28rem 0.65rem;
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
    padding: 0.3rem 0.7rem;
    border-radius: 99px;
    white-space: nowrap;
    border: 1px solid transparent;
}

.pill-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
}

.pill-green {
    background: #ecfdf5;
    color: #065f46;
    border-color: #a7f3d0;
}
.pill-green .pill-dot {
    background: #10b981;
}
.pill-amber {
    background: #fffbeb;
    color: #78350f;
    border-color: #fcd34d;
}
.pill-amber .pill-dot {
    background: #f59e0b;
}
.pill-blue {
    background: #eff6ff;
    color: #1e3a8a;
    border-color: #bfdbfe;
}
.pill-blue .pill-dot {
    background: #3b82f6;
}
.pill-gray {
    background: #f8f7f3;
    color: var(--ink2);
    border-color: var(--border);
}
.pill-gray .pill-dot {
    background: var(--ink3);
}

/* ── Update Button ── */
.btn-update {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.78rem;
    font-weight: 600;
    font-family: "DM Sans", sans-serif;
    padding: 0.45rem 1rem;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
    background: #f5f4f0;
    color: var(--ink);
    cursor: pointer;
    white-space: nowrap;
    transition:
        background 0.15s,
        color 0.15s,
        border-color 0.15s,
        transform 0.1s;
}

.btn-update:hover:not(:disabled) {
    background: var(--ink);
    color: #fff;
    border-color: var(--ink);
}

.btn-update:active:not(:disabled) {
    transform: scale(0.97);
}

.btn-update:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-update--done {
    background: #ecfdf5 !important;
    color: #065f46 !important;
    border-color: #a7f3d0 !important;
}

.btn-update .ti {
    font-size: 13px;
}

@keyframes spin-anim {
    to {
        transform: rotate(360deg);
    }
}
.spin {
    display: inline-block;
    animation: spin-anim 0.7s linear infinite;
}

/* ── Footer ── */
.tbl-footer {
    padding: 0.85rem 1.25rem;
    background: #f8f7f3;
    border-top: 1px solid var(--border);
}

.footer-info {
    font-size: 0.8rem;
    color: var(--ink3);
}
.footer-info strong {
    color: var(--ink);
    font-weight: 600;
}

/* ── Responsive ── */
@media (max-width: 900px) {
    .search-row {
        grid-template-columns: 1fr 1fr;
    }
    .field-btn {
        grid-column: span 2;
    }
    .page-header {
        flex-direction: column;
    }
}

@media (max-width: 600px) {
    .page {
        padding: 1.25rem 0.75rem;
    }
    .page-title {
        font-size: 1.6rem;
    }
    .search-row {
        grid-template-columns: 1fr;
    }
    .field-btn {
        grid-column: span 1;
    }
}
</style>
