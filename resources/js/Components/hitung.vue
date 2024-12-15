<script setup>
import axios from "axios";
import ProsesLink from "@/Components/proseslink.vue";  // Komponen ProsesLink

// Mendeklarasikan reactive data
import { ref, onMounted } from 'vue';

const countSelesaiTTE = ref(0);
const countProses = ref(0);

// Mengambil data counts saat komponen dimounting
onMounted(() => {
    fetchServiceCounts();
});

// Fungsi untuk mengambil data
async function fetchServiceCounts() {
    try {
        const response = await axios.get('/api/services/count');
        countSelesaiTTE.value = response.data.countSelesaiTTE;
        countProses.value = response.data.countProses;
    } catch (error) {
        console.error("Error fetching counts:", error);
    }
}
</script>

<template>
    <div class="flex justify-center py-4">
        <!-- Bagian Status Layanan -->
        <div class="grid sm:grid-cols-2 gap-4 w-full max-w-7xl px-4 ">
            <!-- total proses -->
            <ProsesLink :href="route('total-proses')">
                <div class="font-semibold dark:text-white text-gray-800 flex-1">Total Proses:</div>
                <div class="flex items-center gap-2">
                    <div class="text-xl text-blue-500 dark:text-blue-400 font-medium">{{ countProses }}</div>
                    <!-- Icon Panah -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 dark:text-blue-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </ProsesLink>

            <!-- total selesai tte -->
            <ProsesLink :href="route('total-proses-tte')"
                class="bg-white dark:bg-gray-800 p-4 rounded-lg flex items-center w-full text-xl cursor-pointer transition-shadow shadow-md hover:shadow-lg">
                <div class="font-semibold dark:text-white text-gray-800 flex-1">Total Selesai TTE:</div>
                <div class="flex items-center gap-2">
                    <div class="text-xl text-blue-500 dark:text-blue-400 font-medium">{{ countSelesaiTTE }}</div>
                    <!-- Icon Panah -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 dark:text-blue-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </ProsesLink>
        </div>
    </div>
</template>
