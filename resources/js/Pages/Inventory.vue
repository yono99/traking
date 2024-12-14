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
            // Salinan lokal dari prop 'services' untuk diubah
            localServices: [...this.services],

            buttons: {
                verifikator: [
                    "FORWARD PENGUKURAN",
                    "FORWARD CARI BT",
                    "FORWARD BENSUS DISPOSISI",
                    "FORWARD SPS",
                ],
                sps: ["FORWARD BENSUS"],
                bensus: [
                    "FORWARD PELAKSANA",
                    "SELESAI INFO DISPOSISI",
                    
                ],
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
                    "FORWARD VERIFIKATOR",
                    "FORWARD ALIH MEDIA BTEL",
                    
                ],
                bukutanah: [
                    "FORWARD VERIFIKATOR CEK SYARAT",
                    "FORWARD PENGESAHAN ALIH MEDIA BTEL",
                   
                ],
                pengesahan: ["FORWARD PARAF"],
                paraf: ["FORWARD TTE PRODUK LAYANAN"],
                TTE_PRODUK_LAYANAN: ["FORWARD PELAKSANA CEKTAK SERTEL"],
                LOKET_PENYERAHAN: ["SELESAI DISERAHKAN"],
            },
        };
    },

    methods: {
        // Fungsi untuk memperbarui status layanan dan memuat ulang data
        async updateStatus(serviceId, newStatus) {
            try {
                const response = await axios.post(
                    `/inventory/update-status/${serviceId}`,
                    { status: newStatus }
                );
                alert(response.data.message); // Tampilkan pesan sukses

                // Perbarui status layanan di salinan lokal
                const service = this.localServices.find(s => s.id === serviceId);
                if (service) {
                    service.status = newStatus;
                }

                // Panggil API untuk memuat ulang data setelah pembaruan status
                await this.loadServices();

            } catch (error) {
                console.error(error);
                const errorMessage = error.response?.data?.message || "Terjadi kesalahan saat memperbarui status";
                alert(errorMessage);
            }
        },

        // Fungsi untuk memuat ulang data layanan dari backend
        async loadServices() {
            try {
                const response = await axios.get("/inventory");
                // Update salinan lokal dari 'services' dengan data terbaru
                this.localServices = response.data.services;
            } catch (error) {
                console.error(error);
                alert("Terjadi kesalahan saat memuat data layanan");
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
                    <td class="border px-4 py-2">
                        {{ service.status }}
                    </td>
                    <td class="border px-4 py-2">
                        <div v-if="buttons[user.unit]">
                            <button
                                v-for="button in buttons[user.unit]"
                                :key="button"
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
