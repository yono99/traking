<script setup>
import { reactive, ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import AppLayout from "@/Layouts/AppLayout.vue";
import Users from "@/Components/Users.vue";

defineProps({});

defineExpose({});

const form = reactive({
    email: "",
    unit: "",
    role: "",
});

const message = ref("");

function updateAccount() {
    Inertia.post("/management-akun/update", form, {
        onSuccess: () => {
            message.value = "Akun berhasil diperbarui!";
        },
        onError: () => {
            message.value = "Terjadi kesalahan, periksa kembali form!";
        },
    });
}
</script>

<template>
    <AppLayout title="Management Account">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 dark:text-white">
            <form @submit.prevent="updateAccount">
                <div class="md:grid md:grid-cols-3 md:gap-6 px-4 sm:px-6 lg:px-8 gap-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Management Account
                        </h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Manage the Roles of our member accounts
                        </p>
                        <div v-if="message" class="mt-1 text-sm text-green-600 dark:text-green-400">
                            <i class="underline">{{ message }}</i>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <div class="bg-white dark:bg-gray-800 px-4 sm:px-6 lg:px-8 py-4 rounded-t-xl space-y-4">
                            <!-- Email Input -->
                            <div>
                                <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                    Email
                                </label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    id="email"
                                    required
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                />
                            </div>

                            <!-- Unit Selection -->
                            <div>
                                <label for="unit" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                    Unit
                                </label>
                                <select
                                    v-model="form.unit"
                                    id="unit"
                                    required
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                >
                                    <option value="" disabled selected>Pilih Unit</option>
                                    <option value="umum">Umum</option>
                                    <option value="loket">loket</option>
                                    <option value="verifikator">verifikator</option>
                                    <option value="bukutanah">Buku tanah</option>
                                    <option value="sps">SPS</option>
                                    <option value="pengukuran">pengukuran</option>
                                    <option value="pengesahan">Pengesahan</option>
                                    <option value="bensus">bensus</option>
                                    <option value="pelaksana">pelaksana</option>
                                    <option value="pelaksana_bn">pelaksana bn</option>
                                    <option value="pelaksana_ph">pelaksana ph</option>
                                    <option value="pelaksana_roya">pelaksana roya</option>
                                    <option value="pelaksana_ph_ruko">pelaksana ph ruko</option>
                                    <option value="pelaksana_sk">pelaksana sk</option>
                                    <option value="TTE_PRODUK_LAYANAN">TTE Produk Layanan</option>
                                    <option value="LOKET_PENYERAHAN">Loket Penyerahan</option>
                                </select>
                            </div>

                            <!-- Role Selection -->
                            <div>
                                <label for="role" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                    Role
                                </label>
                                <select
                                    v-model="form.role"
                                    id="role"
                                    required
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                >
                                    <option value="" disabled selected>Pilih Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="umum">Umum</option>
                                </select>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end bg-gray-50 dark:bg-gray-800 px-4 sm:px-6 lg:px-8 py-4 rounded-b-xl">
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Update
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Add Users Component -->
            <Users />
        </div>
    </AppLayout>
</template>

<style scoped>
/* Custom Styles */
form div {
    margin-bottom: 15px;
}
</style>
