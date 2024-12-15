<template>
    <AppLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-2xl font-semibold text-gray-900">
                    Daftar Berkas
                </h1>
            </div>

            <!-- Filter -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 my-4">
                <div class="flex flex-wrap gap-4">
                    <input
                        v-model="filters.nomer_hak"
                        placeholder="Filter Nomer Hak"
                        class="border border-gray-300 rounded px-4 py-2"
                    />
                    <input
                        v-model="filters.jenis_hak"
                        placeholder="Filter Jenis Hak"
                        class="border border-gray-300 rounded px-4 py-2"
                    />
                    <input
                        v-model="filters.desa_kecamatan"
                        placeholder="Filter Desa/Kecamatan"
                        class="border border-gray-300 rounded px-4 py-2"
                    />
                    <button
                        @click="applyFilter"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-500"
                    >
                        Apply Filter
                    </button>
                    <button
                        @click="resetFilter"
                        class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400"
                    >
                        Reset
                    </button>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <div class="mt-8 flex flex-col">
                    <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div
                            class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8"
                        >
                            <div
                                class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg"
                            >
                                <table
                                    class="min-w-full divide-y divide-gray-300"
                                >
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                            >
                                                No
                                            </th>
                                            <th
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                            >
                                                Nama Layanan
                                            </th>
                                            <th
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                            >
                                                Nomer Hak
                                            </th>
                                            <th
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                            >
                                                Jenis Hak
                                            </th>
                                            <th
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                            >
                                                Desa/Kecamatan
                                            </th>
                                            <th
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                            >
                                                Status
                                            </th>
                                            <th
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                            >
                                                PNBP
                                            </th>
                                            <th
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                            >
                                                Nomor HP
                                            </th>
                                            <th
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                            >
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="divide-y divide-gray-200 bg-white"
                                    >
                                        <tr
                                            v-for="(
                                                service, index
                                            ) in filteredServices"
                                            :key="service.id"
                                        >
                                            <td
                                                class="px-3 py-4 text-sm text-gray-500"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-3 py-4 text-sm text-gray-500"
                                            >
                                                {{ service.name || "N/A" }}
                                            </td>
                                            <td
                                                class="px-3 py-4 text-sm text-gray-500"
                                            >
                                                {{
                                                    service.land_book
                                                        ?.nomer_hak || "N/A"
                                                }}
                                            </td>
                                            <td
                                                class="px-3 py-4 text-sm text-gray-500"
                                            >
                                                {{
                                                    service.land_book
                                                        ?.jenis_hak || "N/A"
                                                }}
                                            </td>
                                            <td
                                                class="px-3 py-4 text-sm text-gray-500"
                                            >
                                                {{
                                                    service.land_book
                                                        ?.desa_kecamatan ||
                                                    "N/A"
                                                }}
                                            </td>
                                            <td
                                                class="px-3 py-4 text-sm text-gray-500"
                                            >
                                                {{ service.status }}
                                            </td>
                                            <td
                                                class="px-3 py-4 text-sm text-gray-500"
                                            >
                                                {{ service.PNBP || "N/A" }}
                                            </td>
                                            <td
                                                class="px-3 py-4 text-sm text-gray-500"
                                            >
                                                {{ service.nomor_hp || "N/A" }}
                                            </td>
                                            <td
                                                class="px-3 py-4 text-sm text-gray-500"
                                            >
                                                <button
                                                    @click="
                                                        openUpdateModal(service)
                                                    "
                                                    class="inline-flex items-center rounded-md bg-blue-600 px-2.5 py-1.5 text-sm font-semibold text-white hover:bg-blue-500"
                                                >
                                                    Update
                                                </button>
                                            </td>
                                        </tr>
                                        <tr
                                            v-if="filteredServices.length === 0"
                                        >
                                            <td
                                                colspan="9"
                                                class="px-3 py-4 text-sm text-gray-500 text-center"
                                            >
                                                Tidak ada data yang ditemukan.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Modal -->
            <UpdateModal
                :show="showUpdateModal"
                :service-id="selectedService?.id"
                :service="selectedService"
                :land-book="selectedService?.land_book"
                @close="closeUpdateModal"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import UpdateModal from "@/Components/UpdateModal.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

// Props
const props = defineProps({
    services: {
        type: Array,
        required: true,
        default: () => [],
    },
});

// State
const showUpdateModal = ref(false);
const selectedService = ref(null);
const filters = ref({
    nomer_hak: "",
    jenis_hak: "",
    desa_kecamatan: "",
});

// Filtered services
const filteredServices = computed(() => {
    return props.services.filter((service) => {
        return (
            (!filters.value.nomer_hak ||
                service.land_book?.nomer_hak?.includes(
                    filters.value.nomer_hak
                )) &&
            (!filters.value.jenis_hak ||
                service.land_book?.jenis_hak?.includes(
                    filters.value.jenis_hak
                )) &&
            (!filters.value.desa_kecamatan ||
                service.land_book?.desa_kecamatan?.includes(
                    filters.value.desa_kecamatan
                ))
        );
    });
});

// Methods
const openUpdateModal = (service) => {
    selectedService.value = service;
    showUpdateModal.value = true;
};

const closeUpdateModal = () => {
    selectedService.value = null;
    showUpdateModal.value = false;
};

const applyFilter = () => {
    // Filter applied automatically by computed property
};

const resetFilter = () => {
    filters.value = {
        nomer_hak: "",
        jenis_hak: "",
        desa_kecamatan: "",
    };
};
</script>
