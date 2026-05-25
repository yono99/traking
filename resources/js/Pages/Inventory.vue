<script setup>
import { ref, computed } from "vue";
import { onMounted } from "vue";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";
import UpdateModal from "@/Components/UpdateModal.vue";
import Kendala from "@/Components/Kendala.vue";

// ── Kirim WA Selesai ───────────────────────────────────────────
const kirimWaSelesai = async (service) => {
    try {
        const phone =
            "62" +
            (service.nomor_hp ?? "").replace(/^0+/, "").replace(/\D/g, "");
        if (!phone || phone === "62") return;

        const baseUrl = window.location.origin;
        const kode = service.kode_berkas ?? "-";
        const lacakUrl = `${baseUrl}/lacak/${kode}`;
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(lacakUrl)}&format=png`;

        const nama =
            service.land_book?.nama_pemegang ?? service.nama_pemohon ?? "-";
        const lokasi = service.land_book?.desa_kecamatan ?? "-";
        const jenisHak = service.land_book?.jenis_hak ?? "-";
        const nomerHak = service.land_book?.nomer_hak ?? "-";
        const tanggal = new Date().toLocaleDateString("id-ID", {
            day: "numeric",
            month: "long",
            year: "numeric",
        });

        const text =
            `✅ *Berkas Anda Telah Selesai Diproses*\n\n` +
            `📋 Kode Berkas: *${kode}*\n` +
            `👤 Nama: ${nama}\n` +
            `📍 Lokasi: ${lokasi}\n` +
            `📄 Jenis Hak: ${jenisHak} / No. ${nomerHak}\n` +
            `📅 Tanggal Selesai: ${tanggal}\n\n` +
            `🎉 Permohonan Anda telah selesai dan siap diambil.\n\n` +
            `🔗 Cek status berkas:\n${lacakUrl}\n\n` +
            `Terima kasih.`;

        await axios.post("/wa/send", { to: phone, text });
        await axios.post("/wa/send-image", {
            to: phone,
            text: `🔲 QR Code berkas *${kode}*\nScan untuk membuka: ${lacakUrl}`,
            image_url: qrUrl,
        });
    } catch (err) {
        console.warn(
            "Gagal kirim WA selesai:",
            err?.response?.data?.message ?? err.message,
        );
    }
};

const props = defineProps({
    services: { type: Array, required: true },
    user: { type: Object, required: true },
});

const showUpdateModal = ref(false);
const showKendala = ref(false);
const selectedItem = ref(null);
const alertMessage = ref("");
const alertType = ref("");

// Image / PDF viewer
const showImageModal = ref(false);
const imageModalSrc = ref("");
const imageModalTitle = ref("");
const isPdf = ref(false);
const imgError = ref(false);
const zoomLevel = ref(1);
const MIN_ZOOM = 0.5;
const MAX_ZOOM = 4;
const ZOOM_STEP = 0.25;

const nextStatus = {
    "PROSES LOKET": "FORWARD BUKU TANAH",
    "PROSES BUKU TANAH": "FORWARD PENGUKURAN",
    "PROSES PENGUKURAN": "FORWARD VALIDASI BIDANG",
    "PROSES VALIDASI BIDANG": "FORWARD VALIDASI BUKU TANAH",
    "PROSES VALIDASI BUKU TANAH": "FORWARD LOKET PENYERAHAN",
    "PROSES LOKET PENYERAHAN": "SELESAI DISERAHKAN",
};
const rejectStatus = {
    "PROSES LOKET": null,
    "PROSES BUKU TANAH": "FORWARD LOKET",
    "PROSES PENGUKURAN": "FORWARD BUKU TANAH",
    "PROSES VALIDASI BIDANG": "FORWARD PENGUKURAN",
    "PROSES VALIDASI BUKU TANAH": "FORWARD VALIDASI BIDANG",
    "PROSES LOKET PENYERAHAN": "FORWARD VALIDASI BUKU TANAH",
};

const badgeConfig = {
    "PROSES LOKET": { label: "Loket", cls: "badge-loket" },
    "PROSES BUKU TANAH": { label: "Buku Tanah", cls: "badge-buku" },
    "PROSES PENGUKURAN": { label: "Pengukuran", cls: "badge-ukur" },
    "PROSES VALIDASI BIDANG": { label: "Validasi Bidang", cls: "badge-validasi" },
    "PROSES VALIDASI BUKU TANAH": { label: "Validasi BT", cls: "badge-validasi" },
    "PROSES LOKET PENYERAHAN": { label: "Loket Penyerahan", cls: "badge-loket" },
    "SELESAI DISERAHKAN": { label: "Selesai", cls: "badge-selesai" },
};

onMounted(async () => {
    try {
        await axios.get("/sanctum/csrf-cookie");
    } catch {
        alertMessage.value = "Gagal memuat token CSRF.";
        alertType.value = "error";
    }
});

const updateStatus = async (serviceId, newStatus) => {
    try {
        const response = await axios.post(
            `/inventory/update-status/${serviceId}`,
            { status: newStatus },
        );
        alertMessage.value = response.data.message || "Status berhasil diperbarui.";
        alertType.value = "success";
        setTimeout(() => { alertMessage.value = ""; }, 3000);
        window.location.reload();
    } catch (error) {
        alertMessage.value = error.response?.data?.message || "Gagal memperbarui status.";
        alertType.value = "error";
    }
};

const handleNext = async (service) => {
    const status = nextStatus[service.status];
    if (!status) return alert("Tidak ada tahap selanjutnya.");
    if (service.status === "PROSES LOKET PENYERAHAN") {
        await kirimWaSelesai(service);
    }
    await updateStatus(service.id, status);
};

const handleTolak = async (service) => {
    const status = rejectStatus[service.status];
    if (status === null || status === undefined)
        return alert("Berkas tidak dapat ditolak di tahap ini.");
    await updateStatus(service.id, status);
};

const openUpdateModal = (s) => {
    selectedItem.value = s;
    showUpdateModal.value = true;
};
const openKendalaModal = (s) => {
    selectedItem.value = s;
    showKendala.value = true;
};
const closeUpdateModal = () => {
    showUpdateModal.value = false;
    selectedItem.value = null;
    window.location.reload();
};
const closeKendalaModal = () => {
    showKendala.value = false;
    selectedItem.value = null;
    window.location.reload();
};

// ── File viewer — support gambar & PDF ──────────────────────────
const openImageModal = (filePath, title) => {
    if (!filePath) return;

    // Normalize path: hapus prefix ganda jika ada
    let cleanPath = filePath
        .replace(/^\/+/, "")                  // hapus leading slash
        .replace(/^storage\//, "")            // hapus "storage/" di awal jika ada
        .replace(/^public\//, "");            // hapus "public/" di awal jika ada

    imageModalSrc.value = `/storage/${cleanPath}`;
    imageModalTitle.value = title || "File Dokumen";
    isPdf.value = cleanPath.toLowerCase().endsWith(".pdf");
    imgError.value = false;
    zoomLevel.value = 1;
    showImageModal.value = true;
};

const closeImageModal = () => {
    showImageModal.value = false;
    zoomLevel.value = 1;
    imgError.value = false;
};
const zoomIn = () => {
    zoomLevel.value = Math.min(MAX_ZOOM, +(zoomLevel.value + ZOOM_STEP).toFixed(2));
};
const zoomOut = () => {
    zoomLevel.value = Math.max(MIN_ZOOM, +(zoomLevel.value - ZOOM_STEP).toFixed(2));
};
const resetZoom = () => { zoomLevel.value = 1; };
const handleWheel = (e) => {
    e.preventDefault();
    e.deltaY < 0 ? zoomIn() : zoomOut();
};
const handleBackdrop = (e) => {
    if (e.target === e.currentTarget) closeImageModal();
};
const onImgError = () => { imgError.value = true; };
const getBadge = (status) =>
    badgeConfig[status] || { label: status, cls: "badge-loket" };
</script>

<template>
    <AppLayout>
        <div class="inv-page">
            <!-- Alert -->
            <Transition name="inv-fade">
                <div
                    v-if="alertMessage"
                    :class="[
                        'inv-alert',
                        alertType === 'success' ? 'inv-alert-success' : 'inv-alert-error',
                    ]"
                >
                    <svg v-if="alertType === 'success'" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <span>{{ alertMessage }}</span>
                </div>
            </Transition>

            <!-- Main Card -->
            <div class="inv-card">
                <div class="inv-card-header">
                    <div class="inv-card-header-left">
                        <span class="inv-section-num">01</span>
                        <div>
                            <p class="inv-section-title">Daftar Berkas</p>
                            <p class="inv-section-sub">Berkas aktif sesuai unit Anda</p>
                        </div>
                    </div>
                </div>

                <div class="inv-table-wrap">
                    <table class="inv-table">
                        <thead>
                            <tr>
                                <th style="width: 44px">No</th>
                                <th>Kode Berkas</th>
                                <th>Nomer Hak</th>
                                <th>Jenis Hak</th>
                                <th>Desa / Kecamatan</th>
                                <th>Status</th>
                                <th style="width: 80px">File</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="services.length === 0">
                                <td colspan="8">
                                    <div class="inv-empty">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                                        </svg>
                                        <p>Tidak ada data berkas.</p>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="(service, index) in services" :key="service.id">
                                <td class="inv-td-muted">{{ index + 1 }}</td>
                                <td class="inv-td-muted">{{ service.kode_berkas || "—" }}</td>
                                <td class="inv-td-bold">{{ service.land_book?.nomer_hak || "—" }}</td>
                                <td class="inv-td-muted">{{ service.land_book?.jenis_hak || "—" }}</td>
                                <td class="inv-td-muted">{{ service.land_book?.desa_kecamatan || "—" }}</td>
                                <td>
                                    <span :class="['inv-badge', getBadge(service.status).cls]">
                                        <span class="inv-badge-dot"></span>
                                        {{ getBadge(service.status).label }}
                                    </span>
                                </td>
                                <td>
                                    <button
                                        v-if="service.land_book?.file_path"
                                        @click="openImageModal(service.land_book.file_path, service.land_book?.nomer_hak)"
                                        class="inv-btn inv-btn-file"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="18" height="18" rx="2" />
                                            <circle cx="8.5" cy="8.5" r="1.5" />
                                            <polyline points="21 15 16 10 5 21" />
                                        </svg>
                                        Lihat
                                    </button>
                                    <span v-else class="inv-no-file">—</span>
                                </td>
                                <td>
                                    <div class="inv-actions">
                                        <button
                                            v-if="
                                                $page.props.auth?.user?.unit === 'loket' ||
                                                $page.props.auth?.user?.unit === 'bukutanah' ||
                                                $page.props.auth?.user?.role === 'admin'
                                            "
                                            @click="openUpdateModal(service)"
                                            class="inv-btn inv-btn-update"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                            Update
                                        </button>
                                        <button @click="openKendalaModal(service)" class="inv-btn inv-btn-kendala">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                                <line x1="12" y1="9" x2="12" y2="13" />
                                                <line x1="12" y1="17" x2="12.01" y2="17" />
                                            </svg>
                                            Kendala
                                        </button>
                                        <button @click="handleNext(service)" class="inv-btn inv-btn-next">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="5" y1="12" x2="19" y2="12" />
                                                <polyline points="12 5 19 12 12 19" />
                                            </svg>
                                            Next
                                        </button>
                                        <button
                                            @click="handleTolak(service)"
                                            :disabled="rejectStatus[service.status] === null || rejectStatus[service.status] === undefined"
                                            class="inv-btn inv-btn-tolak"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="18" y1="6" x2="6" y2="18" />
                                                <line x1="6" y1="6" x2="18" y2="18" />
                                            </svg>
                                            Tolak
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="inv-card-footer">
                    <span>Menampilkan {{ services.length }} berkas</span>
                    <span class="inv-footer-hint">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        Field wajib harus diisi
                    </span>
                </div>
            </div>

            <!-- Modals -->
            <UpdateModal
                :show="showUpdateModal"
                :service-id="selectedItem?.id"
                :service="selectedItem"
                :user="user"
                @close="closeUpdateModal"
            />
            <Kendala
                :show="showKendala"
                :service-id="selectedItem?.id"
                :service="selectedItem"
                :user="user"
                @close="closeKendalaModal"
            />
        </div>

        <!-- ── File Viewer Modal ── -->
        <Teleport to="body">
            <Transition name="inv-modal">
                <div
                    v-if="showImageModal"
                    class="inv-modal-backdrop"
                    @click="handleBackdrop"
                    @wheel.prevent="handleWheel"
                >
                    <div class="inv-modal-box">
                        <!-- Header -->
                        <div class="inv-modal-header">
                            <span class="inv-modal-title">{{ imageModalTitle }}</span>
                            <div class="inv-modal-tools">
                                <!-- Zoom controls hanya untuk gambar -->
                                <template v-if="!isPdf && !imgError">
                                    <button @click="zoomOut" :disabled="zoomLevel <= MIN_ZOOM" class="inv-tool-btn" title="Perkecil">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="5" y1="12" x2="19" y2="12" />
                                        </svg>
                                    </button>
                                    <button @click="resetZoom" class="inv-zoom-display">
                                        {{ Math.round(zoomLevel * 100) }}%
                                    </button>
                                    <button @click="zoomIn" :disabled="zoomLevel >= MAX_ZOOM" class="inv-tool-btn" title="Perbesar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="12" y1="5" x2="12" y2="19" />
                                            <line x1="5" y1="12" x2="19" y2="12" />
                                        </svg>
                                    </button>
                                    <div class="inv-divider"></div>
                                </template>
                                <a :href="imageModalSrc" target="_blank" rel="noopener noreferrer" class="inv-tool-btn" title="Buka di tab baru">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                                        <polyline points="15 3 21 3 21 9" />
                                        <line x1="10" y1="14" x2="21" y2="3" />
                                    </svg>
                                </a>
                                <button @click="closeImageModal" class="inv-tool-btn inv-tool-close" title="Tutup">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="18" y1="6" x2="6" y2="18" />
                                        <line x1="6" y1="6" x2="18" y2="18" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="inv-modal-img-wrap">
                            <!-- PDF -->
                            <iframe
                                v-if="isPdf"
                                :src="imageModalSrc"
                                class="inv-modal-pdf"
                                frameborder="0"
                            ></iframe>

                            <!-- Gambar normal -->
                            <img
                                v-else-if="!imgError"
                                :src="imageModalSrc"
                                :alt="imageModalTitle"
                                :style="{
                                    transform: `scale(${zoomLevel})`,
                                    transformOrigin: 'center center',
                                    transition: 'transform 0.15s ease',
                                }"
                                class="inv-modal-img"
                                draggable="false"
                                @error="onImgError"
                            />

                            <!-- Fallback jika gambar gagal load -->
                            <div v-else class="inv-modal-error">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="12" y1="18" x2="12" y2="12" />
                                    <line x1="9" y1="15" x2="15" y2="15" />
                                </svg>
                                <p>Gagal memuat file.</p>
                                <a :href="imageModalSrc" target="_blank" rel="noopener noreferrer" class="inv-modal-error-link">
                                    Buka langsung di tab baru →
                                </a>
                            </div>
                        </div>

                        <div class="inv-modal-footer">
                            <span v-if="isPdf">Gunakan tombol buka tab baru untuk tampilan penuh</span>
                            <span v-else>Scroll untuk zoom · Klik di luar untuk tutup</span>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AppLayout>
</template>

<style scoped>
.inv-modal-pdf {
    width: 100%;
    height: 70vh;
    border: none;
    border-radius: 4px;
}

.inv-modal-error {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 48px 24px;
    color: #6b7280;
}

.inv-modal-error p {
    font-size: 14px;
    margin: 0;
}

.inv-modal-error-link {
    font-size: 13px;
    color: #6366f1;
    text-decoration: underline;
}
</style>

<style scoped>
.inv-page {
    background: #f5f6fa;
    min-height: 100vh;
    padding: 2rem 1.5rem;
}

/* Header */
.inv-header-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    gap: 1rem;
    flex-wrap: wrap;
}
.inv-page-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 4px;
}
.inv-page-sub {
    font-size: 0.84rem;
    color: #6b7280;
}

/* Stepper */
.inv-stepper {
    display: flex;
    align-items: center;
    gap: 8px;
}
.inv-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}
.inv-step-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    background: #e5e7eb;
    color: #9ca3af;
}
.inv-step-circle.active {
    background: #1d4ed8;
    color: #fff;
}
.inv-step-label {
    font-size: 11px;
    color: #9ca3af;
}
.inv-step-label.active {
    color: #1d4ed8;
    font-weight: 700;
}
.inv-step-line {
    width: 48px;
    height: 1.5px;
    background: #e5e7eb;
    margin-bottom: 14px;
}

/* Alert */
.inv-alert {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 1rem;
}
.inv-alert-success {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}
.inv-alert-error {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

/* Card */
.inv-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    overflow: hidden;
}
.inv-card-header {
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.inv-card-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.inv-section-num {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: #eff6ff;
    color: #1d4ed8;
    font-size: 12px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.inv-section-title {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
}
.inv-section-sub {
    font-size: 12px;
    color: #9ca3af;
}
.inv-search-box {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 7px 12px;
    font-size: 13px;
    color: #9ca3af;
}

/* Table */
.inv-table-wrap {
    overflow-x: auto;
}
.inv-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.inv-table thead tr {
    background: #f9fafb;
}
.inv-table th {
    padding: 10px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 11px;
    color: #6b7280;
    border-bottom: 1px solid #f3f4f6;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.inv-table tbody tr {
    border-bottom: 1px solid #f9fafb;
    transition: background 0.1s;
}
.inv-table tbody tr:last-child {
    border-bottom: none;
}
.inv-table tbody tr:hover {
    background: #fafbff;
}
.inv-table td {
    padding: 13px 16px;
    vertical-align: middle;
    color: #111827;
}
.inv-td-muted {
    color: #6b7280 !important;
}
.inv-td-bold {
    font-weight: 600;
}

/* Badge */
.inv-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 600;
    white-space: nowrap;
}
.inv-badge-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    display: inline-block;
    flex-shrink: 0;
}
.badge-loket {
    background: #eff6ff;
    color: #1d4ed8;
}
.badge-buku {
    background: #fff7ed;
    color: #c2410c;
}
.badge-ukur {
    background: #f0fdf4;
    color: #166534;
}
.badge-validasi {
    background: #fdf4ff;
    color: #7e22ce;
}
.badge-selesai {
    background: #ecfdf5;
    color: #065f46;
}

/* Buttons */
.inv-actions {
    display: flex;
    gap: 6px;
    flex-wrap: nowrap;
}
.inv-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 11px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid transparent;
    cursor: pointer;
    transition:
        opacity 0.12s,
        transform 0.08s;
    white-space: nowrap;
    line-height: 1;
}
.inv-btn:active {
    transform: scale(0.96);
}
.inv-btn:disabled {
    opacity: 0.38;
    cursor: not-allowed;
    transform: none;
}
.inv-btn-update {
    background: #eff6ff;
    color: #1d4ed8;
    border-color: #bfdbfe;
}
.inv-btn-update:hover {
    background: #dbeafe;
}
.inv-btn-file {
    background: #f5f3ff;
    color: #6d28d9;
    border-color: #ddd6fe;
}
.inv-btn-file:hover {
    background: #ede9fe;
}
.inv-btn-next {
    background: #ecfdf5;
    color: #065f46;
    border-color: #a7f3d0;
}
.inv-btn-next:hover {
    background: #d1fae5;
}
.inv-btn-tolak {
    background: #fef2f2;
    color: #991b1b;
    border-color: #fecaca;
}
.inv-btn-tolak:hover:not(:disabled) {
    background: #fee2e2;
}
.inv-btn-kendala {
    background: #fff7ed;
    color: #c2410c;
    border-color: #fed7aa;
}
.inv-btn-kendala:hover {
    background: #ffedd5;
}
.inv-no-file {
    font-size: 12px;
    color: #d1d5db;
}

/* Footer */
.inv-card-footer {
    padding: 11px 1.5rem;
    border-top: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12px;
    color: #9ca3af;
}

/* Empty */
.inv-empty {
    text-align: center;
    padding: 3rem 1rem;
    color: #9ca3af;
    font-size: 13px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}

/* Modal */
.inv-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.72);
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    backdrop-filter: blur(4px);
}
.inv-modal-box {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    max-width: 780px;
    width: 100%;
    display: flex;
    flex-direction: column;
    max-height: 90vh;
}
.inv-modal-header {
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.inv-modal-title {
    font-size: 13px;
    font-weight: 600;
    color: #111827;
}
.inv-modal-tools {
    display: flex;
    align-items: center;
    gap: 6px;
}
.inv-tool-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    color: #374151;
    cursor: pointer;
    transition: background 0.1s;
    text-decoration: none;
}
.inv-tool-btn:hover {
    background: #f3f4f6;
}
.inv-tool-btn:disabled {
    opacity: 0.38;
    cursor: not-allowed;
}
.inv-tool-close:hover {
    background: #fee2e2;
    color: #991b1b;
    border-color: #fecaca;
}
.inv-zoom-display {
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 5px 10px;
    min-width: 50px;
    text-align: center;
    cursor: pointer;
}
.inv-zoom-display:hover {
    background: #f3f4f6;
}
.inv-divider {
    width: 1px;
    height: 18px;
    background: #e5e7eb;
}
.inv-modal-img-wrap {
    overflow: auto;
    flex: 1;
    background: #1f2937;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}
.inv-modal-img {
    max-width: none;
    border-radius: 6px;
    display: block;
    user-select: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
}
.inv-modal-footer {
    padding: 9px 16px;
    border-top: 1px solid #f3f4f6;
    font-size: 11px;
    color: #9ca3af;
    text-align: center;
    flex-shrink: 0;
}

/* Transitions */
.inv-fade-enter-active,
.inv-fade-leave-active {
    transition: opacity 0.2s;
}
.inv-fade-enter-from,
.inv-fade-leave-to {
    opacity: 0;
}
.inv-modal-enter-active {
    transition: opacity 0.2s ease;
}
.inv-modal-leave-active {
    transition: opacity 0.15s ease;
}
.inv-modal-enter-from,
.inv-modal-leave-to {
    opacity: 0;
}
</style>
