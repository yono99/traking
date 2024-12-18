<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    dataProses: Array, // Mendapatkan dataProses dari Laravel
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
                    <div class="bg-white dark:bg-gray-800 px-4 sm:px-6 lg:px-8 py-4 rounded-t-xl shadow-md">
                        <table class="table-auto w-full border-collapse border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 border border-gray-200 dark:border-gray-600">No</th>
                                    <th class="px-4 py-2 border border-gray-200 dark:border-gray-600">Status</th>
                                    <th class="px-4 py-2 border border-gray-200 dark:border-gray-600">Nomer Hak</th>
                                    <th class="px-4 py-2 border border-gray-200 dark:border-gray-600">Jenis Hak</th>
                                    <th class="px-4 py-2 border border-gray-200 dark:border-gray-600">Desa/Kecamatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(service, index) in paginatedData" :key="service.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-4 py-2 border border-gray-200 dark:border-gray-600 text-center">{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
                                    <td class="px-4 py-2 border border-gray-200 dark:border-gray-600">{{ service.status }}</td>
                                    <td class="px-4 py-2 border border-gray-200 dark:border-gray-600" v-if="service.land_book">{{ service.land_book.nomer_hak }}</td>
                                    <td class="px-4 py-2 border border-gray-200 dark:border-gray-600" v-if="service.land_book">{{ service.land_book.jenis_hak }}</td>
                                    <td class="px-4 py-2 border border-gray-200 dark:border-gray-600" v-if="service.land_book">{{ service.land_book.desa_kecamatan }}</td>
                                </tr>
                                <tr v-if="props.dataProses.length === 0">
                                    <td colspan="5" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">{{ atEmpty }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-800 px-4 sm:px-6 lg:px-8 py-4 rounded-b-xl shadow-md">
                        <button @click="prevPage" :disabled="currentPage === 1" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50">
                            Previous
                        </button>
                        <span class="text-gray-700 dark:text-gray-300">Page {{ currentPage }} of {{ pageCount }}</span>
                        <button @click="nextPage" :disabled="currentPage === pageCount" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}
th, td {
    padding: 10px;
    border: 1px solid #ddd;
}
</style>