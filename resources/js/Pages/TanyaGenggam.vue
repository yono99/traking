<template>
    <div class="search-container">
        <h1>Pencarian Tanya Genggam</h1>
        <input
            type="text"
            v-model="nomorHak"
            placeholder="Masukkan Nomer Hak"
            @keyup.enter="search"
        />
        <button @click="search">Cari</button>

        <!-- Tabel hasil pencarian -->
        <table v-if="services.length" class="result-table">
            <thead>
                <tr>
                    <th>Nomer Hak</th>
                    <th>Jenis Hak</th>
                    <th>Desa/Kecamatan</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="service in services" :key="service.id">
                    <!-- <td>{{ service.id }}</td> -->
                    <td>{{ service.land_book?.nomer_hak || "N/A" }}</td>
                    <td>{{ service.land_book?.jenis_hak || "N/A" }}</td>
                    <td>{{ service.land_book?.desa_kecamatan || "N/A" }}</td>
                    <!-- <td>{{ service.land_book?.status_alih_media === 0 ? 'Belum Alih Media' : 'Sudah Alih Media' }}</td>
                    <td>{{ service.PNBP || 'N/A' }}</td> -->
                    <td>
                        <!-- Tombol Update -->
                        <button
                            @click="updateStatus(service.id)"
                            class="btn-update"
                        >
                            Update Status
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div v-if="errorMessage" class="error-message">
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
                    //    console.log("Data diterima:", data); // Debugging
                    services.value = data.services || [];
                })
                .catch((error) => {
                    console.error("Error saat mencari data:", error);
                    errorMessage.value =
                        "Terjadi kesalahan saat mencari data. Silakan coba lagi.";
                });
        };

        const updateStatus = async (serviceId) => {
            if (!serviceId) {
                errorMessage.value = "Service ID tidak valid.";
                return;
            }

            try {
                const csrfToken = document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content");

                if (!csrfToken) {
                    throw new Error("CSRF token tidak ditemukan.");
                }

                const response = await fetch(`/update-status`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({
                        service_id: serviceId,
                        status: "UPDATED",
                    }),
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(
                        errorData.message || "Gagal memperbarui status."
                    );
                }

                const data = await response.json();
                alert(data.message || "Status berhasil diperbarui.");
                search(); // Refresh data
            } catch (error) {
                console.error("Error saat memperbarui status:", error);
                errorMessage.value =
                    error.message ||
                    "Terjadi kesalahan saat memperbarui status.";
            }
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
