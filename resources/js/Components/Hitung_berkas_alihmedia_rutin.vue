<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const countAlihMedia = ref(0)
const countRutin = ref(0)

const fetchBerkasCounts = async () => {
    try {
        // Gunakan URL yang benar sesuai dengan route yang berhasil
        const response = await axios.get('/hitung-berkas-alihmedia-rutin')
        console.log('Response:', response.data) // Debugging
        countAlihMedia.value = response.data.jumlah_status_alihmedia
        countRutin.value = response.data.jumlah_status_rutin
    } catch (error) {
        console.error("Error fetching berkas counts:", error)
    }
}

onMounted(() => {
    fetchBerkasCounts()
})
</script>

<template>
    <div class="flex justify-center py-4">
        <div class="grid grid-cols-2 gap-4 w-full max-w-7xl px-4">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg flex items-center w-full text-xl">
                <div class="font-semibold dark:text-white text-gray-800 flex-1">
                    Jumlah Selesai Alih Media:
                </div>
                <div class="text-xl text-blue-500 dark:text-blue-400 font-medium">
                    {{ countAlihMedia }}
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg flex items-center w-full text-xl">
                <div class="font-semibold dark:text-white text-gray-800 flex-1">
                    Jumlah Selesai Rutin:
                </div>
                <div class="text-xl text-blue-500 dark:text-blue-400 font-medium">
                    {{ countRutin }}
                </div>
            </div>
        </div>
    </div>
</template>