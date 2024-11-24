<script>
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";

export default {
        layout: AppLayout,

    props: {
        activities: Array,
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
                bensus: [
                    "FORWARD ALIH MEDIA SUEL",
                    "FORWARD BENSUS DISPOSISI",
                    "UPDATE SELESAI",
                ],
                QC: [
                    "FORWARD PENGESAHAN ALIH MEDIA BTEL",
                    "FORWARD BUKU TANAH REVISI",
                    "FORWARD PENGUKURAN REVISI",
                ],
                pengukuran: [
                    "FORWARD VERIFIKATOR",
                    "FORWARD ALIH MEDIA BTEL",      
                    "FORWARD SELESAI REVISI",
                ],
                bukutanah: [
                    "FORWARD VERIFIKATOR CEK SYARAT",
                    "FORWARD QC SELESAI ALIH MEDIA",
                    "FORWARD SELESAI REVISI",
                ],
                pengesahan: ["FORWARD PARAF"],
                paraf: ["FORWARD TTE PRODUK LAYANAN"],
                TTE_PRODUK_LAYANAN: ["SELESAI TTE"],
            },
        };
    },
    methods: {
        async updateStatus(serviceId, newStatus) {
            try {
                const response = await axios.post(
                    `/inventory/update-status/${serviceId}`,
                    {
                        status: newStatus,
                    }
                );
                alert(response.data.message); // Tampilkan pesan sukses
                location.reload(); // Refresh data di halaman
            } catch (error) {
                console.error(error);
                alert("Terjadi kesalahan saat memperbarui status");
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
                <tr v-for="(activity, index) in activities" :key="activity.id">
                    <td class="border px-4 py-2">{{ index + 1 }}</td>
                    <td class="border px-4 py-2">
                        {{ activity.service.land_book?.nomer_hak || "-" }}
                    </td>
                    <td class="border px-4 py-2">
                        {{ activity.service.land_book?.jenis_hak || "-" }}
                    </td>
                    <td class="border px-4 py-2">
                        {{ activity.service.land_book?.desa_kecamatan || "-" }}
                    </td>
                    <td class="border px-4 py-2">
                        {{ activity.service.status }}
                    </td>
                    <td class="border px-4 py-2">
                        <div v-if="buttons[activity.user.unit]">
                            <button
                                v-for="button in buttons[activity.user.unit]"
                                :key="button"
                                class="bg-blue-500 text-white px-2 py-1 rounded mr-2"
                                @click="
                                    updateStatus(activity.service.id, button)
                                "
                            >
                                {{ button }}
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

