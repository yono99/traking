<template>
    <div class="p-5 max-w-7xl mx-auto">
        <h1 class="text-2xl font-semibold mb-4 dark:text-white">SMART Research</h1>

        <!-- Input Pencarian -->
        <div class="flex mb-4">
            <input type="text" v-model="nomorHak" placeholder="Masukkan Nomer Hak" @keyup.enter="search"
                class="border border-gray-300 p-2 rounded-l-md w-64 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700" />
            <button @click="search"
                class="bg-blue-500 text-white px-4 py-2 rounded-r-md hover:bg-blue-600 dark:bg-blue-600  ">
                Cari
            </button>
        </div>

        <!-- Tabel hasil pencarian -->
        <div class="mt-8 flex flex-col" v-if="services.length >= 0">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">No</th>
                                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nomer Hak</th>
                                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Jenis Hak</th>
                                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Desa/Kecamatan
                                    </th>
                                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="(service, index) in services" :key="service.id">
                                    <td class="px-3 py-4 text-sm text-gray-500">{{ index + 1 }}</td>
                                    <td class="px-3 py-4 text-sm text-gray-500">{{ service.land_book?.nomer_hak || 'N/A'
                                        }}</td>
                                    <td class="px-3 py-4 text-sm text-gray-500">{{ service.land_book?.jenis_hak || 'N/A'
                                        }}</td>
                                    <td class="px-3 py-4 text-sm text-gray-500">{{ service.land_book?.desa_kecamatan ||
                                        'N/A' }}</td>
                                    <td class="px-3 py-4 text-sm text-gray-500">
                                        <!-- Tombol Update -->
                                        <button @click="updateStatus(service.id)"
                                            class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                                            Update Status
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="(services.length === 0)">
                                    <td colspan="5" class="px-3 py-4 text-sm text-gray-500 text-center">
                                        Tidak ada data yang ditemukan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pesan error -->
        <div v-if="errorMessage" class="mt-4 text-red-500 font-semibold">
            {{ errorMessage }}
        </div>
    </div>
</template>

<script>
import { ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
    layout: AppLayout,
    setup() {
        const nomorHak = ref("");
        const services = ref([]);
        const errorMessage = ref("");

        const search = () => {
            services.value = [];
            errorMessage.value = "";

            fetch(`/search?nomer_hak=${encodeURIComponent(nomorHak.value)}`)
                .then((response) => {
                    if (!response.ok)
                        throw new Error(
                            `HTTP error! status: ${response.status}`
                        );
                    return response.json();
                })
                .then((data) => {
                    console.log("Data diterima:", data); // Debugging
                    services.value = data.services || [];
                })
                .catch((error) => {
                    console.error("Error saat mencari data:", error);
                    errorMessage.value =
                        "Terjadi kesalahan saat mencari data. Silakan coba lagi.";
                });
        };

        const updateStatus = (serviceId) => {
            if (!serviceId) {
                errorMessage.value = "Service ID tidak valid.";
                return;
            }

            fetch(`/update-status`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: JSON.stringify({
                    service_id: serviceId,
                    status: "UPDATED",
                }),
            })
                .then((response) => {
                    if (!response.ok)
                        throw new Error("Gagal memperbarui status.");
                    return response.json();
                })
                .then((data) => {
                    alert(data.message);
                    search(); // Refresh data
                })
                .catch((error) => {
                    console.error("Error saat memperbarui status:", error);
                    errorMessage.value =
                        "Terjadi kesalahan saat memperbarui status.";
                });
        };

        return {
            nomorHak,
            services,
            errorMessage,
            search,
            updateStatus,
        };
    },
};
</script>

<style scoped>
.search-container {
    padding: 20px;
}

.result-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-family: Arial, sans-serif;
}

.result-table th {
    background-color: #007bff;
    color: white;
    padding: 10px;
    text-align: left;
}

.result-table td {
    border: 1px solid #ddd;
    padding: 10px;
}

.result-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.result-table tr:hover {
    background-color: #f1f1f1;
}

.btn-update {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 4px;
    font-size: 14px;
}

.btn-update:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.btn-update:hover:not(:disabled) {
    background-color: #218838;
}
</style>
