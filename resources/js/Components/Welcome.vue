<script>
import { ref } from "vue";
import axios from "axios";
import { useForm } from "@inertiajs/inertia-vue3";
// Fungsi dideklarasikan di sini, di luar `setup`
const parseFlatArrayToObjects = (flatArray) => {
    const result = [];
    for (let i = 0; i < flatArray.length; i += 7) {
        result.push({
            nomer_hak: flatArray[i],
            jenis_hak: flatArray[i + 1],
            desa_kecamatan: flatArray[i + 2],
            user_name: flatArray[i + 3],
            activity_status: flatArray[i + 4],
            service_name: flatArray[i + 5],
            service_contact: flatArray[i + 6],
        });
    }
    return result;
};
export default {
    setup() {
        const form = useForm({
            desa_kecamatan: "",
            jenis_hak: "",
            nomer_hak: "",
        });

        const resultData = ref([]);
        const showResult = ref(false);
        const loading = ref(false);
        const errorMessage = ref("");

        const submitForm = async () => {
            errorMessage.value = "";
            loading.value = true;

            try {
                const response = await axios.get("/activities/fetch", {
                    params: {
                        desa_kecamatan: form.desa_kecamatan,
                        jenis_hak: form.jenis_hak,
                        nomer_hak: form.nomer_hak,
                    },
                });

                // Gunakan fungsi untuk memproses data flat menjadi objek
                resultData.value = parseFlatArrayToObjects(response.data);
                showResult.value = true;
            } catch (error) {
                console.error("Error fetching data:", error);
                errorMessage.value = "Data tidak dapat diambil. Silakan coba lagi.";
            } finally {
                loading.value = false;
            }
        };

        return {
            form,
            resultData,
            showResult,
            loading,
            errorMessage,
            submitForm,
        };
    },
};
 
</script>

<template>
    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-medium text-gray-900 dark:text-white mb-6">
            Selamat Datang di Aplikasi Genggamtanahku!
        </h1>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-4">
            <div>
                <label for="desa_kecamatan" class="block text-sm font-medium text-gray-900 dark:text-white">
                    Desa/Kecamatan
                </label>
                <input
                    type="text"
                    id="desa_kecamatan"
                    v-model="form.desa_kecamatan"
                    placeholder="Masukkan Desa/Kecamatan..."
                    class="mt-1 block w-full border border-gray-300 rounded p-2"
                    required
                />
            </div>

            <div>
                <label for="jenis_hak" class="block text-sm font-medium text-gray-900 dark:text-white">
                    Jenis Hak
                </label>
                <select
                    id="jenis_hak"
                    v-model="form.jenis_hak"
                    class="mt-1 block w-full border border-gray-300 rounded p-2"
                    required
                >
                    <option value="" disabled>Pilih Jenis Hak</option>
                    <option value="HGB">HGB</option>
                    <option value="HM">HM</option>
                    <option value="HP">HP</option>
                </select>
            </div>

            <div>
                <label for="nomer_hak" class="block text-sm font-medium text-gray-900 dark:text-white">
                    Nomer Hak
                </label>
                <input
                    type="text"
                    id="nomer_hak"
                    v-model="form.nomer_hak"
                    placeholder="Masukkan Nomer Hak..."
                    class="mt-1 block w-full border border-gray-300 rounded p-2"
                    required
                />
            </div>

            <button
                type="submit"
                class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                :disabled="loading"
            >
                {{ loading ? "Memuat..." : "Submit" }}
            </button>
        </form>

        <!-- Error Message -->
        <div v-if="errorMessage" class="text-red-500 mt-4">
            {{ errorMessage }}
        </div>

        <!-- Result Table -->
        <div v-if="showResult" class="mt-6 bg-gray-100 p-4 rounded shadow-md">
            <h2 class="text-xl font-medium text-gray-800 mb-4">Hasil Aktivitas</h2>

            <!-- Show if no data -->
            <div v-if="resultData.length === 0" class="text-center text-gray-500">
                Tidak ada data yang ditemukan untuk filter yang diberikan.
            </div>

            <!-- Data Table -->
            <div v-else class="overflow-x-auto">
                <table class="table-auto w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-left">No. Hak</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Jenis Hak</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Desa/Kecamatan</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Nama Pengguna</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Status Aktivitas</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Nama Layanan</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Kontak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(activity, index) in resultData"
                            :key="index"
                            class="odd:bg-white even:bg-gray-50"
                        >
                            <td class="border border-gray-300 px-4 py-2">{{ activity.nomer_hak }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ activity.jenis_hak }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ activity.desa_kecamatan }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ activity.user_name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ activity.activity_status }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ activity.service_name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ activity.service_contact }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
