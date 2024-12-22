<script setup>
import { ref, computed } from "vue";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";
import UpdateModal from "@/Components/UpdateModal.vue";
import Kendala from "@/Components/Kendala.vue";

// Props definition
const props = defineProps({
    services: {
        type: Array,
        required: true,
    },
    user: {
        type: Object,
        required: true,
    },
});

// Reactive references
const showUpdateModal = ref(false);
const showKendala = ref(false);
const selectedItem = ref(null);
const alertMessage = ref('');
const alertType = ref('');

// Status buttons configuration
const buttons = computed(() => ({
    verifikator: [
        "FORWARD PENGUKURAN",
        "FORWARD CARI BT",
        "FORWARD BENSUS DISPOSISI",
        "FORWARD SPS",
    ],
    sps: ["FORWARD BENSUS", "FORWARD BENSUS DISPOSISI"],
    bensus: ["FORWARD PELAKSANA", "SELESAI INFO DISPOSISI"],
    pelaksana: [
        "FORWARD TTE PRODUK LAYANAN",
        "FORWARD ALIH MEDIA SUEL",
        "FORWARD LOKET PENYERAHAN",
    ],
    pelaksana_bn: [
        "FORWARD TTE PRODUK LAYANAN",
        "FORWARD ALIH MEDIA SUEL",
        "FORWARD LOKET PENYERAHAN",
    ],
    pelaksana_ph: [
        "FORWARD TTE PRODUK LAYANAN",
        "FORWARD ALIH MEDIA SUEL",
        "FORWARD LOKET PENYERAHAN",
    ],
    pelaksana_roya: [
        "FORWARD TTE PRODUK LAYANAN",
        "FORWARD ALIH MEDIA SUEL",
        "FORWARD LOKET PENYERAHAN",
    ],
    pelaksana_ph_ruko: [
        "FORWARD TTE PRODUK LAYANAN",
        "FORWARD ALIH MEDIA SUEL",
        "FORWARD LOKET PENYERAHAN",
    ],
    pelaksana_sk: [
        "FORWARD TTE PRODUK LAYANAN",
        "FORWARD ALIH MEDIA SUEL",
        "FORWARD LOKET PENYERAHAN",
    ],
    pengukuran: ["FORWARD VERIFIKASI LANJUTAN", "FORWARD ALIH MEDIA BTEL"],
    bukutanah: [
        "FORWARD VERIFIKATOR CEK SYARAT",
        "FORWARD PENGESAHAN ALIH MEDIA BTEL",
    ],
    pengesahan: ["FORWARD PELAKSANA BUAT CATATAN"],

    TTE_PRODUK_LAYANAN: ["FORWARD PELAKSANA CETAK SERTEL"],
    LOKET_PENYERAHAN: ["SELESAI DISERAHKAN"],
}));

// Status rules untuk visibility tombol
const hideButtonRules = computed(() => ({
    "PROSES VERIFIKASI LANJUTAN": ["FORWARD PENGUKURAN"],
    "PROSES VERIFIKASI CROSSCHECK": ["FORWARD PENGUKURAN", "FORWARD CARI BT"],
    "PROSES VERIFIKASI": ["FORWARD SPS"],
    "PROSES MEMPERBAHARUI": ["FORWARD ALIH MEDIA BTEL"],
    "PROSES ALIH MEDIA SUEL": ["FORWARD VERIFIKASI LANJUTAN"],
    "PROSES CARI BT": ["FORWARD PENGESAHAN ALIH MEDIA BTEL"],
    "PROSES ALIH MEDIA BTEL": ["FORWARD VERIFIKATOR CEK SYARAT"],
    "PROSES BENSUS": ["SELESAI INFO DISPOSISI"],
    "PROSES INFO DISPOSISI": ["FORWARD PELAKSANA"],
    "PROSES PELAKSANA": [
        "FORWARD TTE PRODUK LAYANAN",
        "FORWARD LOKET PENYERAHAN",
    ],
    "PROSES PELAKSANA BUAT CATATAN": [
        "FORWARD ALIH MEDIA SUEL",
        "FORWARD LOKET PENYERAHAN",
    ],
    "PROSES CETAK SERTEL": [
        "FORWARD TTE PRODUK LAYANAN",
        "FORWARD ALIH MEDIA SUEL",
    ],
}));

// Methods
const updateStatus = async (serviceId, newStatus) => {
    try {
        console.log("Updating status:", { serviceId, newStatus });

        await axios.post(`/inventory/update-status/${serviceId}`, {
            status: newStatus,
            _token: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        });

        alert("Status berhasil diperbarui.");
        await loadServices();
    } catch (error) {
        console.error("Error updating status:", error);
        alert(error.response?.data?.message || "Error updating status");
    }
};

const isButtonVisible = (service, buttonType) => {
    if (!service || !buttonType) return false;

    const status = service.status;
    const buttonsToHide = hideButtonRules.value[status] || [];
    return !buttonsToHide.includes(buttonType);
};

const loadServices = async () => {
    window.location.reload();
};

const openUpdateModal = (service) => {
    selectedItem.value = service;
    showUpdateModal.value = true;
};

const openKendalaModal = (service) => {
    selectedItem.value = service;
    showKendala.value = true;
};

const closeUpdateModal = () => {
    showUpdateModal.value = false;

    selectedItem.value = null;
    loadServices();
};
const closeKendalaModal = () => {

    showKendala.value = false;
    selectedItem.value = null;
    loadServices();
};

const submitForm = async () => {
    try {
        console.log("Submitting form with data:", {
            status: selectedItem.value.status || "",
            remarks: selectedItem.value.remarks || "",
            PNBP: selectedItem.value.PNBP || "",
            nomor_hp: selectedItem.value.nomor_hp || "",
            nomer_hak: selectedItem.value.nomer_hak || "",
            jenis_hak: selectedItem.value.jenis_hak || "",
            desa_kecamatan: selectedItem.value.desa_kecamatan || "",
            status_alih_media: selectedItem.value.status_alih_media || "",
        });

        await axios.post(`/inventory/update-status/${selectedItem.value.id}`, {
            status: selectedItem.value.status || "",
            remarks: selectedItem.value.remarks || "",
            PNBP: selectedItem.value.PNBP || "",
            nomor_hp: selectedItem.value.nomor_hp || "",
            nomer_hak: selectedItem.value.nomer_hak || "",
            jenis_hak: selectedItem.value.jenis_hak || "",
            desa_kecamatan: selectedItem.value.desa_kecamatan || "",
            status_alih_media: selectedItem.value.status_alih_media || "",
        });

        // Jika pengiriman berhasil, tampilkan alert sukses
        alertMessage.value = response.data.message || "Data berhasil diupdate";
        alertType.value = 'success';

        closeModal();

        // Menghilangkan alert setelah 5 detik
        setTimeout(() => {
            alertMessage.value = '';
        }, 5000); // 5000ms = 5 detik
    } catch (error) {
        console.error("Error submitting form:", error);
        // Jika terjadi kesalahan, tampilkan alert error
        alertMessage.value = error.response?.data?.message || "Gagal mengupdate data";
        alertType.value = 'error';
        // alert(error.response?.data?.message || "Gagal mengupdate data");

        // Menghilangkan alert setelah 5 detik
        setTimeout(() => {
            alertMessage.value = '';
        }, 5000); // 5000ms = 5 detik
    }
};
</script>

<template>
    <AppLayout>
        <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1
                        class="text-2xl font-semibold text-gray-900 dark:text-gray-200"
                    >
                        Inventory
                    </h1>
                </div>
            </div>
            <!-- Alert Message Floating di kanan atas -->
            <div
                v-if="alertMessage"
                :class="[
                    'p-4 mb-4 text-sm rounded-md fixed top-20 right-4 z-50',
                    alertType === 'success' ? 'bg-green-100 text-green-800' : '',
                    alertType === 'error' ? 'bg-red-100 text-red-800' : '',
                    'transition-all duration-300 ease-in-out transform opacity-100'
                ]"
                style="max-width: 300px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            >
                <strong>{{ alertType === 'success' ? 'Sukses!' : 'Error!' }}</strong>
                <p>{{ alertMessage }}</p>
            </div>

            <div class="mt-8 flex flex-col">
                <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div
                        class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8"
                    >
                        <div
                            class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg"
                        >
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                        >
                                            No
                                        </th>
                                        <th
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                        >
                                            Nomer hak
                                        </th>
                                        <th
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                        >
                                            Jenis Hak
                                        </th>
                                        <th
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                        >
                                            Desa - Kecamatan
                                        </th>
                                        <th
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                        >
                                            Status
                                        </th>
                                        <th
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                        >
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="divide-y divide-gray-200 bg-white"
                                >
                                    <tr v-if="services.length === 0">
                                        <td
                                            colspan="6"
                                            class="px-3 py-4 text-sm text-gray-500 text-center"
                                        >
                                            Tidak ada data yang ditemukan.
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="(service, index) in services"
                                        :key="service.id"
                                    >
                                        <td
                                            class="px-3 py-4 text-sm text-gray-500"
                                        >
                                            {{ index + 1 }}
                                        </td>
                                        <td
                                            class="px-3 py-4 text-sm text-gray-500"
                                        >
                                            {{
                                                service.land_book?.nomer_hak ||
                                                "-"
                                            }}
                                        </td>
                                        <td
                                            class="px-3 py-4 text-sm text-gray-500"
                                        >
                                            {{
                                                service.land_book?.jenis_hak ||
                                                "-"
                                            }}
                                        </td>
                                        <td
                                            class="px-3 py-4 text-sm text-gray-500"
                                        >
                                            {{
                                                service.land_book
                                                    ?.desa_kecamatan || "-"
                                            }}
                                        </td>
                                        <td
                                            class="px-3 py-4 text-sm text-gray-500"
                                        >
                                            {{ service.status }}
                                        </td>
                                        <td
                                            class="px-3 py-4 text-sm text-gray-500"
                                        >
                                            <div class="flex flex-wrap gap-2">
                                                <button
                                                    @click="openUpdateModal(service)"
                                                    class="inline-flex items-center w-fit rounded-md bg-blue-600 px-2.5 py-1.5 text-sm font-semibold text-white hover:bg-blue-500"
                                                >
                                                    Update
                                                </button>
                                                <button
                                                    @click="openKendalaModal(service)"
                                                    class="inline-flex items-center w-fit rounded-md bg-blue-600 px-2.5 py-1.5 text-sm font-semibold text-white hover:bg-blue-500"
                                                >
                                                    Kendala
                                                </button>
                                                <div
                                                    v-if="buttons[user.unit]"
                                                    class="flex flex-wrap gap-2"
                                                >
                                                    <button
                                                        v-for="button in buttons[
                                                            user.unit
                                                        ]"
                                                        :key="button"
                                                        v-show="
                                                            isButtonVisible(
                                                                service,
                                                                button
                                                            )
                                                        "
                                                        @click="
                                                            updateStatus(
                                                                service.id,
                                                                button
                                                            )
                                                        "
                                                        class="inline-flex items-center rounded-md bg-blue-600 px-2.5 py-1.5 text-sm font-semibold text-white hover:bg-blue-500"
                                                    >
                                                        {{ button }}
                                                    </button>

                                                </div>
                                                <div v-else>
                                                    <span class="text-gray-500"
                                                        >No actions
                                                        available</span
                                                    >
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

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
    </AppLayout>
</template>
