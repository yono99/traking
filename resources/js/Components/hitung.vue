<template>
    <div class="flex justify-center py-4">
        <!-- Bagian Status Layanan -->
        <div class="grid grid-cols-2 gap-4 w-full max-w-7xl px-4 ">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg flex items-center w-full text-xl">
                <div class="font-semibold dark:text-white text-gray-800 flex-1">Total Proses:</div>
                <div class="text-xl text-blue-500 dark:text-blue-400 font-medium">{{ countProses }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg flex items-center w-full text-xl">
                <div class="font-semibold dark:text-white text-gray-800 flex-1">Total Selesai TTE:</div>
                <div class="text-xl text-blue-500 dark:text-blue-400 font-medium">{{ countSelesaiTTE }}</div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            countSelesaiTTE: 0,
            countProses: 0,
        };
    },
    mounted() {
        this.fetchServiceCounts();
    },
    methods: {
        async fetchServiceCounts() {
            try {
                const response = await axios.get('/api/services/count');
                this.countSelesaiTTE = response.data.countSelesaiTTE;
                this.countProses = response.data.countProses;
            } catch (error) {
                console.error("Error fetching counts:", error);
            }
        },
    },
};
</script>
