<script setup>
import { ref } from 'vue';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    dataProses: Array,// Mendapatkan dataProses dari Laravel
    countProses: Number,
    title: String,
    subtitle: String,
    atEmpty: String
});

// Menyimpan data yang akan ditampilkan pada halaman aktif
const currentPage = ref(1);
const itemsPerPage = ref(10);

// Menghitung data yang akan ditampilkan berdasarkan halaman aktif
const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return props.dataProses.slice(start, end);
});

// Menyusun pagination untuk jumlah halaman
const pageCount = computed(() => {
    return Math.ceil(props.dataProses.length / itemsPerPage.value);
});

// Fungsi untuk pindah ke halaman berikutnya dan sebelumnya
const nextPage = () => {
    if (currentPage.value < pageCount.value) {
        currentPage.value++;
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};
</script>

<template>
    <AppLayout title="Data Proses">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 dark:text-white">
            <div class="md:grid md:grid-cols-3 md:gap-6 px-4 sm:px-6 lg:px-8 gap-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ title }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ subtitle }}
                    </p>
                    <p class="mt-1 text-5xl text-blue-600 dark:text-blue-400">
                        {{ countProses }}
                    </p>
                </div>
                <div class="col-span-2">
                    <div class="bg-white dark:bg-gray-800 px-4 sm:px-6 lg:px-8 py-4 rounded-t-xl">
                        <table class="table-auto w-full">
                            <thead>
                                <tr>
                                    <th>Service ID</th>
                                    <th>Status</th>
                                    <th>Land Book Number</th>
                                    <th>Jenis Hak</th>
                                    <th>Desa/Kecamatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="service in paginatedData" :key="service.id">
                                    <td>{{ service.id }}</td>
                                    <td>{{ service.status }}</td>
                                    <td v-if="service.land_book">{{ service.land_book.nomer_hak }}</td>
                                    <td v-if="service.land_book">{{ service.land_book.jenis_hak }}</td>
                                    <td v-if="service.land_book">{{ service.land_book.desa_kecamatan }}</td>
                                </tr>
                                <tr v-if="dataProses.length === 0">
                                    <td colspan="5" style="text-align: center">{{ atEmpty }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        class="flex justify-between bg-gray-50 dark:bg-gray-800 px-4 sm:px-6 lg:px-8 py-4 rounded-b-xl">
                        <button @click="prevPage" :disabled="currentPage === 1">
                            Previous
                        </button>
                        <span>Page {{ currentPage }} of {{ pageCount }}</span>
                        <button @click="nextPage" :disabled="currentPage === pageCount">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Menambahkan styling untuk tabel */
table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    padding: 8px;
    border: 1px solid #ddd;
    text-align: left;
}
</style>
