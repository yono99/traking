<script>
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";

export default {
    layout: AppLayout,

    props: {
        services: Array,
        user: Object,
    },

    data() {
        return {
            buttons: {
                verifikator: [
                    "FORWARD PENGUKURAN",
                    "FORWARD CARI BT",
                    "FORWARD BENSUS DISPOSISI",
                    "FORWARD SPS",
                ],
                sps: ["FORWARD BENSUS"],
                bensus: ["FORWARD PELAKSANA", "SELESAI INFO DISPOSISI"],
                pelaksana: [
                    "FORWARD PARAF",
                    "FORWARD ALIH MEDIA SUEL",
                    "FORWARD LOKET PENYERAHAN",
                ],
                pelaksana_bn: [
                    "FORWARD PARAF",
                    "FORWARD ALIH MEDIA SUEL",
                    "FORWARD LOKET PENYERAHAN",
                ],
                pelaksana_ph: [
                    "FORWARD PARAF",
                    "FORWARD ALIH MEDIA SUEL",
                    "FORWARD LOKET PENYERAHAN",
                ],
                pelaksana_roya: [
                    "FORWARD PARAF",
                    "FORWARD ALIH MEDIA SUEL",
                    "FORWARD LOKET PENYERAHAN",
                ],
                pelaksana_ph_ruko: [
                    "FORWARD PARAF",
                    "FORWARD ALIH MEDIA SUEL",
                    "FORWARD LOKET PENYERAHAN",
                ],
                pelaksana_sk: [
                    "FORWARD PARAF",
                    "FORWARD ALIH MEDIA SUEL",
                    "FORWARD LOKET PENYERAHAN",
                ],
                pengukuran: [
                    "FORWARD VERIFIKASI LANJUTAN",
                    "FORWARD ALIH MEDIA BTEL",
                ],
                bukutanah: [
                    "FORWARD VERIFIKATOR CEK SYARAT",
                    "FORWARD PENGESAHAN ALIH MEDIA BTEL",
                ],
                pengesahan: ["FORWARD PARAF"],
                paraf: ["FORWARD TTE PRODUK LAYANAN"],
                TTE_PRODUK_LAYANAN: ["FORWARD PELAKSANA CETAK SERTEL"],
                LOKET_PENYERAHAN: ["SELESAI DISERAHKAN"],
            },
        };
    },

    methods: {
        // Fungsi untuk memperbarui status layanan
        async updateStatus(serviceId, newStatus) {
            try {
                const response = await axios.post(
                    `/inventory/update-status/${serviceId}`,
                    { status: newStatus }
                );
                alert(response.data.message); // Tampilkan pesan sukses
                await this.loadServices();
            } catch (error) {
                console.error(error);
                alert("Error updating status");
            }
        },

        // Fungsi untuk memeriksa apakah tombol harus disembunyikan berdasarkan status
        isButtonVisible(service, buttonType) {
            const status = service.status;

            // Mapping status dan button yang harus di-hide
            const hideButtonRules = {
                "PROSES VERIFIKASI LANJUTAN": ["FORWARD PENGUKURAN"],
                "PROSES VERIFIKASI CROSSCHECK": [
                    "FORWARD PENGUKURAN",
                    "FORWARD CARI BT",
                ],
                "PROSES VERIFIKASI": ["FORWARD SPS"],
                "PROSES MEMPERBAHARUI": ["FORWARD ALIH MEDIA BTEL"],
                "PROSES ALIH MEDIA SUEL": ["FORWARD VERIFIKASI LANJUTAN"],
                "PROSES CARI BT": ["FORWARD PENGESAHAN ALIH MEDIA BTEL"],
                "PROSES ALIH MEDIA BTEL": ["FORWARD VERIFIKATOR CEK SYARAT"],
                "PROSES BENSUS": ["SELESAI INFO DISPOSISI"],
                "PROSES INFO DISPOSISI": ["FORWARD PELAKSANA"],
                "PROSES PELAKSANA": [
                    "FORWARD PARAF",
                    "FORWARD LOKET PENYERAHAN",
                ],
                "PROSES PELAKSANA BUAT CATATAN": [
                    "FORWARD ALIH MEDIA SUEL",
                    "FORWARD LOKET PENYERAHAN",
                ],
                "PROSES CETAK SERTEL": [
                    "FORWARD PARAF",
                    "FORWARD ALIH MEDIA SUEL",
                ],
            };

            // Cek apakah button harus di-hide berdasarkan status
            const buttonsToHide = hideButtonRules[status] || [];
            return !buttonsToHide.includes(buttonType);
        },

        // Fungsi untuk memuat ulang data layanan
        async loadServices() {
            try {
                window.location.reload();
            } catch (error) {
                console.error("Error:", error);
            }
        },
    },
};
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold mb-4">Inventory</h1>
        <table class="table-auto w-full border-collapse border border-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">No</th>
                    <th class="border px-4 py-2">Nomer hak</th>
                    <th class="border px-4 py-2">Jenis Hak</th>
                    <th class="border px-4 py-2">Desa - Kecamatan</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(service, index) in services" :key="service.id">
                    <td class="border px-4 py-2">{{ index + 1 }}</td>
                    <td class="border px-4 py-2">
                        {{ service.land_book?.nomer_hak || "-" }}
                    </td>
                    <td class="border px-4 py-2">
                        {{ service.land_book?.jenis_hak || "-" }}
                    </td>
                    <td class="border px-4 py-2">
                        {{ service.land_book?.desa_kecamatan || "-" }}
                    </td>
                    <td class="border px-4 py-2">{{ service.status }}</td>
                    <td class="border px-4 py-2">
                        <div v-if="buttons[user.unit]">
                            <button
                                v-for="button in buttons[user.unit]"
                                :key="button"
                                v-show="isButtonVisible(service, button)"
                                class="bg-blue-500 text-white px-2 py-1 rounded mr-2"
                                @click="updateStatus(service.id, button)"
                            >
                                {{ button }}
                            </button>
                        </div>
                        <div v-else>
                            <span class="text-gray-500"
                                >No actions available</span
                            >
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
