<template>
    <div class="search-container">
        <input type="text" v-model="nomerHak" placeholder="Masukkan Nomer Hak" @keyup.enter="search" />
        <button @click="search">Cari</button>

        <!-- Tabel untuk menampilkan hasil -->
        <table v-if="landBooks.length || services.length" class="result-table">
            <thead>
                <tr>
                    <th>Nomer Hak</th>
                    <th>Desa/Kecamatan</th>
                    <th>Nama Service</th>
                    <th>Keterangan</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(landBook, index) in landBooks" :key="index">
                    <td>{{ landBook.nomer_hak }}</td>
                    <td>{{ landBook.desa_kecamatan }}</td>
                    <td>{{ services[index]?.name || "N/A" }}</td>
                    <td>{{ services[index]?.remarks || "N/A" }}</td>
                    <td>
                        <button @click="updateStatus(services[index]?.id)" :disabled="!services[index]?.id">
                            Update Status
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div v-if="errorMessage" class="error-message">
            {{ errorMessage }}
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="pagination">
            <button @click="prevPage" :disabled="currentPage === 1">Previous</button>
            <span>Page {{ currentPage }} of {{ totalPages }}</span>
            <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
        </div>
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref } from "vue";

export default {
    layout: AppLayout,

    setup() {
        const nomerHak = ref("");
        const landBooks = ref([]);
        const services = ref([]);
        const errorMessage = ref("");
        const currentPage = ref(1);
        const itemsPerPage = ref(10);
        const totalItems = ref(0);
        const totalPages = ref(0);

        const search = () => {
            fetch(`/search?nomer_hak=${encodeURIComponent(nomerHak.value)}&page=${currentPage.value}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then((data) => {
                    landBooks.value = data.landBooks || [];
                    services.value = data.services || [];
                    totalItems.value = data.totalItems; // Total items from the response
                    totalPages.value = Math.ceil(totalItems.value / itemsPerPage.value);
                })
                .catch((error) => {
                    console.error("Error saat mencari data:", error);
                    errorMessage.value = "Terjadi kesalahan saat mencari data. Silakan coba lagi.";
                });
        };

        const updateStatus = (serviceId) => {
            if (!serviceId) {
                errorMessage.value = "Service ID tidak valid.";
                return;
            }

            const userUnit = "bensus"; // Ganti dengan logika untuk mendapatkan unit pengguna saat ini
            const statusUpdate = getUpdateStatusByUnit(userUnit);
            // console.log(statusUpdate, serviceId);

            fetch(`/update-status`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ service_id: serviceId, status: statusUpdate }),
            })
                .then((response) => {
                    if (!response.ok) {
                        return response.text().then((text) => {
                            console.error("Backend response:", text);
                            throw new Error(`Error: ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then((data) => {
                    alert(data.message);
                    search(); // Refresh data
                })
                .catch((error) => {
                    console.error("Error saat memperbarui status:", error);
                    errorMessage.value = error.message || "Terjadi kesalahan saat memperbarui status.";
                });
        };

        const getUpdateStatusByUnit = (unit) => {
            switch (unit) {
                case 'verifikator':
                    return 'UPDATE PROSES VERIFIKASI';
                case 'pengukuran':
                    return 'UPDATE PROSES MEMPERBAHARUI';
                case 'bukutanah':
                    return 'UPDATE PROSES ALIH MEDIA BTEL';
                case 'sps':
                    return 'UPDATE PROSES SPS';
                case 'bensus':
                    return 'UPDATE PROSES BENSUS';
                case 'QC':
                    return 'UPDATE PROSES QC';
                case 'pengesahan':
                    return 'UPDATE PROSES PENGESAHAN ALIH MEDIA BTEL';
                case 'paraf':
                    return 'UPDATE PROSES PARAF';
                case 'TTE_PRODUK_LAYANAN':
                    return 'UPDATE PROSES TTE';
                default:
                    return null;
            }
        };

        const nextPage = () => {
            if (currentPage.value < totalPages.value) {
                currentPage.value++;
                search();
            }
        };

        const prevPage = () => {
            if (currentPage.value > 1) {
                currentPage.value--;
                search();
            }
        };

        return {
            nomerHak,
            landBooks,
            services,
            errorMessage,
            currentPage,
            totalPages,
            search,
            updateStatus,
            nextPage,
            prevPage,
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
}

.result-table th,
.result-table td {
    border: 1px solid #ddd;
    padding: 8px;
}

.result-table th {
    background-color: #f4f4f4;
}

.error-message {
    color: red;
    margin-top: 10px;
}

.pagination {
    margin-top: 20px;
}
</style>
